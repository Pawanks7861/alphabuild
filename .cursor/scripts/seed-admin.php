<?php

define('BASEPATH', dirname(__DIR__, 2) . '/system/');

$options = getopt('', ['email:', 'password:']);
$email = $options['email'] ?? 'admin@example.com';
$password = $options['password'] ?? 'admin123456';

require_once dirname(__DIR__, 2) . '/application/third_party/phpass.php';

$host = getenv('PERFEX_DB_HOST') ?: 'localhost';
$user = getenv('PERFEX_DB_USER') ?: 'alphabuild';
$pass = getenv('PERFEX_DB_PASS') ?: 'alphabuild';
$name = getenv('PERFEX_DB_NAME') ?: 'alphabuild';
$socket = '/var/run/mysqld/mysqld.sock';

$mysqli = @new mysqli($host, $user, $pass, $name, 3306, $socket);
if ($mysqli->connect_errno) {
    $mysqli = new mysqli($host, $user, $pass, $name);
}

if ($mysqli->connect_errno) {
    fwrite(STDERR, 'Database connection failed: ' . $mysqli->connect_error . PHP_EOL);
    exit(1);
}

$hasher = new PasswordHash(8, false);
$hash = $hasher->HashPassword($password);

$stmt = $mysqli->prepare('SELECT staffid FROM tblstaff WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row) {
    $stmt = $mysqli->prepare('UPDATE tblstaff SET password = ?, active = 1, admin = 1, firstname = ?, lastname = ? WHERE staffid = ?');
    $firstname = 'Admin';
    $lastname = 'User';
    $staffId = (int) $row['staffid'];
    $stmt->bind_param('sssi', $hash, $firstname, $lastname, $staffId);
    $stmt->execute();
    $stmt->close();
    echo "Updated existing admin user (staffid={$staffId}).\n";
    exit(0);
}

$stmt = $mysqli->prepare(
    'INSERT INTO tblstaff (
        email, firstname, lastname, password, datecreated, admin, active,
        default_language, direction, hourly_rate, two_factor_auth_enabled
    ) VALUES (?, ?, ?, ?, NOW(), 1, 1, NULL, NULL, 0, 0)'
);
$firstname = 'Admin';
$lastname = 'User';
$stmt->bind_param('ssss', $email, $firstname, $lastname, $hash);
$stmt->execute();
$staffId = $stmt->insert_id;
$stmt->close();

echo "Created admin user (staffid={$staffId}).\n";
