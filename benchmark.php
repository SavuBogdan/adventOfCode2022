<?php

use LucidFrame\Console\ConsoleTable;

require_once 'ConsoleTable.php';

const EMOJI = [
    'success' => '✅',
    'fail' => '❌',
    'info' => 'ℹ️',
    'warning' => '⚠️',
    'question' => '❓',
    'christmas' => '🎄',
];

$files = preg_filter('/.*day\d+\.php/', '$0', glob(dirname(__FILE__) . '/*.php'));


foreach ($files as $file) {
    include $file;
}


print PHP_EOL . PHP_EOL . '🎄🎄🎄  Benchmarking AdventOfCode2021...  🎄🎄🎄' . PHP_EOL . PHP_EOL;


$table = new ConsoleTable();
$table->addHeader('Day');
$table->addHeader('Parse time');
$table->addHeader('Unit');
$table->addHeader('Part 1 time');
$table->addHeader('Unit');
$table->addHeader('Test');
$table->addHeader('Part 2 time');
$table->addHeader('Unit');
$table->addHeader('Test');


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
    $testResults = explode(PHP_EOL, file_get_contents('TestData/expectedTestResults.txt'));
    foreach ($testResults as $key => $testResult) {
        $testResults[$key] = array_map('intval', explode(' ', $testResult));
    }
    [$result1, $result2] = $class->test();

    $outputGreenCheckmark = "\x1b[32m✔\x1b[0m";
    $outputRedX = "\x1b[31m✘\x1b[0m";
    $outputOrangeQ = "\x1b[33m?\x1b[0m";

    if (!isset($testResults[(int)filter_var($className, FILTER_SANITIZE_NUMBER_INT) - 1])) {
        $result1 = $outputOrangeQ;
    } else {
        $result1 = $result1 !== $testResults[(int)filter_var($className, FILTER_SANITIZE_NUMBER_INT) - 1][0]
            ? $outputRedX
            : $outputGreenCheckmark;
    }
    if (!isset($testResults[(int)filter_var($className, FILTER_SANITIZE_NUMBER_INT) - 1])) {
        $result2 = $outputOrangeQ;
    } else {
        $result2 = $result2 !== $testResults[(int)filter_var($className, FILTER_SANITIZE_NUMBER_INT) - 1][1]
            ? $outputRedX
            : $outputGreenCheckmark;
    }



    $parseTime = $class->benchmarkParseData();

    $class2 = new $className();
    [$part1Time, $part2Time] = $class2->benchmark(1000);

    $table->addRow()
        ->addColumn($className)
        ->addColumn($parseTime[0])
        ->addColumn($parseTime[1])
        ->addColumn($part1Time[0])
        ->addColumn($part1Time[1])
        ->addColumn($result1)
        ->addColumn($part2Time[0])
        ->addColumn($part2Time[1])
        ->addColumn($result2);


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
