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

        $maxCalories = 0;
        $currentMax = 0;

        foreach ($inputData as $entry) {
            if (empty($entry)) {
                if ($currentMax > $maxCalories) {
                    $maxCalories = $currentMax;
                }
                $currentMax = 0;
            } else {
                $currentMax += $entry[0];
            }
        }

        return $maxCalories;
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;

        $max1 = 0;
        $max2 = 0;
        $max3 = 0;
        $currentMax = 0;

        foreach ($inputData as $entry) {

            if (empty($entry)) {
                $this->updateMax($max1, $max2, $max3, $currentMax);

                $currentMax = 0;
            } else {
                $currentMax += $entry[0];
            }
        }

        if ($currentMax > 0) {
            $this->updateMax($max1, $max2, $max3, $currentMax);
        }

        return $max1 + $max2 + $max3;
    }

    /**
     * @param mixed $max1
     * @param mixed $max2
     * @param mixed $max3
     * @param mixed $currentMax
     * @return array
     */
    protected function updateMax(mixed &$max1, mixed &$max2, mixed &$max3, mixed $currentMax): array
    {
        $min = min([$max1, $max2, $max3]);
        if ($currentMax > $min) {
            if ($min === $max1) {
                $max1 = $currentMax;
            } elseif ($min === $max2) {
                $max2 = $currentMax;
            } elseif ($min === $max3) {
                $max3 = $currentMax;
            }
        }
        return array($max1, $max2, $max3);
    }
}