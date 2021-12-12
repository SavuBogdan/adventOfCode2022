<?php

use JetBrains\PhpStorm\Pure;

require_once './AbstractBenchmarking.php';

class Decoder
{

    public $groupedBySize = [];

    public $zero = false;
    public $one = false;
    public $two = false;
    public $three = false;
    public $four = false;
    public $five = false;
    public $six = false;
    public $seven = false;
    public $eight = false;
    public $nine = false;



    public function addEncodedSegment(array $segments)
    {
        foreach ($segments as $segment) {
            $this->groupedBySize[count($segment)][] = $segment;
        }
    }

    public function decode()
    {
        $this->one = $this->groupedBySize[2][0];
        $this->seven = $this->groupedBySize[3][0];
        $this->eight = $this->groupedBySize[7][0];
        $this->four = $this->groupedBySize[4][0];
        $this->findTwo();
        $this->findThreeAndFive();
        $this->findNine();
        $this->findZero();


    }

    #[Pure] public function returnMappedDigits(): array
    {
        return [
            $this->weightLetters($this->zero) => 0,
            $this->weightLetters($this->one) => 1,
            $this->weightLetters($this->two) => 2,
            $this->weightLetters($this->three) => 3,
            $this->weightLetters($this->four) => 4,
            $this->weightLetters($this->five) => 5,
            $this->weightLetters($this->six) => 6,
            $this->weightLetters($this->seven) => 7,
            $this->weightLetters($this->eight) => 8,
            $this->weightLetters($this->nine) => 9,
        ];
    }

    public function weightLetters(array $letters): int
    {
        $total = 0;
        foreach ($letters as $letter) {
            $total += match ($letter) {
                'a' => 89,
                'b' => 13367,
                'c' => 397,
                'd' => 569,
                'e' => 281,
                'f' => 691,
                'g' => 1129,
            };
        }
        return $total;
    }


    private function findZero()
    {
        foreach ($this->groupedBySize[6] as $key => $digit) {
            if (count(array_intersect($digit, $this->seven)) == 3) {
                $this->zero = $digit;
                unset($this->groupedBySize[6][$key]);
            } else {
                $this->six = $digit;
                unset($this->groupedBySize[6][$key]);
            }
        }

    }

    private function findTwo()
    {
        foreach ($this->groupedBySize[5] as $key => $digit) {
            if (count(array_intersect($digit, $this->groupedBySize[4][0])) == 2) {
                $this->two = $digit;
                unset($this->groupedBySize[5][$key]);
            }
        }
    }


    private function findThreeAndFive()
    {
        foreach ($this->groupedBySize[5] as $key => $digit) {
            if (count(array_intersect($digit, $this->two)) == 4) {
                $this->three = $digit;
                unset($this->groupedBySize[5][$key]);
            } else {
                $this->five = $digit;
                unset($this->groupedBySize[5][$key]);
            }

        }
    }

    private function findNine()
    {
        foreach ($this->groupedBySize[6] as $key => $digit) {

            if (
                is_array($this->five) &&
                is_array($this->four) &&
                array_count_values(array_unique(array_merge($this->five, $this->four), SORT_REGULAR)) == array_count_values($digit)
            ) {
                $this->nine = $digit;
                unset($this->groupedBySize[6][$key]);
            }
        }
    }


}


class Day8 extends AbstractBenchmarking
{
    private array $data;
    private array $testData;
    private string $rawData;
    private string $rawTestData;

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day8.txt');
        $this->rawTestData = file_get_contents('TestData/day8.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function parseData(bool $test = false): void
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        if ($test) {
            $this->testData = explode(PHP_EOL, $rawInputData);
        } else {
            $this->data = explode(PHP_EOL, $rawInputData);
        }
    }

    public function part1(bool $test = false): int
    {
        $str = $test ? $this->rawTestData : $this->rawData;
        $pattern = '/(?:.+\| )(?:(\w{2} |\w{4} |\w{3} |\w{7} )|(?:(?:\w{1} )|(?:\w{5} )|(?:\w{6} )|(?:\w{8} )))(?:(\w{2} |\w{4} |\w{3} |\w{7} )|(?:(?:\w{1} )|(?:\w{5} )|(?:\w{6} )|(?:\w{8} )))(?:(\w{2} |\w{4} |\w{3} |\w{7} )|(?:(?:\w{1} )|(?:\w{5} )|(?:\w{6} )|(?:\w{8} )))(?:(\w{2}(?:\n|$)|\w{4}(?:\n|$)|\w{3}(?:\n|$)|\w{7}(?:\n|$))|(?:(?:\w{1}(?:\n|$))|(?:\w{5}(?:\n|$))|(?:\w{6}(?:\n|$))|(?:\w{8}(?:\n|$))))/';
        preg_match_all($pattern, $str, $matches);
        return array_reduce(array_splice($matches, 1), fn($carry, $groupMatches) => $carry += count(array_filter($groupMatches)), 0);
    }


    public function part2(bool $test = false): int
    {

        $inputData = $test ? $this->testData : $this->data;
        $sum = 0;
        foreach ($inputData as $lineData) {
            $lineData = array_map('trim', explode(' | ', $lineData));
            $segments = array_map('str_split', explode(' ', $lineData[0]));
            $decoder = new Decoder();
            $decoder->addEncodedSegment($segments);
            $decoder->decode();
            $mappedDigits = $decoder->returnMappedDigits();
            $number = 0;
            foreach (explode(' ', $lineData[1]) as $output) {
                $weightedNumber = $mappedDigits[$decoder->weightLetters(str_split($output))];
                $number = $number * 10 + $weightedNumber;
            }
            $sum += $number;
        }
        return $sum;
    }
}
