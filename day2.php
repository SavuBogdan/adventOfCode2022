<?php

/**
 *
 */
require_once './AbstractBenchmarking.php';

class Day2 extends AbstractBenchmarking
{
    private array $data;

    private const DOWN = 'down';
    private const UP = 'up';
    private const FORWARD = 'forward';
    private int $x;
    private int $y;
    private int $aim;

    public function __construct()
    {
        $this->data = explode(PHP_EOL, file_get_contents('Data/day2.txt'));
        $this ->data = array_map(fn($item) => explode(' ', $item), $this->data);
    }

    public function part1(): int
    {
        $this->x = 0;
        $this->y = 0;
        foreach ($this->data as $value) {
            if ($value[0] === self::DOWN) {
                $this->y += $value[1];
            } elseif ($value[0] === self::UP) {
                $this->y -= $value[1];
            } elseif ($value[0] === self::FORWARD) {
                $this->x += $value[1];
            }
        }
        return $this->x * $this->y;
    }

    public function part2(): float|int
    {
        $this->x = 0;
        $this->y = 0;
        $this->aim = 0;
        foreach ($this->data as $value) {
            if ($value[0] === self::DOWN) {
                $this->aim += $value[1];
            } elseif ($value[0] === self::UP) {
                $this->aim -= $value[1];
            } elseif ($value[0] === self::FORWARD) {
                $this->x += $value[1];
                $this->y += $value[1] * $this->aim;
            }
        }
        return $this->x * $this->y;
    }
}
$day = new Day2();

var_dump($day->part2());