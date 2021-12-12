<?php

require_once './AbstractBenchmarking.php';


class FishSchool
{
    public int $lanternFishCount = 0;
    public array $lanternFish = [];


    protected function simulateDay(): array
    {
        $spawningFish = array_shift($this->lanternFish);
        $this->lanternFish[6] += $spawningFish;
        $this->lanternFish[] = $spawningFish;
        return $this->lanternFish;
    }

    /**
     * @param $data
     */
    protected function addLanternFish($data)
    {
        $this->lanternFish = array_fill(0, 9, 0);

        foreach ($data as $age) {
            $this->lanternFish[$age]++;
        }
    }

    /**
     * @param int $days
     * @param mixed $data
     */
    public function simulateReplication(int $days, mixed $data)
    {
        $this->addLanternFish($data);
        for ($i = 0; $i < $days; $i++) {
            $this->simulateDay();
        }
        $this->lanternFishCount = array_sum($this->lanternFish);
    }
}

class Day6 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day6.txt');
        $this->rawTestData = file_get_contents('TestData/day6.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        if ($test) {
            $this->testData = explode(',', $rawInputData);
        } else {
            $this->data = explode(',', $rawInputData);
        }
    }

    public function part1(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $fishSchool = new FishSchool();
        $fishSchool->simulateReplication(80, $inputData);
        return $fishSchool->lanternFishCount;

    }

    public function part2(bool $test = false): int
    {
        $inputData = $test ? $this->testData : $this->data;
        $fishSchool = new FishSchool();
        $fishSchool->simulateReplication(256, $inputData);
        return $fishSchool->lanternFishCount;
    }
}
