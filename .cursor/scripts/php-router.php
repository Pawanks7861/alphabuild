<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/../../' . ltrim($path, '/');

if ($path !== '/' && is_file($file) && !is_dir($file)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/../../index.php';
