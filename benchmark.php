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
natsort($files);

foreach ($files as $file) {
    include $file;
}
$gradient1 = [];
$steps = 333333;

$previous = null;
$colors = [
//    [251, 0, 77],
//    [218, 0, 251],
//    [21, 0, 251],
//    [0, 208, 251],
    [0, 251, 10],
    [251, 251, 0],
    [251, 76, 0],
];

foreach ($colors as $color) {
    if (empty($previous)) {
        $previous = $color;
        continue;
    }
    $r_step = ($color[0] - $previous[0]) / $steps;
    $g_step = ($color[1] - $previous[1]) / $steps;
    $b_step = ($color[2] - $previous[2]) / $steps;

    for ($i = 0; $i < $steps; $i++) {
        $r = floor($previous[0] + ($r_step * $i));
        $g = floor($previous[1] + ($g_step * $i));
        $b = floor($previous[2] + ($b_step * $i));
        $gradient1[] = [$r, $g, $b];
    }
    $previous = $color;
}

$gradient2 = [];
$steps = 10001;

$previous = null;
$colors = [
    [251, 0, 77],
    [218, 0, 251],
    [21, 0, 251],
    [0, 208, 251],
    [0, 251, 10],
    [251, 251, 0],
    [251, 76, 0],
];

foreach ($colors as $color) {
    if (empty($previous)) {
        $previous = $color;
        continue;
    }
    $r_step = ($color[0] - $previous[0]) / $steps;
    $g_step = ($color[1] - $previous[1]) / $steps;
    $b_step = ($color[2] - $previous[2]) / $steps;

    for ($i = 0; $i < $steps; $i++) {
        $r = floor($previous[0] + ($r_step * $i));
        $g = floor($previous[1] + ($g_step * $i));
        $b = floor($previous[2] + ($b_step * $i));
        $gradient2[] = [$r, $g, $b];
    }
    $previous = $color;
}

$timings = [];
$lowest = PHP_INT_MAX;
$highest = PHP_INT_MIN;

$currentRow = 0;

$table = new ConsoleTable();
$table->addHeader('Day');
$table->addHeader('Parse time');
$table->addHeader('Part 1 time');
$table->addHeader('Part 2 time');
$table->addHeader('Test1');
$table->addHeader('Test2');

function padding(string $string, int $length): string
{
    $stringSanitized = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $string);
    return str_repeat(' ', $length - mb_strlen($stringSanitized, 'UTF-8')) . $string;
}

/**
 * @param mixed $file
 * @param array $timings
 * @param int $currentRow
 * @param int $low
 * @param int $high
 */
function addEntry(mixed $file, array &$timings, int &$currentRow, int &$low, int &$high)
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
    [$part1Time, $part2Time] = $class2->benchmark(100);


    $timings[$currentRow] = [
        'day' => $className,
        'parseTime' => $parseTime,
        'part1Time' => $part1Time,
        'part2Time' => $part2Time,
        'result1' => padding($result1, 5),
        'result2' => padding($result2, 5),
    ];

    foreach ([$part1Time[2], $part2Time[2]] as $time) {
        if ($time < $low) {
            $low = $time;
        }
        if ($time > $high) {
            $high = $time;
        }
    }
    $currentRow++;
}

foreach ($files as $file) {
    if (isset($argv[1])) {
        if (str_contains($file, 'day' . $argv[1] . '.php')) {
            addEntry($file, $timings, $currentRow, $lowest, $highest);
        }
    } else {
        addEntry($file, $timings, $currentRow, $lowest, $highest);
    }
}


function percentageBetweenTwoNumbers(float $number, int $min, int $max)
{
    return (($number - $min) / ($max - $min) * 100);
}

function getColoredTiming(array $timing, array $gradient, $lowest, $highest): string
{
    $ns = $timing[2];

    $output = padding(implode(' ', array_slice($timing, 0, 2)), 11);
    $percentage = percentageBetweenTwoNumbers($ns, $lowest, $highest);
    $key = $percentage * 666666 / 100 >= 666666 ? 666665 : ($percentage === 0 ? 0 : $percentage * 666666 / 100);
    $key = intval($key);
    $output = colorize($output, $gradient[$key][0], $gradient[$key][1], $gradient[$key][2]);
    return $output;
}

foreach ($timings as $row) {
    $table->addRow();
    foreach ($row as $key => $col) {
        if (is_array($col) && $key !== 'parseTime') {
            $table->addColumn(getColoredTiming($col, $gradient1, $lowest, $highest));
        } else {
            if (is_array($col)) {
                $col = padding(implode(' ', array_slice($col, 0, 2)), 11);
            }
            if (strpos($col, 'Day') !== false) {
                $col = padding(filter_var($col, FILTER_SANITIZE_NUMBER_INT),3);
            }
            $table->addColumn($col);
        }
    }
}

$tableOutput = $table->getTable();
$output = "\n\n\n" .
    PHP_EOL . PHP_EOL . '🎄🎄🎄  Benchmarking AdventOfCode2021...  🎄🎄🎄' . PHP_EOL . PHP_EOL .
    $tableOutput .
    "\n\n\n";

echo $output;

//printTable($output, $gradient2);

function printTable($output, $gradient): string
{
    $return = '';
    $outputPrint = preg_replace('#\x1b[[][^A-Za-z]*[A-Za-z]#', '', $output);
    foreach (str_split($outputPrint) as $key => $char) {

        $percentage = percentageBetweenTwoNumbers($key, 0, strlen($outputPrint));
//        var_dump($percentage);
//        var_dump(count($gradient));
        $key = $percentage * 60000 / 100 >= 60000 ? 60000 : ($percentage === 0 ? 0 : $percentage * 60000 / 100);
        $return .= colorize($char, $gradient[$key][0], $gradient[$key][1], $gradient[$key][2]);
    }
    echo $return;
    return $return;
}

function colorize($string, $r, $g, $b)
{
    $colored_string = '';
    $bg = "48;2;${r};${g};${b}";

    $ir = 255 - $r;
    $ig = 255 - $g;
    $ib = 255 - $b;
    $fg = "38;2;${ir};${ig};${ib}";

    if (isset($fg)) {
        $colored_string .= "\033[" . $fg . "m";
    }
    if (isset($bg)) {
        $colored_string .= "\033[" . $bg . "m";
    }
    $colored_string .= $string . "\033[0m";
    return $colored_string;
}

echo "\n\n\n";

