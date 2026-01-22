<?php

namespace App\Services;

use App\Models\FaceEmbedding;
use App\Models\FaceVerificationLog;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaceRecognitionService
{
    // Match threshold (0-100) - higher means stricter matching
    const MATCH_THRESHOLD = 70;
    const LIVENESS_THRESHOLD = 60;
    const QUALITY_THRESHOLD = 50;
    
    /**
     * Extract face embeddings from image data
     * In production, this would use a real ML model (face-api.js, AWS Rekognition, etc.)
     * For demo, we create a simulated embedding based on image characteristics
     */
    public function extractEmbeddings(string $imageData): array
    {
        // Remove base64 prefix
        $image = $this->cleanBase64Image($imageData);
        $imageBytes = base64_decode($image);
        
        // Create a hash-based embedding (simulated)
        // In production: Use face-api.js, TensorFlow, or cloud API
        $embedding = [];
        $hash = hash('sha512', $imageBytes);
        
        // Generate 128-dimensional embedding from hash
        for ($i = 0; $i < 128; $i++) {
            $embedding[] = (hexdec(substr($hash, $i % 128, 2)) / 255.0) - 0.5;
        }
        
        // Calculate quality score based on image size and characteristics
        $qualityScore = $this->calculateQualityScore($imageBytes);
        
        return [
            'embedding' => $embedding,
            'quality_score' => $qualityScore,
            'hash' => md5(json_encode($embedding)),
        ];
    }
    
    /**
     * Compare two embeddings and return similarity score (0-100)
     */
    public function compareEmbeddings(array $embedding1, array $embedding2): float
    {
        if (count($embedding1) !== count($embedding2)) {
            return 0;
        }
        
        // Cosine similarity
        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;
        
        for ($i = 0; $i < count($embedding1); $i++) {
            $dotProduct += $embedding1[$i] * $embedding2[$i];
            $norm1 += $embedding1[$i] * $embedding1[$i];
            $norm2 += $embedding2[$i] * $embedding2[$i];
        }
        
        if ($norm1 == 0 || $norm2 == 0) {
            return 0;
        }
        
        $similarity = $dotProduct / (sqrt($norm1) * sqrt($norm2));
        
        // Convert to 0-100 scale
        return round(($similarity + 1) * 50, 2);
    }
    
    /**
     * Verify face against stored embeddings
     */
    public function verifyFace(User $user, string $imageData, array $livenessData = []): array
    {
        $result = [
            'success' => false,
            'match_score' => 0,
            'liveness_score' => 0,
            'quality_score' => 0,
            'message' => '',
            'checks' => [],
        ];
        
        // Extract embeddings from captured image
        $capturedData = $this->extractEmbeddings($imageData);
        $result['quality_score'] = $capturedData['quality_score'];
        
        // Check image quality
        if ($capturedData['quality_score'] < self::QUALITY_THRESHOLD) {
            $result['message'] = 'Image quality too low. Please ensure good lighting.';
            $result['checks']['quality'] = false;
            return $result;
        }
        $result['checks']['quality'] = true;
        
        // Check liveness
        $livenessResult = $this->checkLiveness($livenessData);
        $result['liveness_score'] = $livenessResult['score'];
        $result['checks']['liveness'] = $livenessResult['checks'];
        
        if ($livenessResult['score'] < self::LIVENESS_THRESHOLD) {
            $result['message'] = 'Liveness check failed. ' . $livenessResult['message'];
            return $result;
        }
        
        // Get stored embeddings
        $storedEmbeddings = FaceEmbedding::getActiveEmbeddings($user->id);
        
        if ($storedEmbeddings->isEmpty()) {
            $result['message'] = 'No face data registered. Please contact admin.';
            return $result;
        }
        
        // Compare with stored embeddings
        $maxScore = 0;
        foreach ($storedEmbeddings as $stored) {
            $storedEmbed = json_decode($stored->embedding_data, true);
            $score = $this->compareEmbeddings($capturedData['embedding'], $storedEmbed);
            $maxScore = max($maxScore, $score);
        }
        
        $result['match_score'] = $maxScore;
        $result['checks']['match'] = $maxScore >= self::MATCH_THRESHOLD;
        
        if ($maxScore >= self::MATCH_THRESHOLD) {
            $result['success'] = true;
            $result['message'] = 'Face verified successfully!';
        } else {
            $result['message'] = 'Face does not match. Please try again.';
        }
        
        return $result;
    }
    
    /**
     * Check liveness based on user actions
     */
    public function checkLiveness(array $livenessData): array
    {
        $checks = [
            'blink_detected' => $livenessData['blink_detected'] ?? false,
            'head_movement' => $livenessData['head_movement'] ?? false,
            'face_centered' => $livenessData['face_centered'] ?? false,
            'multiple_frames' => $livenessData['multiple_frames'] ?? false,
        ];
        
        $passedChecks = array_filter($checks);
        $score = (count($passedChecks) / count($checks)) * 100;
        
        $message = '';
        if (!$checks['blink_detected']) {
            $message = 'Please blink your eyes.';
        } elseif (!$checks['head_movement']) {
            $message = 'Please move your head slightly.';
        } elseif (!$checks['face_centered']) {
            $message = 'Please center your face in the frame.';
        }
        
        return [
            'score' => $score,
            'checks' => $checks,
            'message' => $message,
        ];
    }
    
    /**
     * Detect potential spoofing attempts
     */
    public function detectSpoof(array $frameData): array
    {
        $isSpoof = false;
        $confidence = 100;
        $reasons = [];
        
        // Check for static image (no variation between frames)
        if (isset($frameData['frame_variance']) && $frameData['frame_variance'] < 0.01) {
            $isSpoof = true;
            $confidence = 90;
            $reasons[] = 'Static image detected';
        }
        
        // Check for screen reflection patterns
        if (isset($frameData['reflection_detected']) && $frameData['reflection_detected']) {
            $isSpoof = true;
            $confidence = 85;
            $reasons[] = 'Screen reflection detected';
        }
        
        // Check for unnatural edges (printed photo)
        if (isset($frameData['edge_sharpness']) && $frameData['edge_sharpness'] > 0.9) {
            $isSpoof = true;
            $confidence = 80;
            $reasons[] = 'Unnatural image edges detected';
        }
        
        return [
            'is_spoof' => $isSpoof,
            'confidence' => $confidence,
            'reasons' => $reasons,
        ];
    }
    
    /**
     * Enroll face for a user (registration)
     */
    public function enrollFace(User $user, array $images): array
    {
        $enrolledCount = 0;
        $errors = [];
        
        foreach ($images as $index => $imageData) {
            $embeddingData = $this->extractEmbeddings($imageData);
            
            if ($embeddingData['quality_score'] < self::QUALITY_THRESHOLD) {
                $errors[] = "Image " . ($index + 1) . " quality too low";
                continue;
            }
            
            FaceEmbedding::create([
                'user_id' => $user->id,
                'embedding_data' => json_encode($embeddingData['embedding']),
                'embedding_hash' => $embeddingData['hash'],
                'quality_score' => $embeddingData['quality_score'],
                'is_primary' => $enrolledCount === 0,
                'is_active' => true,
            ]);
            
            $enrolledCount++;
        }
        
        if ($enrolledCount > 0) {
            $user->update([
                'face_verified' => true,
                'face_enrollment_count' => $enrolledCount,
                'face_enrolled_at' => now(),
            ]);
        }
        
        return [
            'success' => $enrolledCount > 0,
            'enrolled_count' => $enrolledCount,
            'errors' => $errors,
        ];
    }
    
    /**
     * Calculate image quality score
     */
    protected function calculateQualityScore(string $imageBytes): int
    {
        $size = strlen($imageBytes);
        
        // Basic quality estimation based on file size
        // Larger files typically have more detail
        if ($size < 10000) return 30;
        if ($size < 30000) return 50;
        if ($size < 60000) return 70;
        if ($size < 100000) return 85;
        return 95;
    }
    
    /**
     * Clean base64 image string
     */
    protected function cleanBase64Image(string $imageData): string
    {
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        return $image;
    }
    
    /**
     * Log verification attempt
     */
    public function logVerification(array $data): FaceVerificationLog
    {
        return FaceVerificationLog::logAttempt($data);
    }
}
