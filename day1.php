<?php

/**
 *
 */
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

    public function part1(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $count = 0;
        $previousValue = $inputData[0];

        foreach ($inputData as $value) {
            if ($previousValue < $value) {
                $count++;
            }
            $previousValue = $value;
        }
        return $count;
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $count = 0;
        $previousGroup = $inputData[0] + $inputData[1] + $inputData[2];
        $countInputData = count($inputData);
        for ($i = 2; $i < $countInputData - 2; $i++) {
            $currentGroup = $inputData[$i] + $inputData[$i + 1] + $inputData[$i + 2];
            if ($previousGroup < $currentGroup) {
                $count++;
            }
            $previousGroup = $currentGroup;
        }
        return $count;
    }

    public function parseData(bool $test = false)
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        if ($test) {
            $this->testData = array_map('intval',explode("\n", $rawInputData));
        } else {
            $this->data = array_map('intval',explode("\n", $rawInputData));
        }
    }
}