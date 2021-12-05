<?php

/**
 *
 */

use JetBrains\PhpStorm\Pure;

require_once './AbstractBenchmarking.php';


class Line
{
    private array $numbers = [];
    public bool $bingo = false;
    private int $markedNumbers = 0;

    public function addNumber(int $number): void
    {
        $this->numbers[$number] = false;
    }

    public function markNumber(int $number): void
    {
        if (isset($this->numbers[$number])) {
            $this->markedNumbers++;
            if ($this->markedNumbers === 5) {
                $this->bingo = true;
            }
        }
    }
}

class BingoBoard
{
    /**
     * @var Line[]
     */
    private array $rows = [];
    private int $currentRow = 0;
    /**
     * @var Line[]
     */
    private array $columns = [];
    private array $unmarkedNumbers = [];
    public int $unmarkedNumbersSum = 0;
    public int $lastNumber = 0;
    public int $playedNumbers = 0;
    public bool $isBingo = false;

    #[Pure] public function __construct()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->rows[] = new Line();
            $this->columns[] = new Line();
        }
    }


    public function addBoardNumbers(array $boardNumbers): void
    {
        foreach ($boardNumbers as $boardNumber) {
            $this->unmarkedNumbers[$boardNumber] = false;
            $this->unmarkedNumbersSum += $boardNumber;
        }
    }


    public function addRow(string $param)
    {
        $rowNumbers = array_map('intval', str_split($param, 3));
        foreach ($rowNumbers as $rowNumber) {
            $this->rows[$this->currentRow]->addNumber($rowNumber);
        }
        $this->currentRow++;
        for ($i = 0; $i < 5; $i++) {
            $this->columns[$i]->addNumber($rowNumbers[$i]);
        }
        $this->addBoardNumbers($rowNumbers);
    }

    public function playBingo(array $numbers)
    {
        foreach ($numbers as $number) {
            if (!$this->isBingo) {
                $this->playNumber($number);
            }
        }
    }

    private function playNumber(int $number)
    {
        if (isset($this->unmarkedNumbers[$number])) {
            $this->lastNumber = $number;
            $this->unmarkedNumbersSum -= $number;
            unset($this->unmarkedNumbers[$number]);
            foreach ($this->rows as $row) {
                $row->markNumber($number);
                if ($row->bingo) {
                    $this->isBingo = true;
                }
            }
            foreach ($this->columns as $column) {
                $column->markNumber($number);
                if ($column->bingo) {
                    $this->isBingo = true;
                }
            }
        }
        $this->playedNumbers++;
    }
}


class Day4 extends AbstractBenchmarking
{
    private array $data = [];
    private array $testData = [];
    private string $rawData;
    private string $rawTestData;
    private array $numbersDrawn = [];

    public function __construct()
    {
        $this->rawData = file_get_contents('Data/day4.txt');
        $this->rawTestData = file_get_contents('TestData/day4.txt');
        $this->parseData(true);
        $this->parseData();
    }

    public function part1(bool $test = false): float|int
    {
        $inputData = $test ? $this->testData : $this->data;
        $this->numbersDrawn = array_map('intval', explode(',', $inputData[0]));
        $winningBoard = $this->generateBoardsPart1(array_slice($inputData, 1), $this->numbersDrawn);
        return $winningBoard->unmarkedNumbersSum * $winningBoard->lastNumber;
    }

    public function part2(bool $test = false): float|int
    {
        $inputData = $test ? $this->testData : $this->data;
        $this->numbersDrawn = array_map('intval', explode(',', $inputData[0]));
        $worstBoard = $this->generateBoardsPart2(array_slice($inputData, 1), $this->numbersDrawn);
        return $worstBoard->unmarkedNumbersSum * $worstBoard->lastNumber;
    }

    private function generateBoardsPart1(array $rows, array $numbers): BingoBoard
    {
        $winningBoard = false;
        $rowCount = count($rows);
        for ($i = 0; $i < $rowCount; $i = $i + 5) {

            $board = new BingoBoard();
            for ($j = $i; $j < $i + 5; $j++) {
                $board->addRow($rows[$j]);
            }
            $board->playBingo($numbers);
            if (!$winningBoard || ($board->isBingo && $board->playedNumbers < $winningBoard->playedNumbers)) {
                $winningBoard = $board;
            }
        }
        return $winningBoard;
    }

    private function generateBoardsPart2(array $rows, array $numbers): BingoBoard
    {
        $worstBoard = false;
        $rowCount = count($rows);
        for ($i = 0; $i < $rowCount; $i = $i + 5) {

            $board = new BingoBoard();
            for ($j = 0; $j < 5; $j++) {
                $board->addRow($rows[$j + $i]);
            }
            $board->playBingo($numbers);
            if (!$worstBoard || ($board->isBingo && $board->playedNumbers >= $worstBoard->playedNumbers)) {
                $worstBoard = $board;
            }
        }
        return $worstBoard;
    }

    public function parseData(bool $test = false)
    {
        $rawInputData = $test ? $this->rawTestData : $this->rawData;
        if ($test) {
            $this->testData = explode(PHP_EOL, $rawInputData);
        } else {
            $this->data = explode(PHP_EOL, $rawInputData);
        }
    }
}
