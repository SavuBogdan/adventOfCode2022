<?php

/**
 *
 */
require_once './AbstractBenchmarking.php';

class Day extends AbstractBenchmarking
{
    private array $data;

    public function __construct()
    {
        $this->data = array_map('intval', explode(PHP_EOL, file_get_contents('Data/day.txt')));
    }

    public function part1()
    {
    }

    public function part2()
    {
    }
}