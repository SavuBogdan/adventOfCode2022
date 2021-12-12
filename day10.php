<?php

use JetBrains\PhpStorm\Pure;

require_once './AbstractBenchmarking.php';

class Parser
{
    private array $startingBrackets = ['{', '<', '[', '('];
    private array $endingBrackets = ['}', '>', ']', ')'];

    public function isCorrupted(string $input): string|bool
    {
        $brackets = [];
        foreach (str_split($input) as $char) {

            if (in_array($char, $this->startingBrackets)) {
                $brackets[] = $char;
            } else {
                $key = array_pop($brackets);

                if (!empty($key) && array_search($key, $this->startingBrackets) !== array_search($char, $this->endingBrackets)) {
                    return $char;
                }
            }
        }

        return false;
    }

    public function isIncomplete(string $input): array|bool
    {
        $brackets = [];
        foreach (str_split($input) as $char) {
            if (in_array($char, $this->startingBrackets)) {
                $brackets[] = $this->endingBrackets[array_search($char, $this->startingBrackets)];
            } else {
                $key = array_pop($brackets);
                if (!empty($key) && $char !== $key) {
                    return false;
                }
            }
        }

        return array_reverse($brackets);
    }

    public function scoreBracket(string $input): int
    {
        return match ($input) {
            ')' => 3,
            ']' => 57,
            '}' => 1197,
            '>' => 25137,
        };
    }

    #[Pure] public function scoreIncompleteBrackets(array $input): int
    {
        $score = 0;
        foreach ($input as $char) {
            $score = $score * 5 + match ($char) {
                    ')' => 1,
                    ']' => 2,
                    '}' => 3,
                    '>' => 4,
                };
        }
        return $score;
    }
}


class Day10 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day10.txt');
        $this->rawTestData = file_get_contents('TestData/day10.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        foreach (explode(PHP_EOL, $rawInputData) as $value) {
            if ($test) {
                $this->testData[] = $value;
            } else {
                $this->data[] = $value;
            }
        }
    }

    public function part1(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $parser = new Parser();
        $score = 0;
        foreach ($inputData as $value) {
            $value = $parser->isCorrupted($value);
            if ($value) {
                $score += $parser->scoreBracket($value);
            }
        }
        return $score;
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $parser = new Parser();
        $scores = [];
        foreach ($inputData as $value) {
            $value = $parser->isIncomplete($value);
            if ($value) {
                $scores[] = $parser->scoreIncompleteBrackets($value);
            }
        }
        sort($scores);
        return $scores[ceil(count($scores) / 2) - 1];
    }
}