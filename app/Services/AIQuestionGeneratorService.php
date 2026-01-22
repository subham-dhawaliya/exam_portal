<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIQuestionGeneratorService
{
    protected string $provider;
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->provider = config('services.ai.provider', 'gemini');
        $this->apiKey = config('services.ai.api_key');
        $this->model = config('services.ai.model', 'gemini-2.0-flash');
    }

    /**
     * Generate questions using AI
     */
    public function generateQuestions(array $params): array
    {
        $topic = $params['topic'];
        $questionType = $params['question_type'];
        $difficulty = $params['difficulty'];
        $count = min($params['count'] ?? 5, 20);
        $instructions = $params['instructions'] ?? '';

        // Build the prompt
        $prompt = $this->buildPrompt($topic, $questionType, $difficulty, $count, $instructions);

        try {
            if ($this->provider === 'openai') {
                $response = $this->callOpenAI($prompt);
            } else {
                $response = $this->callGemini($prompt);
            }

            return $this->parseResponse($response, $questionType);

        } catch (\Exception $e) {
            Log::error('AI Question Generation Failed', [
                'error' => $e->getMessage(),
                'provider' => $this->provider,
                'topic' => $topic,
            ]);
            
            throw new \Exception('Failed to generate questions: ' . $e->getMessage());
        }
    }

    /**
     * Build prompt based on question type
     */
    protected function buildPrompt(string $topic, string $type, string $difficulty, int $count, string $instructions): string
    {
        $difficultyGuide = match($difficulty) {
            'easy' => 'Simple, straightforward questions suitable for beginners. Use basic concepts.',
            'medium' => 'Moderate difficulty requiring good understanding. Include some application-based questions.',
            'hard' => 'Challenging questions requiring deep understanding. Include analysis and problem-solving.',
            default => 'Medium difficulty level.'
        };

        $basePrompt = "You are an expert exam question creator. Generate exactly {$count} high-quality questions about \"{$topic}\".\n\n";
        $basePrompt .= "Difficulty Level: {$difficulty} - {$difficultyGuide}\n\n";
        
        if ($instructions) {
            $basePrompt .= "Additional Instructions: {$instructions}\n\n";
        }

        $formatPrompt = match($type) {
            'mcq' => $this->getMCQPrompt($count),
            'multiple_select' => $this->getMultipleSelectPrompt($count),
            'true_false' => $this->getTrueFalsePrompt($count),
            'fill_blank' => $this->getFillBlankPrompt($count),
            'short_answer' => $this->getShortAnswerPrompt($count),
            'numerical' => $this->getNumericalPrompt($count),
            'match_following' => $this->getMatchFollowingPrompt($count),
            'ordering' => $this->getOrderingPrompt($count),
            'essay' => $this->getEssayPrompt($count),
            default => $this->getMCQPrompt($count),
        };

        return $basePrompt . $formatPrompt . "\n\nIMPORTANT: Return ONLY valid JSON array, no markdown, no explanation, just the JSON.";
    }

    protected function getMCQPrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} Multiple Choice Questions (MCQ) with exactly 4 options each.

Return as JSON array with this exact structure:
[
  {
    "question_text": "Clear question text here?",
    "options": ["Option A", "Option B", "Option C", "Option D"],
    "correct_index": 0,
    "explanation": "Brief explanation why this answer is correct"
  }
]

Rules:
- Each question must have exactly 4 options
- correct_index is 0-based (0 for first option, 1 for second, etc.)
- All options should be plausible
- Only ONE correct answer per question
- Questions should be clear and unambiguous
PROMPT;
    }

    protected function getMultipleSelectPrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} Multiple Select Questions where 2-3 options can be correct.

Return as JSON array:
[
  {
    "question_text": "Select all correct answers:",
    "options": ["Option A", "Option B", "Option C", "Option D"],
    "correct_indices": [0, 2],
    "explanation": "Explanation for correct answers"
  }
]

Rules:
- Each question must have 4-5 options
- correct_indices is array of 0-based indices for ALL correct options
- At least 2 correct answers per question
PROMPT;
    }

    protected function getTrueFalsePrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} True/False questions.

Return as JSON array:
[
  {
    "question_text": "Statement that is either true or false.",
    "correct_answer": true,
    "explanation": "Why this statement is true/false"
  }
]

Rules:
- Statements should be clear and factual
- correct_answer must be boolean (true or false)
- Avoid ambiguous statements
PROMPT;
    }

    protected function getFillBlankPrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} Fill in the Blank questions.

Return as JSON array:
[
  {
    "question_text": "The _____ is the powerhouse of the cell.",
    "answers": ["mitochondria", "mitochondrion"],
    "explanation": "Explanation of the answer"
  }
]

Rules:
- Use _____ to indicate the blank
- answers array should include all acceptable answers (variations, spellings)
- Keep blanks for key terms/concepts
PROMPT;
    }

    protected function getShortAnswerPrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} Short Answer questions requiring 1-2 sentence answers.

Return as JSON array:
[
  {
    "question_text": "What is the main function of...?",
    "answers": ["key phrase 1", "key phrase 2"],
    "explanation": "Complete answer explanation"
  }
]

Rules:
- Questions should have concise answers
- answers array contains key phrases that should appear in correct answer
PROMPT;
    }

    protected function getNumericalPrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} Numerical/Calculation questions.

Return as JSON array:
[
  {
    "question_text": "Calculate the value of... (show your work)",
    "answer": 42.5,
    "tolerance": 0.1,
    "explanation": "Step by step solution"
  }
]

Rules:
- answer must be a number
- tolerance is the acceptable range (±)
- Include calculation steps in explanation
PROMPT;
    }

    protected function getMatchFollowingPrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} Match the Following questions with 4-5 pairs each.

Return as JSON array:
[
  {
    "question_text": "Match the following items:",
    "pairs": [
      {"left": "Term 1", "right": "Definition 1"},
      {"left": "Term 2", "right": "Definition 2"},
      {"left": "Term 3", "right": "Definition 3"},
      {"left": "Term 4", "right": "Definition 4"}
    ],
    "explanation": "Brief explanation of the matches"
  }
]
PROMPT;
    }

    protected function getOrderingPrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} Ordering/Sequence questions where items must be arranged in correct order.

Return as JSON array:
[
  {
    "question_text": "Arrange the following in correct order:",
    "items": ["First step", "Second step", "Third step", "Fourth step"],
    "explanation": "Why this order is correct"
  }
]

Rules:
- items array should be in CORRECT order
- Include 4-6 items per question
- Order should be logical (chronological, size, process steps, etc.)
PROMPT;
    }

    protected function getEssayPrompt(int $count): string
    {
        return <<<PROMPT
Generate {$count} Essay/Long Answer questions that require detailed written responses.

Return as JSON array:
[
  {
    "question_text": "Discuss in detail the importance of... Explain with examples.",
    "key_points": ["Key point 1 to cover", "Key point 2 to cover", "Key point 3 to cover"],
    "word_limit": 500,
    "explanation": "Model answer outline or grading criteria"
  }
]

Rules:
- Questions should require analytical/descriptive answers
- Include key_points array with main points expected in answer
- word_limit suggests expected answer length
- explanation should contain grading criteria or model answer outline
PROMPT;
    }

    /**
     * Call OpenAI API
     */
    protected function callOpenAI(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model ?: 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert educational content creator. Always respond with valid JSON only.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 4000,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        return $response->json('choices.0.message.content');
    }

    /**
     * Call Google Gemini API
     */
    protected function callGemini(string $prompt): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(90)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 4000,
                'responseMimeType' => 'application/json',
            ]
        ]);

        if (!$response->successful()) {
            $error = $response->json();
            $errorCode = $error['error']['code'] ?? 'unknown';
            $errorMessage = $error['error']['message'] ?? 'Unknown error';
            
            // Handle rate limit error
            if ($errorCode == 429) {
                throw new \Exception('Rate limit exceeded. Please wait a minute and try again with fewer questions (2-3).');
            }
            
            // Handle model not found
            if ($errorCode == 404) {
                throw new \Exception('AI model not found. Please check AI_MODEL in .env file. Try: gemini-2.0-flash or gemini-1.5-flash');
            }
            
            throw new \Exception('Gemini API error: ' . $errorMessage);
        }

        $text = $response->json('candidates.0.content.parts.0.text');
        
        if (empty($text)) {
            throw new \Exception('Empty response from AI. Please try again.');
        }
        
        return trim($text);
    }

    /**
     * Parse AI response into structured format
     */
    protected function parseResponse(string $response, string $questionType): array
    {
        // Log raw response for debugging
        Log::info('AI Raw Response', ['response' => substr($response, 0, 500)]);
        
        // Clean up the response
        $json = $response;
        
        // Remove any text before the first [
        if (($pos = strpos($json, '[')) !== false) {
            $json = substr($json, $pos);
        }
        
        // Remove any text after the last ]
        if (($pos = strrpos($json, ']')) !== false) {
            $json = substr($json, 0, $pos + 1);
        }
        
        // Remove any remaining markdown or extra characters
        $json = preg_replace('/```json\s*/i', '', $json);
        $json = preg_replace('/```\s*/i', '', $json);
        $json = trim($json);
        
        // Fix common JSON issues from AI
        // Replace single quotes with double quotes (if AI uses single quotes)
        // But be careful not to break strings that contain apostrophes
        
        // Try to decode
        $questions = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try one more time with more aggressive cleaning
            $json = preg_replace('/[\x00-\x1F\x7F]/u', '', $json); // Remove control characters
            $questions = json_decode($json, true);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse AI response', [
                'error' => json_last_error_msg(),
                'response' => $response,
                'cleaned' => $json
            ]);
            throw new \Exception('Failed to parse AI response. Please try again.');
        }

        if (!is_array($questions) || empty($questions)) {
            throw new \Exception('Invalid response format from AI. No questions generated.');
        }

        // Normalize the response based on question type
        return array_map(function($q) use ($questionType) {
            return $this->normalizeQuestion($q, $questionType);
        }, $questions);
    }

    /**
     * Normalize question to standard format
     */
    protected function normalizeQuestion(array $question, string $type): array
    {
        $normalized = [
            'question_type' => $type,
            'question_text' => $question['question_text'] ?? '',
            'explanation' => $question['explanation'] ?? '',
        ];

        switch ($type) {
            case 'mcq':
                $normalized['options'] = $question['options'] ?? [];
                $normalized['correct_index'] = $question['correct_index'] ?? 0;
                break;

            case 'multiple_select':
                $normalized['options'] = $question['options'] ?? [];
                $normalized['correct_indices'] = $question['correct_indices'] ?? [];
                break;

            case 'true_false':
                $normalized['correct_answer'] = $question['correct_answer'] ?? true;
                break;

            case 'fill_blank':
            case 'short_answer':
                $normalized['answers'] = $question['answers'] ?? [];
                break;

            case 'numerical':
                $normalized['answer'] = $question['answer'] ?? 0;
                $normalized['tolerance'] = $question['tolerance'] ?? 0;
                break;

            case 'match_following':
                $normalized['pairs'] = $question['pairs'] ?? [];
                break;

            case 'ordering':
                $normalized['items'] = $question['items'] ?? [];
                break;

            case 'essay':
                $normalized['key_points'] = $question['key_points'] ?? [];
                $normalized['word_limit'] = $question['word_limit'] ?? 500;
                break;
        }

        return $normalized;
    }

    /**
     * Check if AI service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get current provider name
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
