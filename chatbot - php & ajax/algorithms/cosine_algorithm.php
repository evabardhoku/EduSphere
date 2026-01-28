<?php

class CosineSimilarity {
// Metoda për të llogaritur ngjashmërinë midis dy teksteve
public function calculate($text1, $text2) {
$vector1 = $this->textToVector($text1);
$vector2 = $this->textToVector($text2);
return $this->dotProduct($vector1, $vector2) / (sqrt($this->magnitude($vector1)) * sqrt($this->magnitude($vector2)));
}

// Metoda për të konvertuar tekstin në një vektor
private function textToVector($text) {
$words = preg_split('/\W+/', strtolower($text));
$vector = array_count_values($words);
return $vector;
}

// Metoda për të llogaritur produktin e dot-it midis dy vektorëve
private function dotProduct($vector1, $vector2) {
$product = 0;
foreach ($vector1 as $word => $count) {
if (isset($vector2[$word])) {
$product += $count * $vector2[$word];
}
}
return $product;
}

// Metoda për të llogaritur magnitudën e një vektori
private function magnitude($vector) {
$sum = 0;
foreach ($vector as $count) {
$sum += $count * $count;
}
return $sum;
}
}
?>