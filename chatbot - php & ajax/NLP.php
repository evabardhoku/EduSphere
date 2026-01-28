<?php

// Include the individual algorithm classes
include 'algorithms/levenshtein_distance.php';
include 'algorithms/jaro_distance.php';
include 'algorithms/cosine_algorithm.php';
include 'algorithms/tfidf.php'; // Shto këtë rresht

class NLP
{
    private $levenshteinDistance;
    private $jaroDistance;
    private $cosineSimilarity;
    private $tfidf;

    public function __construct($documents)
    {
        // Initialize the algorithm classes
        $this->levenshteinDistance = new levenshtein_distance();
        $this->jaroDistance = new jaro_distance();
        $this->cosineSimilarity = new CosineSimilarity();
        $this->tfidf = new TFIDF($documents); // Inicializo TFIDF me dokumentet
    }

    // Levenshtein Distance to correct small typos
    public function correctTypos($input, $databaseQueries, $threshold = 5)
    {
        $closestMatches = [];

        foreach ($databaseQueries as $query) {
            $distance = $this->levenshteinDistance->calculate($input, $query);

            if ($distance <= $threshold) {
                $closestMatches[$query] = $distance;
            }
        }

        if (empty($closestMatches)) {
            return null;
        }

        asort($closestMatches);
        return key($closestMatches);
    }

    // Jaro Distance Algorithm
    public function findSimilarPhrases($input, $databaseQueries, $threshold = 0.7)
    {
        $closestMatches = [];

        foreach ($databaseQueries as $query) {
            $distance = $this->jaroDistance->jaro_distance($input, $query);

            if ($distance >= $threshold) {
                $closestMatches[$query] = $distance;
            }

            if ($distance === 1.0) {
                return $query;
            }
        }

        if (empty($closestMatches)) {
            return null;
        }

        arsort($closestMatches);
        return key($closestMatches);
    }

    // Cosine Similarity Algorithm
    public function findSimilarPhrasesWithCosine($input, $databaseQueries)
    {
        $closestMatch = null;
        $highestSimilarity = 0;

        foreach ($databaseQueries as $query) {
            $similarity = $this->cosineSimilarity->calculate($input, $query);
            if ($similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $closestMatch = $query;
            }
        }

        return $closestMatch;
    }

    // TF-IDF: Gjej dokumentet relevante për inputin
    public function getRelevantDocuments($input)
    {
        return $this->tfidf->getRelevantDocuments($input);
    }
}
