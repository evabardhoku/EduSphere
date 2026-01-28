<?php

class levenshtein_distance
{
    public function calculate($str1, $str2)
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        // Create a matrix with dimensions (len1+1) x (len2+1)
        $matrix = array();
        for ($i = 0; $i <= $len1; $i++) {
            $matrix[$i] = array();
            for ($j = 0; $j <= $len2; $j++) {
                if ($i == 0) {
                    $matrix[$i][$j] = $j;
                } elseif ($j == 0) {
                    $matrix[$i][$j] = $i;
                } else {
                    $cost = ($str1[$i - 1] == $str2[$j - 1]) ? 0 : 1;
                    $matrix[$i][$j] = min(
                        $matrix[$i - 1][$j] + 1, // Deletion
                        $matrix[$i][$j - 1] + 1, // Insertion
                        $matrix[$i - 1][$j - 1] + $cost // Substitution
                    );
                }
            }
        }

        return $matrix[$len1][$len2];
    }
}

//// Example usage
//$levenshtein = new Levenshtein_distance();
//
//$wordPairs = [
//    ["shkollë", "shkollar"],
//    ["mësues", "mësuese"],
//    ["ndihmës", "ndihmë"],
//    ["këndim", "këndues"],
//    // Add more word pairs as needed
//];
//
//foreach ($wordPairs as $pair) {
//    list($word1, $word2) = $pair;
//    echo "Levenshtein Distance between '$word1' and '$word2': " . $levenshtein->calculate($word1, $word2) . "<br>";
//}
?>
