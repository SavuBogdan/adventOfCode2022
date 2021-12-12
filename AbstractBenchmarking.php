<?php

abstract class AbstractBenchmarking
{
    private const MULTIPLIER_NANO_SECONDS = 1000000000;

    abstract public function parseData(bool $test = false);


    abstract public function part1(bool $test = false);

    abstract public function part2(bool $test = false);

    public function test()
    {
        return [
            $this->part1(true),
            $this->part2(true),
        ];
    }

    public function benchmarkParseData(): array
    {
        $bestTimeParse = PHP_INT_MAX;
        for ($i = 0; $i < 100; $i++) {
            $start = round(microtime(true) * self::MULTIPLIER_NANO_SECONDS);
            $this->parseData();
            $end = round(microtime(true) * self::MULTIPLIER_NANO_SECONDS);
            $time = $end - $start;
            if ($time < $bestTimeParse) {
                $bestTimeParse = $time;
            }
        }
        return $this->getTimeAndNotationForOutput($bestTimeParse);
    }

    public function benchmark($numberOfRuns): array
    {
        $bestTimePart1 = PHP_INT_MAX;
        $bestTimePart2 = PHP_INT_MAX;
        for ($i = 0; $i < $numberOfRuns; $i++) {
            $start = round(microtime(true) * self::MULTIPLIER_NANO_SECONDS);
            $this->part1();
            $end = round(microtime(true) * self::MULTIPLIER_NANO_SECONDS);
            $time = $end - $start;
            if ($time < $bestTimePart1) {
                $bestTimePart1 = $time;
            }
        }

        for ($i = 0; $i < $numberOfRuns; $i++) {
            $start = round(microtime(true) * self::MULTIPLIER_NANO_SECONDS);
            $this->part2();
            $end = round(microtime(true) * self::MULTIPLIER_NANO_SECONDS);
            $time = $end - $start;
            if ($time < $bestTimePart2) {
                $bestTimePart2 = $time;
            }
        }

        return [
            $this->getTimeAndNotationForOutput($bestTimePart1),
            $this->getTimeAndNotationForOutput($bestTimePart2)
        ];
    }

    private function getTimeAndNotationForOutput($ns): array
    {
        $time = $ns / self::MULTIPLIER_NANO_SECONDS;
        $notation = 's';
        if ($time < 1) {
            $time *= 1000;
            $notation = 'ms';
        }
        if ($time < 1) {
            $time *= 1000;
            $notation = 'μs';
        }
        if ($time < 1) {
            $time *= 1000;
            $notation = 'ns';
        }

        return [number_format(round($time, 3),3), $notation, $ns];
    }
}