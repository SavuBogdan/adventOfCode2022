<?php

require_once './AbstractBenchmarking.php';

class Day3 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day3.txt');
        $this->rawTestData = file_get_contents('TestData/day3.txt');
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
        $sum = 0;
        $table = array_combine(array_merge(range('a', 'z'), range('A', 'Z')), range(1, 52));
        foreach ($inputData as $rucksack) {
            $compLen = strlen($rucksack) / 2;
            for ($i = 0; $i < $compLen; $i++) {
                if (str_contains(substr($rucksack, $compLen, $compLen), $rucksack[$i])) {
                    $sum += $table[$rucksack[$i]];
                    break;
                }
            }
        }
        return $sum;
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $groups = array_chunk($inputData, 3);
        $sum = 0;
        $table = array_combine(array_merge(range('a', 'z'), range('A', 'Z')), range(1, 52));

        foreach ($groups as $group) {
            $len = strlen($group[0]);
            for ($i = 0; $i < $len; $i++) {
                if (str_contains($group[1], $group[0][$i]) && str_contains($group[2], $group[0][$i])) {
                    $sum += $table[$group[0][$i]];
                    break;
                }
            }
        }
        return $sum;
    }
}
