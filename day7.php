<?php

require_once './AbstractBenchmarking.php';

class Day7 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;
    private int $min;
    private int $max;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day7.txt');
        $this->rawTestData = file_get_contents('TestData/day7.txt');
        $this->parseData();
        $this->parseData(true);
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        $inputData = array_map('intval', explode(',', $rawInputData));
        if ($test) {
            $this->testData = $inputData;
        } else {
            $this->data = $inputData;
        }

    }

    public function part1(bool $test = false): int
    {
//        print_r(PHP_EOL . 'Part 1' . PHP_EOL);
        $positions = $test ? $this->testData : $this->data;
        $this->min = min($positions);
        $this->max = max($positions);
        $lowest = PHP_INT_MAX;
        $mean = array_sum($positions) / count($positions);
//        $routeNumber = 0;
        $sum = array_fill($this->min, $this->max - $this->min + 1, 0);
        for ($i = $this->min; $i <= $this->max; $i++) {
            foreach ($positions as $position) {
                $distance = abs($position - $i);
                $sum[$i] += $distance;
                if ($sum[$i] > $lowest && $lowest !== PHP_INT_MAX) {
                    break;
                }
            }
            if ($sum[$i] < $lowest) {
                $lowest = $sum[$i];
                $routeNumber = $i;
            }
        }
//        print_r($lowest . PHP_EOL);
//        print_r($routeNumber . PHP_EOL);
//        print_r(array_sum($positions) . PHP_EOL);
        return $lowest;
    }

    public function part2(bool $test = false): int
    {
//        print_r(PHP_EOL . 'Part 2' . PHP_EOL);
        $positions = $test ? $this->testData : $this->data;
        $this->min = min($positions);
        $this->max = max($positions);
        $lowest = PHP_INT_MAX;
        $computedDistances = [];
        $mean = array_sum($positions) / count($positions);
//        foreach (range(0, $this->max - $this->min + 1) as $i) {
//            $computedDistances[$i] = $i * ($i + 1) / 2;
//        }
        $sum = array_fill($this->min, $this->max - $this->min + 1, 0);
        $floor = floor($mean-0.5);
        $ceiling = ceil($mean+0.5);
        for ($i = $floor; $i <= $ceiling; $i++) {
            foreach ($positions as $position) {
                $distance = abs($i - $position);
                $sum[$i] += $distance * ($distance + 1) / 2;
                if ($sum[$i] > $lowest && $lowest !== PHP_INT_MAX) {
                    break;
                }
            }
            if ($sum[$i] < $lowest) {
                $lowest = $sum[$i];
            }
        }
        return $lowest;
    }
}
