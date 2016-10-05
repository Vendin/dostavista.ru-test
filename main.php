<?php

require_once 'vendor/autoload.php';
require_once 'function.php';
require_once 'const.php';

date_default_timezone_set('Europe/Moscow');

$city = $argv[1] ?? 'Moscow';
echo getWeatherInfo($city);

