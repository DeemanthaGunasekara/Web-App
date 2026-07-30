<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function is_logged_in() { return isset($_SESSION['user_id']); }

function require_login() {
    if (!is_logged_in()) { header("Location: login.php"); exit(); }
}

function current_user_id() { return $_SESSION['user_id'] ?? null; }
function current_username() { return $_SESSION['username'] ?? null; }

function h($string) { return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8'); }