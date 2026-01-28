<?php

class Calculator
{
    public function calculate($expression)
    {
        $expression = $this->sanitizeExpression($expression);

        if (!$this->isValidExpression($expression)) {
            return 'Error: Invalid Expression';
        }

        try {
            $result = $this->evaluateExpression($expression);
            return $result;
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    private function sanitizeExpression($expression)
    {
        // Remove all characters except digits, operators, parentheses, and whitespace
        return preg_replace('#[^0-9+\-*/().\s^]#', '', $expression);
    }

    private function isValidExpression($expression)
    {
        // Basic validation
        return preg_match('#^[\d\s+\-*/().^]+$#', $expression) &&
            preg_match('#\d[+\-*/]\d#', $expression);
    }

    private function evaluateExpression($expression)
    {
        $result = INF;
        try {
            // Replace exponentiation operator with PHP-compatible operator
            $expression = str_replace('^', '**', $expression);
            // Evaluate the expression
            @eval('$result = ' . $expression . ';');
            if ($result === INF) {
                throw new Exception('Invalid expression');
            }
        } catch (Exception $e) {
            throw new Exception('Invalid expression');
        }
        return $result;
    }

    public function isMathematicalExpression($expression)
    {
        $pattern = '/^[\d\s\+\-*\/().^]+$/';

        // Trim any extra whitespace
        $expression = trim($expression);
        return preg_match($pattern, $expression) && $this->isValidExpression($expression);
    }

    // Additional arithmetic functions
    public function roundToNearest($value)
    {
        return round($value);
    }

    public function roundDown($value)
    {
        return floor($value);
    }

    public function roundUp($value)
    {
        return ceil($value);
    }

    public function nearestInteger($value)
    {
        $rounded = round($value);
        $lower = floor($value);
        $upper = ceil($value);

        return abs($value - $lower) <= abs($value - $upper) ? $lower : $upper;
    }

    public function power($base, $exponent)
    {
        return pow($base, $exponent);
    }

    public function squareRoot($value)
    {
        return sqrt($value);
    }

    public function logarithm($value, $base = 10)
    {
        return log($value, $base);
    }

    public function factorial($value)
    {
        if ($value < 0) {
            throw new Exception('Factorial is not defined for negative numbers.');
        }
        $factorial = 1;
        for ($i = 1; $i <= $value; $i++) {
            $factorial *= $i;
        }
        return $factorial;
    }

    public function sine($angle)
    {
        return sin(deg2rad($angle));
    }

    public function cosine($angle)
    {
        return cos(deg2rad($angle));
    }

    public function tangent($angle)
    {
        return tan(deg2rad($angle));
    }

    public function absoluteValue($value)
    {
        return abs($value);
    }

    public function percentage($value, $total)
    {
        if ($total == 0) {
            throw new Exception('Total must be non-zero for percentage calculation.');
        }
        return ($value / $total) * 100;
    }

    public function combination($n, $k)
    {
        if ($k > $n || $n < 0 || $k < 0) {
            throw new Exception('Invalid values for combination.');
        }
        return $this->factorial($n) / ($this->factorial($k) * $this->factorial($n - $k));
    }

    public function permutation($n, $k)
    {
        if ($k > $n || $n < 0 || $k < 0) {
            throw new Exception('Invalid values for permutation.');
        }
        return $this->factorial($n) / $this->factorial($n - $k);
    }

    public function hypotenuse($a, $b)
    {
        return sqrt($a**2 + $b**2);
    }

    public function mean(array $values)
    {
        if (empty($values)) {
            throw new Exception('Cannot calculate mean of an empty array.');
        }
        return array_sum($values) / count($values);
    }

    public function median(array $values)
    {
        if (empty($values)) {
            throw new Exception('Cannot calculate median of an empty array.');
        }
        sort($values);
        $count = count($values);
        $middle = (int)($count / 2);

        if ($count % 2 == 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }
        return $values[$middle];
    }

    public function mode(array $values)
    {
        if (empty($values)) {
            throw new Exception('Cannot calculate mode of an empty array.');
        }
        $values = array_count_values($values);
        arsort($values);
        $mode = array_key_first($values);

        return $mode;
    }

    public function variance(array $values)
    {
        if (empty($values)) {
            throw new Exception('Cannot calculate variance of an empty array.');
        }
        $mean = $this->mean($values);
        $squaredDifferences = array_map(fn($value) => ($value - $mean) ** 2, $values);
        return array_sum($squaredDifferences) / count($values);
    }

    public function standardDeviation(array $values)
    {
        return sqrt($this->variance($values));
    }
}
