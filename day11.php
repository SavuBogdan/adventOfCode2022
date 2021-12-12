<?php

require_once './AbstractBenchmarking.php';

class Cavern
{
    /**
     * @var $dumboOctopuses DumboOctopus[]
     */
    public array $dumboOctopuses = [];
    public int $steps = 0;
    public int $flashCount = 0;
    public int $currentStepFlashCount = 0;

    public function __construct(array $data)
    {
        foreach ($data as $id => $energy) {
            $octopus = new DumboOctopus($id, $energy);
            $octopus->cavern = $this;
            $this->dumboOctopuses[$id] = $octopus;
        }
        foreach ($this->dumboOctopuses as $id => $dumboOctopus) {
            if ($this->dumboOctopuses[$id] instanceof DumboOctopus) {
                $this->assignNeighbours($this->dumboOctopuses[$id], $data);
            }
        }
    }

    public function display()
    {
        sort($this->dumboOctopuses);
        $output = "\n\n\n";
        foreach ($this->dumboOctopuses as $id => $dumboOctopus) {
            $output .= $dumboOctopus->energy;
            if ($id % 10 == 9) {
                $output .= "\n";
            }
        }
        echo $output . "\n\n\n";
    }

    public function simulateSteps(int $steps = null)
    {
        if ($steps === null) {
            while ($this->currentStepFlashCount !== 100) {
                $this->increaseStep();
            }
            return;
        }
        for ($i = 0; $i < $steps; $i++) {
            $this->increaseStep();
        }
    }

    public function increaseStep()
    {
        $this->steps++;
        $this->currentStepFlashCount = 0;
        array_walk($this->dumboOctopuses, function (DumboOctopus $dumboOctopus) {
            $dumboOctopus->increaseEnergy($this->steps);
        });
    }

    public function assignNeighbours(DumboOctopus &$octopus, array $data)
    {
        $neighbours = [];
        $id = $octopus->id;
//        echo 'Adding neighbours for id '. $id . PHP_EOL;


        if ($id % 10 == 0 && $id < 10) {
            $neighbours[] = $id + 1;
            $neighbours[] = $id + 10;
            $neighbours[] = $id + 11;

        } elseif ($id % 10 == 0 && $id > 89) {
            $neighbours[] = $id - 10;
            $neighbours[] = $id - 9;
            $neighbours[] = $id + 1;
        } elseif ($id % 10 == 9 && $id < 10) {
            $neighbours[] = $id - 1;
            $neighbours[] = $id + 9;
            $neighbours[] = $id + 10;
        } elseif ($id % 10 == 9 && $id > 89) {
            $neighbours[] = $id - 11;
            $neighbours[] = $id - 10;
            $neighbours[] = $id - 1;
        } elseif ($id % 10 == 9 && $id > 9 && $id < 90) {
            $neighbours[] = $id - 11;
            $neighbours[] = $id - 10;
            $neighbours[] = $id - 1;
            $neighbours[] = $id + 9;
            $neighbours[] = $id + 10;
        } elseif ($id % 10 == 0 && $id > 9 && $id < 90) {
            $neighbours[] = $id - 10;
            $neighbours[] = $id - 9;
            $neighbours[] = $id + 1;
            $neighbours[] = $id + 10;
            $neighbours[] = $id + 11;
        } elseif ($id > 0 && $id < 9) {
            $neighbours[] = $id - 1;
            $neighbours[] = $id + 1;
            $neighbours[] = $id + 9;
            $neighbours[] = $id + 10;
            $neighbours[] = $id + 11;
        } elseif ($id > 90 && $id < 99) {
            $neighbours[] = $id - 11;
            $neighbours[] = $id - 10;
            $neighbours[] = $id - 9;
            $neighbours[] = $id - 1;
            $neighbours[] = $id + 1;
        } else {
            $neighbours[] = $id - 11;
            $neighbours[] = $id - 10;
            $neighbours[] = $id - 9;
            $neighbours[] = $id - 1;
            $neighbours[] = $id + 1;
            $neighbours[] = $id + 9;
            $neighbours[] = $id + 10;
            $neighbours[] = $id + 11;
        }

        foreach ($neighbours as $neighbour) {
            if (isset($data[$neighbour])) {
                $octopus->addNeighbour($this->dumboOctopuses[$neighbour]);
            }
        }

    }
}


class DumboOctopus
{
    public int $id;
    public int $energy;
    public int $lastFlash;
    public int $flashCount = 0;
    /**
     * @var $neighbors DumboOctopus[]
     */
    public array $neighbors = [];
    public Cavern $cavern;

    public function __construct($id, $energy)
    {
        $this->id = $id;
        $this->energy = $energy;
        $this->lastFlash = 0;
    }

    public function increaseEnergy($stepNumber)
    {
        if ($stepNumber != $this->lastFlash) {
            $this->energy++;
            if ($this->shouldFlash()) {
                $this->flash($stepNumber);
            }
        }
    }

    private function shouldFlash(): bool
    {
        if ($this->energy > 9) {
            return true;
        }
        return false;
    }

    private function flash($stepNumber)
    {
        $this->flashCount++;
        $this->cavern->flashCount++;
        $this->cavern->currentStepFlashCount++;
        $this->lastFlash = $stepNumber;
        foreach ($this->neighbors as $neighbor) {
            $neighbor->increaseEnergy($stepNumber);
        }
        $this->energy = 0;
    }

    public function addNeighbour(DumboOctopus $neighbour)
    {
        $this->neighbors[$neighbour->id] = $neighbour;
    }


}

class Day11 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day11.txt');
        $this->rawTestData = file_get_contents('TestData/day11.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        foreach (explode(PHP_EOL, $rawInputData) as $row => $value) {
            foreach (str_split($value) as $column => $char) {
                if ($test) {
                    $this->testData[] = intval($char);
                } else {
                    $this->data[] = intval($char);
                }
            }
        }
    }

    public function part1(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $cavern = new Cavern($inputData);
        $cavern->simulateSteps(100000);
        return $cavern->flashCount;
    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $cavern = new Cavern($inputData);
        $cavern->simulateSteps();
        return $cavern->steps;
    }
}

$day11 = new Day11();
$day11->part2(false);