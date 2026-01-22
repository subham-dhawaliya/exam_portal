<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FaceScanLockService
{
    const LOCK_PREFIX = 'face_scan_lock:';
    const LOCK_TIMEOUT = 120; // 2 minutes max scan time
    
    /**
     * Start a new scan session and acquire lock
     */
    public function startScan(string $deviceId, string $scanType = 'registration'): array
    {
        $lockKey = self::LOCK_PREFIX . $deviceId;
        $scanToken = Str::uuid()->toString();
        
        // Check if there's already an active scan for this device
        $existingLock = Cache::get($lockKey);
        if ($existingLock && !$this->isExpired($existingLock)) {
            return [
                'success' => false,
                'message' => 'A scan is already in progress on this device',
                'existing_token' => $existingLock['token'] ?? null,
            ];
        }
        
        // Create new lock
        $lockData = [
            'token' => $scanToken,
            'device_id' => $deviceId,
            'scan_type' => $scanType,
            'started_at' => now()->timestamp,
            'expires_at' => now()->addSeconds(self::LOCK_TIMEOUT)->timestamp,
            'directions_completed' => [],
            'status' => 'scanning',
        ];
        
        // Use atomic lock to prevent race conditions
        $acquired = Cache::lock($lockKey . ':mutex', 10)->get(function () use ($lockKey, $lockData) {
            Cache::put($lockKey, $lockData, self::LOCK_TIMEOUT);
            return true;
        });
        
        if (!$acquired) {
            return [
                'success' => false,
                'message' => 'Could not acquire scan lock. Please try again.',
            ];
        }
        
        return [
            'success' => true,
            'token' => $scanToken,
            'expires_at' => $lockData['expires_at'],
            'message' => 'Scan session started',
        ];
    }
    
    /**
     * Update scan progress (directions completed)
     */
    public function updateProgress(string $deviceId, string $token, array $directions): array
    {
        $lockKey = self::LOCK_PREFIX . $deviceId;
        $lockData = Cache::get($lockKey);
        
        if (!$lockData) {
            return ['success' => false, 'message' => 'No active scan session'];
        }
        
        if ($lockData['token'] !== $token) {
            return ['success' => false, 'message' => 'Invalid scan token'];
        }
        
        if ($this->isExpired($lockData)) {
            $this->releaseLock($deviceId, $token);
            return ['success' => false, 'message' => 'Scan session expired'];
        }
        
        $lockData['directions_completed'] = $directions;
        $lockData['last_update'] = now()->timestamp;
        
        Cache::put($lockKey, $lockData, self::LOCK_TIMEOUT);
        
        return ['success' => true, 'message' => 'Progress updated'];
    }
    
    /**
     * Validate if capture is allowed (all directions done, valid token)
     */
    public function validateCapture(string $deviceId, string $token): array
    {
        $lockKey = self::LOCK_PREFIX . $deviceId;
        $lockData = Cache::get($lockKey);
        
        if (!$lockData) {
            return [
                'success' => false,
                'message' => 'No active scan session. Please start a new scan.',
                'code' => 'NO_SESSION',
            ];
        }
        
        if ($lockData['token'] !== $token) {
            return [
                'success' => false,
                'message' => 'Invalid scan token. Another scan may have started.',
                'code' => 'INVALID_TOKEN',
            ];
        }
        
        if ($this->isExpired($lockData)) {
            $this->releaseLock($deviceId, $token);
            return [
                'success' => false,
                'message' => 'Scan session expired. Please start a new scan.',
                'code' => 'EXPIRED',
            ];
        }
        
        // Check all directions completed
        $requiredDirections = ['left', 'right', 'up', 'down'];
        $completed = $lockData['directions_completed'] ?? [];
        $missing = array_diff($requiredDirections, $completed);
        
        if (!empty($missing)) {
            return [
                'success' => false,
                'message' => 'Please complete all directions before capture',
                'code' => 'INCOMPLETE',
                'missing' => $missing,
            ];
        }
        
        // Mark as ready for capture
        $lockData['status'] = 'ready_for_capture';
        Cache::put($lockKey, $lockData, 30); // 30 seconds to complete capture
        
        return [
            'success' => true,
            'message' => 'Capture authorized',
            'code' => 'AUTHORIZED',
        ];
    }
    
    /**
     * Complete the scan and release lock
     */
    public function completeScan(string $deviceId, string $token): array
    {
        $lockKey = self::LOCK_PREFIX . $deviceId;
        $lockData = Cache::get($lockKey);
        
        if (!$lockData || $lockData['token'] !== $token) {
            return ['success' => false, 'message' => 'Invalid session'];
        }
        
        // Release the lock
        Cache::forget($lockKey);
        
        return [
            'success' => true,
            'message' => 'Scan completed successfully',
        ];
    }
    
    /**
     * Abort/cancel the scan
     */
    public function abortScan(string $deviceId, string $token): array
    {
        return $this->releaseLock($deviceId, $token);
    }
    
    /**
     * Release lock
     */
    protected function releaseLock(string $deviceId, string $token): array
    {
        $lockKey = self::LOCK_PREFIX . $deviceId;
        $lockData = Cache::get($lockKey);
        
        if ($lockData && $lockData['token'] === $token) {
            Cache::forget($lockKey);
            return ['success' => true, 'message' => 'Lock released'];
        }
        
        return ['success' => false, 'message' => 'Could not release lock'];
    }
    
    /**
     * Check if lock is expired
     */
    protected function isExpired(array $lockData): bool
    {
        return now()->timestamp > ($lockData['expires_at'] ?? 0);
    }
    
    /**
     * Get current scan status
     */
    public function getScanStatus(string $deviceId): array
    {
        $lockKey = self::LOCK_PREFIX . $deviceId;
        $lockData = Cache::get($lockKey);
        
        if (!$lockData) {
            return ['active' => false];
        }
        
        if ($this->isExpired($lockData)) {
            Cache::forget($lockKey);
            return ['active' => false, 'expired' => true];
        }
        
        return [
            'active' => true,
            'status' => $lockData['status'],
            'directions_completed' => $lockData['directions_completed'] ?? [],
            'expires_in' => $lockData['expires_at'] - now()->timestamp,
        ];
    }
}
