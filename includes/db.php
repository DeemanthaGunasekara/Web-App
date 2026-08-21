<?php
require_once __DIR__ . '/env.php';
load_env(__DIR__ . '/../.env');

$db_host = env('DB_HOST');
$db_user = env('DB_USER');
$db_pass = env('DB_PASS');
$db_name = env('DB_NAME');

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}