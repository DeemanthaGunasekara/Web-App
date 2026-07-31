<?php
require 'includes/auth.php';
require 'includes/db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title && $content) {
        $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?,?,?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $title, $content);
        $stmt->execute();
        header("Location: view_blog.php?id=" . $stmt->insert_id);
        exit();
    }
}