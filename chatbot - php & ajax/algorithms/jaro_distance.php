<?php

class jaro_distance
{
    public function jaro_distance($s1, $s2)
    {
        $len1 = strlen($s1);
        $len2 = strlen($s2);

        if ($len1 == 0 || $len2 == 0) {
            return 0.0;
        }

        $matchDistance = (int)floor(max($len1, $len2) / 2) - 1;

        $s1Matches = array_fill(0, $len1, false);
        $s2Matches = array_fill(0, $len2, false);

        $matches = 0;
        for ($i = 0; $i < $len1; $i++) {
            $start = max(0, $i - $matchDistance);
            $end = min($len2 - 1, $i + $matchDistance);

            for ($j = $start; $j <= $end; $j++) {
                if ($s2Matches[$j]) {
                    continue;
                }
                if ($s1[$i] !== $s2[$j]) {
                    continue;
                }
                $s1Matches[$i] = true;
                $s2Matches[$j] = true;
                $matches++;
                break;
            }
        }

        if ($matches === 0) {
            return 0.0;
        }

        $t = 0;
        $k = 0;
        for ($i = 0; $i < $len1; $i++) {
            if (!$s1Matches[$i]) {
                continue;
            }
            while (!$s2Matches[$k]) {
                $k++;
            }
            if ($s1[$i] !== $s2[$k]) {
                $t++;
            }
            $k++;
        }
        $t /= 2;

        return (1 / 3) * ($matches / $len1 + $matches / $len2 + ($matches - $t) / $matches);
    }
}
?>