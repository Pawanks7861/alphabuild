<?php

chdir(dirname(__DIR__, 2));

$appUrl = getenv('PERFEX_APP_URL') ?: 'http://127.0.0.1:8080/';
$upgradeUrl = rtrim($appUrl, '/') . '/admin';

$ch = curl_init($upgradeUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => 'upgrade_database=true',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 300,
]);
$output = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($status >= 500 || $output === false) {
    fwrite(STDERR, "Database upgrade request failed with HTTP {$status}.\n");
    exit(1);
}

if (stripos((string) $output, 'Database upgrade is required') !== false) {
    fwrite(STDERR, "Database upgrade did not complete.\n");
    fwrite(STDERR, substr((string) $output, 0, 2000) . PHP_EOL);
    exit(1);
}

$version = null;
$appConfig = file_get_contents('application/config/app-config.php');
if (preg_match("/define\\('APP_DB_NAME', '([^']+)'\\)/", $appConfig, $matches)) {
    $dbName = $matches[1];
    $mysqli = @new mysqli('localhost', 'alphabuild', 'alphabuild', $dbName, 3306, '/var/run/mysqld/mysqld.sock');
    if ($mysqli->connect_errno) {
        $mysqli = new mysqli('localhost', 'alphabuild', 'alphabuild', $dbName);
    }
    if (!$mysqli->connect_errno) {
        $result = $mysqli->query('SELECT version FROM tblmigrations LIMIT 1');
        if ($result) {
            $row = $result->fetch_assoc();
            $version = $row['version'] ?? null;
        }
    }
}

if ((int) $version !== 316) {
    fwrite(STDERR, "Expected database version 316, got {$version}.\n");
    exit(1);
}

echo "Database upgraded to version {$version}.\n";
