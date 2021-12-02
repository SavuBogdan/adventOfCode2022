<?php

abstract class AbstractBenchmarking
{
    private const MULTIPLIER_MICROSECONDS = 1000000;

    abstract public function part1();

    abstract public function part2();

    public function benchmark($numberOfRuns): array
    {
        $bestTimePart1 = PHP_INT_MAX;
        $bestTimePart2 = PHP_INT_MAX;
        for ($i = 0; $i < $numberOfRuns; $i++) {
            $start = round(microtime(true) * self::MULTIPLIER_MICROSECONDS);
            $this->part1();
            $end = round(microtime(true) * self::MULTIPLIER_MICROSECONDS);
            $time = $end - $start;
            if ($time < $bestTimePart1) {
                $bestTimePart1 = $time;
            }
        }

        for ($i = 0; $i < $numberOfRuns; $i++) {
            $start = round(microtime(true) * self::MULTIPLIER_MICROSECONDS);
            $this->part2();
            $end = round(microtime(true) * self::MULTIPLIER_MICROSECONDS);
            $time = $end - $start;
            if ($time < $bestTimePart2) {
                $bestTimePart2 = $time;
            }
        }
        return [$bestTimePart1, $bestTimePart2];
    }
}