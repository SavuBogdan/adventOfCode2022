<?php

/**
 *
 */
require_once './AbstractBenchmarking.php';

class Day3 extends AbstractBenchmarking
{
    private array $data = [];
    private array $testData = [];
    private string $rawData;
    private string $rawTestData;
    private array $dataBitSum = [];
    private string $gamma = '';
    private string $epsilon = '';
    private int $bitSize;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day3.txt');
        $this->rawTestData = file_get_contents('TestData/day3.txt');
        $this->parseData(true);
        $this->parseData();

    }

    public function part1(bool $test = false)
    {
        $inputData = $test ? $this->testData : $this->data;
        $this->bitSize = count($inputData[0]);
        $dataCount = count($inputData) / 2;
        $this->dataBitSum = array_fill(0, $this->bitSize, 0);
        foreach ($inputData as $value) {
            for ($i = 0; $i < $this->bitSize; $i++) {
                $this->dataBitSum[$i] += $value[$i];
            }
        }
        foreach ($this->dataBitSum as $value) {
            if ($value > $dataCount) {
                $this->gamma .= '1';
                $this->epsilon .= '0';
            } else {
                $this->gamma .= '0';
                $this->epsilon .= '1';
            }
        }

        return (bindec($this->gamma)) * (bindec($this->epsilon));
    }

    public function part2(bool $test = false)
    {
        $inputData = $test ? $this->testData : $this->data;
        $this->bitSize = count($inputData[0]);
        $co2 = $inputData;
        $o = $inputData;
        $this->dataBitSum = array_fill(0, $this->bitSize, 0);
        foreach ($inputData as $value) {
            for ($i = 0; $i < $this->bitSize; $i++) {
                $this->dataBitSum[$i] += $value[$i];
            }
        }
        $co2DataBitSum = $this->dataBitSum;
        $oDataBitSum = $this->dataBitSum;
        $co2Index = 0;
        $oIndex = 0;
        while (count($co2) > 1 || count($o) > 1) {
            $this->removeOxygenBinaries($o, $oDataBitSum, $oIndex);
            $this->removeCO2Binaries($co2, $co2DataBitSum, $co2Index);
        }
        return bindec(implode(array_values($o)[0])) * bindec(implode(array_values($co2)[0]));
    }

    /**
     * @param array $o
     * @param array $oDataBitSum
     * @param int $oIndex
     */
    public function removeOxygenBinaries(array &$o, array &$oDataBitSum, int &$oIndex)
    {
        if (count($o) > 1) {
            $bitToRemove = $oDataBitSum[$oIndex] >= count($o) / 2 ? 0 : 1;
            foreach ($o as $key => $value) {
                if ($value[$oIndex] == $bitToRemove && count($o) > 1) {
                    foreach ($value as $bitKey => $bit) {
                        $oDataBitSum[$bitKey] -= $bit;
                    }
                    unset($o[$key]);
                }
            }
            $oIndex++;
        }
    }

    /**
     * @param array $co2
     * @param array $co2DataBitSum
     * @param int $co2Index
     */
    public function removeCO2Binaries(array &$co2, array &$co2DataBitSum, int &$co2Index)
    {
        if (count($co2) > 1) {
            $bitToRemove = $co2DataBitSum[$co2Index] < count($co2) / 2 ? 0 : 1;
            foreach ($co2 as $key => $value) {
                if ($value[$co2Index] == $bitToRemove && count($co2) > 1) {
                    foreach ($value as $bitKey => $bit) {
                        $co2DataBitSum[$bitKey] -= $bit;
                    }
                    unset($co2[$key]);
                }
            }
            $co2Index++;
        }
    }

    public function parseData(bool $test = false)
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        if ($test) {
            $this->testData = array_map('str_split', explode(PHP_EOL, $rawInputData));
        } else {
            $this->data = array_map('str_split', explode(PHP_EOL, $rawInputData));
        }

    }
}