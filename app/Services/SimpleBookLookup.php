<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimpleBookLookup
{
    /**
     * Find the best match for a book in Open Library
     *
     * @param string $title Book title
     * @param string|null $author Book author (optional)
     * @return array|null Book information if found, null otherwise
     */
    public function bestMatch(string $title, ?string $author = null): ?array
    {
        try {
            // Clean up the title for the API request
            $title = trim($title);
            if (empty($title)) {
                return null;
            }
            
            // Build the query
            $query = ['title' => $title];
            if (!empty($author)) {
                $query['author'] = $author;
            }
            
            // Call Open Library API
            $response = Http::get('https://openlibrary.org/search.json', $query);
            
            if (!$response->successful() || !isset($response['docs']) || empty($response['docs'])) {
                return null;
            }
            
            // Find the best match
            $bestMatch = $this->findBestMatch($response['docs'], $title, $author);
            if (!$bestMatch) {
                return null;
            }
            
            // Extract book information
            return [
                'title' => $bestMatch['title'] ?? $title,
                'author' => isset($bestMatch['author_name']) ? $bestMatch['author_name'][0] : ($author ?? ''),
                'cover_id' => $bestMatch['cover_i'] ?? null,
                'isbn' => isset($bestMatch['isbn']) ? $bestMatch['isbn'][0] : null,
            ];
        } catch (\Exception $e) {
            Log::error('Error searching for book in Open Library: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find the best match from Open Library results
     *
     * @param array $docs Open Library search results
     * @param string $title Book title to match
     * @param string|null $author Book author to match (optional)
     * @return array|null Best matching book or null if no good match
     */
    private function findBestMatch(array $docs, string $title, ?string $author = null): ?array
    {
        if (empty($docs)) {
            return null;
        }
        
        // If we only have one result, return it
        if (count($docs) === 1) {
            return $docs[0];
        }
        
        $title = strtolower($title);
        $author = $author ? strtolower($author) : null;
        
        $bestScore = 0;
        $bestMatch = null;
        
        foreach ($docs as $doc) {
            $score = 0;
            
            // Check title similarity
            if (isset($doc['title'])) {
                $docTitle = strtolower($doc['title']);
                similar_text($title, $docTitle, $percent);
                $score += $percent;
                
                // Exact match gets a big bonus
                if ($title === $docTitle) {
                    $score += 50;
                }
            }
            
            // Check author if provided
            if ($author && isset($doc['author_name']) && is_array($doc['author_name'])) {
                $authorMatch = false;
                foreach ($doc['author_name'] as $docAuthor) {
                    $docAuthor = strtolower($docAuthor);
                    if (strpos($docAuthor, $author) !== false || strpos($author, $docAuthor) !== false) {
                        $authorMatch = true;
                        break;
                    }
                }
                
                if ($authorMatch) {
                    $score += 30;
                }
            }
            
            // Prefer books with covers
            if (isset($doc['cover_i'])) {
                $score += 10;
            }
            
            // Prefer books with ISBNs
            if (isset($doc['isbn']) && !empty($doc['isbn'])) {
                $score += 5;
            }
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $doc;
            }
        }
        
        // Only return a match if the score is high enough
        return $bestScore > 50 ? $bestMatch : null;
    }
}
