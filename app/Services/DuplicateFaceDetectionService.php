<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class DuplicateFaceDetectionService
{
    /**
     * Similarity threshold - faces with distance below this are considered same person
     * Lower value = stricter matching (0.4-0.5 is typical for face-api.js)
     */
    const SIMILARITY_THRESHOLD = 0.45;

    /**
     * Check if a face descriptor matches any existing user
     * 
     * @param array $newDescriptor The face descriptor to check
     * @return array|null Returns matching user info if duplicate found, null otherwise
     */
    public function findDuplicateFace(array $newDescriptor): ?array
    {
        // Get all users with face descriptors
        $users = User::whereNotNull('face_descriptor')
            ->where('status', 'active')
            ->get(['id', 'name', 'email', 'face_descriptor', 'created_at']);

        $bestMatch = null;
        $lowestDistance = PHP_FLOAT_MAX;

        foreach ($users as $user) {
            $existingDescriptor = $user->face_descriptor;
            
            if (!is_array($existingDescriptor) || count($existingDescriptor) !== 128) {
                continue;
            }

            $distance = $this->calculateEuclideanDistance($newDescriptor, $existingDescriptor);
            
            Log::debug("Face comparison with user {$user->id}: distance = {$distance}");

            if ($distance < $lowestDistance) {
                $lowestDistance = $distance;
                $bestMatch = $user;
            }
        }

        // If best match is below threshold, it's a duplicate
        if ($bestMatch && $lowestDistance < self::SIMILARITY_THRESHOLD) {
            Log::warning("Duplicate face detected! Matches user ID: {$bestMatch->id}, distance: {$lowestDistance}");
            
            return [
                'is_duplicate' => true,
                'matched_user_id' => $bestMatch->id,
                'matched_user_name' => $bestMatch->name,
                'matched_user_email' => $this->maskEmail($bestMatch->email),
                'similarity_score' => round((1 - $lowestDistance) * 100, 1),
                'distance' => $lowestDistance,
            ];
        }

        return null;
    }

    /**
     * Calculate Euclidean distance between two face descriptors
     * Same algorithm as face-api.js uses
     */
    protected function calculateEuclideanDistance(array $descriptor1, array $descriptor2): float
    {
        if (count($descriptor1) !== count($descriptor2)) {
            return PHP_FLOAT_MAX;
        }

        $sum = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $diff = $descriptor1[$i] - $descriptor2[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    /**
     * Mask email for privacy (show only first 2 chars and domain)
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***@***.***';
        }

        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 2));
        
        return $maskedName . '@' . $domain;
    }
}
