<?php

require_once './AbstractBenchmarking.php';

class Day4 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day4.txt');
        $this->rawTestData = file_get_contents('TestData/day4.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        preg_match_all('/(\d+)/m', $rawInputData, $matches);
        $lineInput = array_chunk(array_map('intval', $matches[1]), 4);
        if ($test) {
            $this->testData = $lineInput;
        } else {
            $this->data = $lineInput;
        }
    }

    public function part1(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $sum = 0;
        foreach ($inputData as $pair) {
            if (($pair[0] >= $pair[2] && $pair[1] <= $pair[3]) || ($pair[2] >= $pair[0] && $pair[3] <= $pair[1])) {
                $sum++;
            }
        }
        return $sum;
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $sum = 0;
        foreach ($inputData as $pair) {
            if (($pair[0] <= $pair[3] && $pair[1] >= $pair[2]) || ($pair[2] <= $pair[1] && $pair[3] >= $pair[0])) {
                $sum++;
            }
        }
        return $sum;
    }
}
