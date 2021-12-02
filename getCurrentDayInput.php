<?php

$year = (int)date('Y');
$day = (int)date('d');

$url = "https://adventofcode.com/$year/day/$day/input";
$payload = executeCurl($url);
generateNewPhpDayFile($day);
generateNewDayDataFile($payload, $day);

function generateNewDayDataFile(string $payload, int $day)
{
    $payload = implode(PHP_EOL, array_filter(explode(PHP_EOL, $payload)));
    file_put_contents("Data/day$day.txt", $payload);
}

function executeCurl($url)
{
    $cookies = implode('; ', explode(PHP_EOL, file_get_contents('../cookiesAdventOfCode2020.txt')));
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_COOKIE, $cookies);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($handle);
}

function generateNewPhpDayFile($day)
{
    $data = file_get_contents('dayTemplate.php');
    $newData = str_replace('day', "day$day", $data);
    $newData = str_replace('Day', "Day$day", $newData);
    file_put_contents("day$day.php", $newData);
}