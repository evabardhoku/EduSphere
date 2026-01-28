<?php
include_once "NLP.php";
include_once "calculator.php";
class QueryProcessor
{
    private $calculator;
    private $nlp;

    public function __construct()
    {
        $this->calculator = new Calculator();
        $this->nlp = new NLP();
    }

    public function processInput($input, $databaseQueries = [])
    {
        // Check if the input is a mathematical expression
        if ($this->calculator->isMathematicalExpression($input)) {
            // If it's a mathematical expression, calculate the result
            return $this->calculator->calculate($input);
        } else {
            // If it's not a mathematical expression, process it with NLP
            $fuzzyMatches = $this->nlp->fuzzySearch($input, $databaseQueries);
            if (!empty($fuzzyMatches)) {
                return $fuzzyMatches;
            }

            // Perform other NLP operations as needed
            $closestLevenshtein = $this->nlp->correctTypos($input, $databaseQueries);
            $closestJaro = $this->nlp->findSimilarPhrases($input, $databaseQueries);
            $closestHamming = $this->nlp->compareFixedLengthStrings($input, $databaseQueries);

            // Return the closest matches or any other NLP results
            return [
                'Levenshtein' => $closestLevenshtein,
                'Jaro' => $closestJaro,
                'Hamming' => $closestHamming,
            ];
        }
    }
}
