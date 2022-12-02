<?php

require_once './AbstractBenchmarking.php';

class Day2 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day2.txt');
        $this->rawTestData = file_get_contents('TestData/day2.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        foreach (explode(PHP_EOL, $rawInputData) as $value) {
            $lineInput = explode(' ', $value);
            if ($test) {
                $this->testData[] = $lineInput;
            } else {
                $this->data[] = $lineInput;
            }
        }
    }

    public function part1(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $score = 0;
        foreach ($inputData as $roundData) {
            $keyOpponent  = ord($roundData[0]) - 65;
            $keyPlayer  = ord($roundData[1]) - 88;
            $result = $keyOpponent - $keyPlayer;
            if ($result === -1 || $result === 2) {
                $score += 6 + $keyPlayer + 1;
            } elseif ($result === -2 || $result === 1) {
                $score += $keyPlayer + 1;
            } else {
                $score += 3 + $keyPlayer + 1;
            }
        }
        return $score;
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $score = 0;
        foreach ($inputData as $roundData) {
            $key = ord($roundData[0]) - 65;
            if ($roundData[1] === 'X') {
                $score += ($key + 2) % 3 + 1;
            } elseif ($roundData[1] === 'Y') {
                $score += 3 + $key % 3 + 1;
            } else {
                $score += 6 + ($key + 1) % 3 + 1;
            }
        }
        return $score;
    }
}

$day = new Day2();
var_dump($day->part1());
//var_dump($day->part2());
