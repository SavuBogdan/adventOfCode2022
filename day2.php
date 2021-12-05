<?php

/**
 *
 */
require_once './AbstractBenchmarking.php';

class Day2 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    private const DOWN = 'down';
    private const UP = 'up';
    private const FORWARD = 'forward';
    private int $x;
    private int $y;
    private int $aim;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day2.txt');
        $this->rawTestData = file_get_contents('TestData/day2.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function part1(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $this->x = 0;
        $this->y = 0;
        foreach ($inputData as $value) {
            if ($value[0] === self::DOWN) {
                $this->y += $value[1];
            } elseif ($value[0] === self::UP) {
                $this->y -= $value[1];
            } elseif ($value[0] === self::FORWARD) {
                $this->x += $value[1];
            }
        }
        return $this->x * $this->y;
    }

    public function part2(bool $test = false): float|int
    {
        $inputData = $test ? $this->testData : $this->data;
        $this->x = 0;
        $this->y = 0;
        $this->aim = 0;
        foreach ($inputData as $value) {
            if ($value[0] === self::DOWN) {
                $this->aim += $value[1];
            } elseif ($value[0] === self::UP) {
                $this->aim -= $value[1];
            } elseif ($value[0] === self::FORWARD) {
                $this->x += $value[1];
                $this->y += $value[1] * $this->aim;
            }
        }
        return $this->x * $this->y;
    }

    public function parseData(bool $test = false)
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        if ($test) {
            $this->testData = explode(PHP_EOL, $rawInputData);
            $this->testData = array_map(fn($item) => explode(' ', $item), $this->testData);
        } else {
            $this->data = explode(PHP_EOL, $rawInputData);
            $this->data = array_map(fn($item) => explode(' ', $item), $this->data);
        }
    }
}