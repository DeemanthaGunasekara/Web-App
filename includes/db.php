<?php
$db_host = "sql208.infinityfree.com";
$db_user = "if0_42558129";
$db_pass = "7o0hIcZgeo";
$db_name = "if0_42558129_blogapplication";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}