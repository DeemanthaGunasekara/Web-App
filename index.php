<?php
require 'includes/auth.php';
require 'includes/db.php';

$sql = "SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.created_at, users.username
        FROM blogPost JOIN users ON blogPost.user_id = users.id
        ORDER BY blogPost.created_at DESC";
$result = $conn->query($sql);
?>
<!-- loop through $result with fetch_assoc() and display cards -->