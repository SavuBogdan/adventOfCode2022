<?php

use LucidFrame\Console\ConsoleTable;

require_once 'ConsoleTable.php';


$files = preg_filter('/.*day\d+\.php/', '$0', glob(dirname(__FILE__) . '/*.php'));


foreach ($files as $file) {
    include $file;
}


print PHP_EOL . PHP_EOL . 'Benchmarking AdventOfCode2021...' . PHP_EOL . PHP_EOL;


$table = new ConsoleTable();
$table->addHeader('Day');
$table->addHeader('Part 1 µs');
$table->addHeader('Part 2 µs');


/**
 * @param mixed $file
 * @param ConsoleTable $table
 */
function addEntry(mixed $file, ConsoleTable &$table)
{

    $className = preg_replace('/\.php$/', '', $file);
    $className = preg_replace('/^.*\//', '', $className);
    $className = preg_replace('/[^a-zA-Z0-9]/', '', $className);
    $className = ucfirst($className);
    $class = new $className();
    [$part1Time, $part2Time] = $class->benchmark(1000);
    $table->addRow()
        ->addColumn($className)
        ->addColumn($part1Time)
        ->addColumn($part2Time);


}

foreach ($files as $file) {
    if (isset($argv[1])) {
        if (str_contains($file, 'day' . $argv[1] . '.php')) {
            addEntry($file, $table);
        }
    } else {
        addEntry($file, $table);
    }
}

$table->display();
