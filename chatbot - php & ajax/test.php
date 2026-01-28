<?php
include 'calculator.php';
include 'message.php';
class CalculatorTest
{
    private $calculator;

    public function __construct()
    {
        $this->calculator = new calculator();
    }

    public function runTests()
    {
        echo "Running tests...\n";

        // Test cases for isMathematicalExpression
        $this->testIsMathematicalExpression('2+2', true);
        $this->testIsMathematicalExpression('3.5*4', true);
        $this->testIsMathematicalExpression('7-(1+2)', true);
        $this->testIsMathematicalExpression('2++2', false);
        $this->testIsMathematicalExpression('3.5**4', false);
        $this->testIsMathematicalExpression('7-(1++2)', false);
        $this->testIsMathematicalExpression('2 2', true); // Assuming spaces are allowed
        $this->testIsMathematicalExpression('2+*2', false);
        $this->testIsMathematicalExpression('3/0', true); // Edge case: division by zero

        echo "Tests completed.\n";
    }

    private function testIsMathematicalExpression($expression, $expected)
    {
        $result = $this->calculator->isMathematicalExpression($expression);
        if ($result === $expected) {
            echo "Test passed for expression: $expression\n";
        } else {
            echo "Test failed for expression: $expression. Expected: $expected, Got: $result\n";
        }
    }
}

// Create an instance of the test class and run tests
$test = new CalculatorTest();
$test->runTests();
?>
