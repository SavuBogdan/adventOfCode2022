<?php

/**
 *
 */
require_once './AbstractBenchmarking.php';

class Plane
{
    public array $coords = [];
    public array $overlapsCount = [];

    public function __construct()
    {
        $arrayRow = array_fill(0, 1000, 0);
        $this->coords = array_fill(0, 1000, $arrayRow);
    }

    public function drawLineP1(int $x1,int $y1,int $x2,int $y2)
    {
        if ($this->drawVerticalLine($x1,$y1, $x2, $y2)) {
            return;
        }
        $this->drawHorizontalLine($x1,$y1, $x2, $y2);
    }

    public function drawLineP2($x1,$y1, $x2, $y2)
    {
        if ($this->drawVerticalLine($x1,$y1, $x2, $y2)) {
            return;
        }
        if ($this->drawHorizontalLine($x1,$y1, $x2, $y2)) {
            return;
        }
        $this->drawDiagonalLine($x1,$y1, $x2, $y2);
    }

    public function trackOverlapCount(int $y, int $x): void
    {
        if (isset($this->overlapsCount[$this->coords[$y][$x]])) {
            $this->overlapsCount[$this->coords[$y][$x]]++;
        } else {
            $this->overlapsCount[$this->coords[$y][$x]] = 1;
        }
    }

    public function drawVerticalLine(int $x1,int $y1,int $x2,int $y2): bool
    {
        if ($x1 == $x2) {
            $x = $x1;
            if ($y1 < $y2) {
                for ($y = $y1; $y <= $y2; $y++) {
                    $this->coords[$y][$x]++;
                    $this->trackOverlapCount($y, $x);
                }
            } else {
                for ($y = $y2; $y <= $y1; $y++) {
                    $this->coords[$y][$x]++;
                    $this->trackOverlapCount($y, $x);
                }
            }
            return true;
        }
        return false;
    }

    public function drawHorizontalLine(int $x1,int $y1,int $x2,int $y2): bool
    {
        if ($y1 == $y2) {
            $y = $y1;
            if ($x1 < $x2) {
                for ($x = $x1; $x <= $x2; $x++) {
                    $this->coords[$y][$x]++;
                    $this->trackOverlapCount($y, $x);
                }
            } else {
                for ($x = $x2; $x <= $x1; $x++) {
                    $this->coords[$y][$x]++;
                    $this->trackOverlapCount($y, $x);
                }
            }
            return true;
        }
        return false;
    }

    private function drawDiagonalLine(int $x1,int $y1,int $x2,int $y2)
    {
        $slope = $this->calculateSlopeOfLine($x1,$y1, $x2, $y2);
        if ($slope == 1) {
            if ($x1 < $x2) {
                for ($x = $x1; $x <= $x2; $x++) {
                    $y = $y1 + ($x - $x1);
                    $this->coords[$y][$x]++;
                    $this->trackOverlapCount($y, $x);
                }
            } else {
                for ($x = $x2; $x <= $x1; $x++) {
                    $y = $y2 + ($x - $x2);
                    $this->coords[$y][$x]++;
                    $this->trackOverlapCount($y, $x);
                }
            }
            return;
        }
        if ($slope == -1) {
            if ($x1 < $x2) {
                for ($x = $x1; $x <= $x2; $x++) {
                    $y = $y1 - ($x - $x1);
                    $this->coords[$y][$x]++;
                    $this->trackOverlapCount($y, $x);
                }
            } else {
                for ($x = $x2; $x <= $x1; $x++) {
                    $y = $y2 - ($x - $x2);
                    $this->coords[$y][$x]++;
                    $this->trackOverlapCount($y, $x);
                }
            }
        }

    }

    private function calculateSlopeOfLine(int $x1,int $y1,int $x2,int $y2): float
    {
        return ($y2 - $y1) / ($x2 - $x1);
    }
}


class Day5 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day5.txt');
        $this->rawTestData = file_get_contents('TestData/day5.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;

        foreach (explode(PHP_EOL, $rawInputData) as $value) {
            preg_match('/(\d+),(\d+) -> (\d+),(\d+)$/', $value, $matches);
            $lineCoords = array_map('intval', array_slice($matches, 1, 4));
            if ($test) {
                $this->testData[] = $lineCoords;
            } else {
                $this->data[] = $lineCoords;
            }
        }
    }

    public function part1(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $plane = new Plane();
        foreach ($inputData as $line) {
            $plane->drawLineP1($line[0], $line[1],$line[2], $line[3]);
        }
        return $plane->overlapsCount[2];
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $plane = new Plane();
        foreach ($inputData as $line) {
            $plane->drawLineP2($line[0], $line[1],$line[2], $line[3]);
        }
        return $plane->overlapsCount[2];
    }
}