<?php

require_once './AbstractBenchmarking.php';

class Day9 extends AbstractBenchmarking
{
    private array $data = [];
    private array $testData = [];
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day9.txt');
        $this->rawTestData = file_get_contents('TestData/day9.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        foreach (explode(PHP_EOL, $rawInputData) as $value) {
            $paths = str_split($value);
            $lineInput = array_map('intval', $paths);
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
        $points = $this->findPoints($inputData);
        return $this->calculateRiskLevel($inputData, $points);
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $points = $this->findPoints($inputData);
        $basins = $this->findLargestBasins($inputData, $points);
        return array_product($basins);
    }

    // find points in 2D array that are lower than all the neighbours if the neighbours exist
    public function findPoints(array $data): array
    {
        $points = [];
        foreach ($data as $y => $row) {
            foreach ($row as $x => $value) {
                $neighbours = $this->getNeighbours($data, $x, $y);
                if ($neighbours) {
                    $neighbourValues = array_map(function ($neighbour) use ($data) {
                        return $data[$neighbour[1]][$neighbour[0]];
                    }, $neighbours);
                    if (min($neighbourValues) > $value) {
                        $points[] = [$x, $y];
                    }
                }
            }
        }
        return $points;
    }

    // function that gets the neighbours of a point if they exist
    public function getNeighbours(array $data, int $x, int $y): array
    {
        $neighbours = [];
        if ($x > 0) {
            $neighbours[] = [$x - 1, $y];
        }
        if ($x < count($data[0]) - 1) {
            $neighbours[] = [$x + 1, $y];
        }
        if ($y > 0) {
            $neighbours[] = [$x, $y - 1];
        }
        if ($y < count($data) - 1) {
            $neighbours[] = [$x, $y + 1];
        }
        return $neighbours;
    }

    // a function that calculates risk level of a point given the points. The risk level of a low point is 1 plus its value
    public function calculateRiskLevel(array $data, array $points): int
    {
        $riskLevel = 0;
        foreach ($points as $point) {
            $riskLevel += $data[$point[1]][$point[0]] + 1;
        }
        return $riskLevel;
    }

    // Given the lowest points on the map find the largest 3 basins around them that are delimited by borders of 9
    // return only the top 3 basins by number of points then multiply their point counts
    public function findLargestBasins(array $data, array $points): array
    {
        $basins = [];
        foreach ($points as $point) {
            $basin = $this->getBasin($data, $point);
            if ($basin) {
                $basins[] = $basin;
            }
        }
        $basins = array_map(function ($basin) {
            return count($basin);
        }, $basins);
        array_multisort($basins, SORT_DESC);
        return array_slice($basins, 0, 3);
    }

    // starting from a point expand the basin of points that are lower than 9 or the borders of the map
    // stop when the basin is delimited by the borders of the map or by points of value 9
    // do not visit the same point twice
    private function getBasin(array $data, mixed $point): array
    {
        $visited = [];
        $queue = [$point];
        while ($queue) {
            $current = array_shift($queue);
            if (!in_array($current, $visited)) {
                $visited[] = $current;
                $neighbours = $this->getNeighbours($data, $current[0], $current[1]);
                foreach ($neighbours as $neighbour) {
                    if (!in_array($neighbour, $visited) && $data[$neighbour[1]][$neighbour[0]] < 9) {
                        $queue[] = $neighbour;
                    }
                }
            }
        }
        return $visited;
    }

}
