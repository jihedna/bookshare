<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiNlpService
{
    private string $provider;
    private ?string $apiKey;

    public function __construct()
    {
        $this->provider = env('AI_PROVIDER', 'none');
        $this->apiKey = env('AI_API_KEY');
    }

    /**
     * Generate a summary and tags for a book based on its title and description
     *
     * @param string $title Book title
     * @param string|null $description Book description
     * @return array ['summary' => string, 'tags' => array]
     */
    public function generateSummaryAndTags(string $title, ?string $description): array
    {
        $result = [
            'summary' => null,
            'tags' => [],
        ];

        if ($this->provider === 'none' || empty($this->apiKey)) {
            // Fallback to deterministic stub
            $result['summary'] = $description ? substr($description, 0, 120) . '...' : "Un livre intitulé '{$title}'.";
            return $result;
        }

        try {
            $content = "Titre: {$title}\n";
            $content .= "Description: " . ($description ?? 'Non disponible');

            if ($this->provider === 'openai') {
                $response = $this->callOpenAi($content);
                return $this->parseOpenAiResponse($response);
            } elseif ($this->provider === 'hf') {
                $response = $this->callHuggingFace($content);
                return $this->parseHuggingFaceResponse($response);
            }
        } catch (\Exception $e) {
            Log::error('AI NLP Service error: ' . $e->getMessage());
            // Fallback to deterministic stub
            $result['summary'] = $description ? substr($description, 0, 120) . '...' : "Un livre intitulé '{$title}'.";
        }

        return $result;
    }

    /**
     * Get book recommendations based on content similarity
     *
     * @param array $bookData Book data to base recommendations on
     * @return array List of recommendation criteria
     */
    public function getRecommendationCriteria(array $bookData): array
    {
        // This is a simplified version that would normally use AI
        // to expand search terms, but we'll just return the original data
        return [
            'title_terms' => explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $bookData['title'])),
            'author_terms' => explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $bookData['author'])),
        ];
    }

    /**
     * Check if content is appropriate (moderation)
     *
     * @param string $content Content to check
     * @return bool True if content is appropriate, false otherwise
     */
    public function isContentAppropriate(string $content): bool
    {
        if ($this->provider === 'none' || empty($this->apiKey)) {
            // Default to allowing content if AI is disabled
            return true;
        }

        try {
            if ($this->provider === 'openai') {
                $response = $this->callOpenAiModeration($content);
                return !$response['flagged'] ?? true;
            }
        } catch (\Exception $e) {
            Log::error('AI Moderation error: ' . $e->getMessage());
            // Default to allowing content if there's an error
            return true;
        }

        return true;
    }

    private function callOpenAi(string $content): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Vous êtes un assistant qui analyse des livres. Générez un résumé court (une phrase) et des tags pertinents. Répondez au format JSON avec les clés "summary" et "tags" (array de strings).'
                ],
                [
                    'role' => 'user',
                    'content' => $content
                ]
            ],
            'temperature' => 0.7,
        ]);

        return $response->json();
    }

    private function parseOpenAiResponse(array $response): array
    {
        $result = [
            'summary' => null,
            'tags' => [],
        ];

        if (isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            
            // Try to parse as JSON
            try {
                $parsed = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $result['summary'] = $parsed['summary'] ?? null;
                    $result['tags'] = $parsed['tags'] ?? [];
                } else {
                    // If not valid JSON, try to extract manually
                    if (preg_match('/summary["\s:]+([^"]+)/i', $content, $matches)) {
                        $result['summary'] = trim($matches[1], " \t\n\r\0\x0B\"',:");
                    }
                    
                    if (preg_match_all('/tags["\s:]+\[(.*?)\]/is', $content, $matches)) {
                        $tagsStr = $matches[1][0] ?? '';
                        preg_match_all('/"([^"]+)"/', $tagsStr, $tagMatches);
                        $result['tags'] = $tagMatches[1] ?? [];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to parse OpenAI response: ' . $e->getMessage());
            }
        }

        return $result;
    }

    private function callOpenAiModeration(string $content): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/moderations', [
            'input' => $content
        ]);

        return $response->json()['results'][0] ?? ['flagged' => false];
    }

    private function callHuggingFace(string $content): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api-inference.huggingface.co/models/facebook/bart-large-cnn', [
            'inputs' => $content,
            'parameters' => [
                'max_length' => 100,
                'min_length' => 30,
            ]
        ]);

        return $response->json();
    }

    private function parseHuggingFaceResponse(array $response): array
    {
        $result = [
            'summary' => null,
            'tags' => [],
        ];

        if (isset($response[0]['summary_text'])) {
            $result['summary'] = $response[0]['summary_text'];
            
            // Extract potential tags from the summary
            $words = explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $result['summary']));
            $potentialTags = array_filter($words, function($word) {
                return strlen($word) > 3;
            });
            
            $result['tags'] = array_slice(array_unique($potentialTags), 0, 5);
        }

        return $result;
    }
}
