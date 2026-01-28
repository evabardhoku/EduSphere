<?php

class TFIDF
{
    private $documents;
    private $tfidfScores = [];

    public function __construct($documents)
    {
        $this->documents = $documents;
        $this->calculateTFIDF();
    }

    private function calculateTFIDF()
    {
        $numDocuments = count($this->documents);
        $wordCounts = [];

        // Numëron frekuencën e fjalëve
        foreach ($this->documents as $doc) {
            $words = preg_split('/\W+/', strtolower($doc));
            $uniqueWords = array_unique($words);

            foreach ($uniqueWords as $word) {
                if (!isset($wordCounts[$word])) {
                    $wordCounts[$word] = 0;
                }
                $wordCounts[$word]++;
            }
        }

        // Llogarit TF-IDF
        foreach ($this->documents as $doc) {
            $this->tfidfScores[$doc] = [];
            $words = preg_split('/\W+/', strtolower($doc));

            foreach ($words as $word) {
                $tf = array_count_values($words)[$word] / count($words);
                $idf = log($numDocuments / ($wordCounts[$word] + 1));
                $this->tfidfScores[$doc][$word] = $tf * $idf;
            }
        }
    }

    public function getScores()
    {
        return $this->tfidfScores;
    }

    public function getRelevantDocuments($input)
    {
        $inputWords = preg_split('/\W+/', strtolower($input));
        $relevantDocs = [];

        foreach ($this->tfidfScores as $doc => $scores) {
            foreach ($inputWords as $word) {
                if (isset($scores[$word])) {
                    $relevantDocs[$doc] = $scores[$word];
                }
            }
        }

        return $relevantDocs;
    }
}
