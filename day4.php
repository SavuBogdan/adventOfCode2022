<?php

/**
 *
 */

use JetBrains\PhpStorm\Pure;

require_once './AbstractBenchmarking.php';


class Line
{
    private array $numbers;
    private bool $bingo = false;
    private int $markedNumbers = 0;

    public function addNumber(int $number): void
    {
        $this->numbers[$number] = false;
    }

    public function markNumber(int $number): void
    {
        if (isset($this->numbers[$number])) {
            $this->numbers[$number] = true;
            $this->markedNumbers++;
            if ($this->markedNumbers === 5) {
                $this->bingo = true;
            }
        }
    }

    /**
     * @return bool
     */
    public function isBingo(): bool
    {
        return $this->bingo;
    }

    /**
     * @return array
     */
    public function getNumbers(): array
    {
        return $this->numbers;
    }

}

class BingoBoard
{
    private int $id;
    /**
     * @var Line[]
     */
    private array $rows = [];
    private int $currentRow = 0;
    /**
     * @var Line[]
     */
    private array $columns = [];
    private array $markedNumbers = [];
    private array $unmarkedNumbers = [];
    private int $playedNumbers = 0;
    public bool $isBingo = false;

    public function __construct($id)
    {
        $this->id = $id;
        $this->initRowsAndColumns();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getUnmarkedNumbers(): array
    {
        return $this->unmarkedNumbers;
    }

    public function addBoardNumbers(array $boardNumbers): void
    {
        foreach ($boardNumbers as $boardNumber) {
            $this->unmarkedNumbers[] = $boardNumber;
        }
    }

    /**
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param array $rows
     */
    public function setRows(array $rows): void
    {
        $this->rows = $rows;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getMarkedNumbers(): array
    {
        return $this->markedNumbers;
    }

    /**
     * @param array $markedNumbers
     */
    public function setMarkedNumbers(array $markedNumbers): void
    {
        $this->markedNumbers = $markedNumbers;
    }

    /**
     * @return int
     */
    public function getPlayedNumbers(): int
    {
        return $this->playedNumbers;
    }

    public function addRow(string $param)
    {
        $rowNumbers = array_map('intval', str_split($param, 3));
        foreach ($rowNumbers as $rowNumber) {
            $this->rows[$this->currentRow]->addNumber($rowNumber);
        }
        $this->currentRow++;
        $this->columns[0]->addNumber($rowNumbers[0]);
        $this->columns[1]->addNumber($rowNumbers[1]);
        $this->columns[2]->addNumber($rowNumbers[2]);
        $this->columns[3]->addNumber($rowNumbers[3]);
        $this->columns[4]->addNumber($rowNumbers[4]);
        $this->addBoardNumbers($rowNumbers);
//        var_dump("added row $param to board $this->id");
//        var_dump($rowNumbers);
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
        if (in_array($number, $this->unmarkedNumbers)) {
//            if (in_array($number, $this->un)) {
//            }
            $this->markedNumbers[] = $number;
            $this->unmarkedNumbers = array_diff($this->unmarkedNumbers, [$number]);
            foreach ($this->rows as $row) {
                $row->markNumber($number);
                if ($row->isBingo()) {
                    $this->isBingo = true;
                }
            }
            foreach ($this->columns as $column) {
                $column->markNumber($number);
                if ($column->isBingo()) {
                    $this->isBingo = true;
                }
            }
//            var_dump("played $number on board $this->id");
        }
        $this->playedNumbers++;
    }

    private function initRowsAndColumns()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->rows[] = new Line();
            $this->columns[] = new Line();
        }
    }
}


class Day4 extends AbstractBenchmarking
{
    private array $data;
    private array $numbersDrawn;
    /**
     * @var BingoBoard[]
     */
    private array $boards = [];

    public function __construct()
    {
        $this->data = explode(PHP_EOL, file_get_contents('Data/day4.txt'));
    }

    public function part1(): float|int
    {
        $this->numbersDrawn = array_map('intval', explode(',', $this->data[0]));
        $this->boards = $this->generateBoards(array_slice($this->data, 1), $this->numbersDrawn);
        $winningBoard = $this->getWinningBoard();
        $markedNumbers = $winningBoard->getMarkedNumbers();
        return array_sum($winningBoard->getUnmarkedNumbers()) * end($markedNumbers);
    }

    public function part2(): float|int
    {
        $this->numbersDrawn = array_map('intval', explode(',', $this->data[0]));
        $this->boards = $this->generateBoards(array_slice($this->data, 1), $this->numbersDrawn);
        $worstBoard = $this->getWorstBoard();
        $markedNumbers = $worstBoard->getMarkedNumbers();
        return array_sum($worstBoard->getUnmarkedNumbers()) * end($markedNumbers);
    }

    private function generateBoards(array $rows, array $numbers): array
    {
        $boardsObj = [];
        for ($i = 0; $i < count($rows); $i = $i + 5) {

            $board = new BingoBoard(floor($i / 5 + 1));
            $board->addRow($rows[$i]);
            $board->addRow($rows[$i + 1]);
            $board->addRow($rows[$i + 2]);
            $board->addRow($rows[$i + 3]);
            $board->addRow($rows[$i + 4]);
            $board->playBingo($numbers);
            $boardsObj[] = $board;
        }
        return $boardsObj;
    }

    /**
     * @return BingoBoard
     */
    #[Pure] public function getWinningBoard(): BingoBoard
    {
        $winningBoard = null;
        foreach ($this->boards as $board) {
            if ($winningBoard === null) {
                $winningBoard = $board;
                continue;
            }
            if ($board->isBingo && $board->getPlayedNumbers() < $winningBoard->getPlayedNumbers()) {
                $winningBoard = $board;
            }
        }
        return $winningBoard;
    }

    /**
     * @return BingoBoard
     */
    #[Pure] public function getWorstBoard(): BingoBoard
    {
        $worstBoard = null;
        foreach ($this->boards as $board) {
            if ($worstBoard === null) {
                $worstBoard = $board;
                continue;
            }
            if ($board->isBingo && $board->getPlayedNumbers() >= $worstBoard->getPlayedNumbers()) {
                $worstBoard = $board;
            }

        }
        return $worstBoard;
    }
}
