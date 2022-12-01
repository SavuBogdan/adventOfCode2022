<?php

require_once './AbstractBenchmarking.php';

class Day1 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day1.txt');
        $this->rawTestData = file_get_contents('TestData/day1.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        foreach (explode(PHP_EOL, $rawInputData) as $value) {
            preg_match('/(\d+)$/', $value, $matches);
            $lineInput = array_map('intval', array_slice($matches, 1, 4));
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
        return 0;
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        return 0;
    }
}