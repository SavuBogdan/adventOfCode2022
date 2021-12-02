<?php

/**
 *
 */
require_once './AbstractBenchmarking.php';

class Day1 extends AbstractBenchmarking
{
    public function __construct()
    {
        $this->data = array_map('intval', explode(PHP_EOL, file_get_contents('Data/day1.txt')));
    }

    public function part1(): int
    {
        $count = 0;
        $previousValue = $this->data[0];

        foreach ($this->data as $value) {
            if ($previousValue < $value) {
                $count++;
            }
            $previousValue = $value;
        }
        return $count;
    }

    public function part2(): int
    {
        $count = 0;
        $previousGroup = $this->data[0] + $this->data[1] + $this->data[2];

        for ($i = 2; $i < count($this->data) - 2; $i++) {
            $currentGroup = $this->data[$i] + $this->data[$i + 1] + $this->data[$i + 2];
            if ($previousGroup < $currentGroup) {
                $count++;
            }
            $previousGroup = $currentGroup;
        }
        return $count;
    }
}