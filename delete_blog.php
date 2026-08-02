<?php
require 'includes/auth.php';
require 'includes/db.php';
require_login();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$id = (int)($_POST['id'] ?? 0);


$stmt = $conn->prepare("SELECT user_id FROM blogPost WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$blog = $result->fetch_assoc();


if ($blog['user_id'] != current_user_id()) {
    $page_title = "Not Allowed";
    require 'includes/header.php';
    echo '<div class="alert alert-error">You are not allowed to delete this blog post.</div>';
    require 'includes/footer.php';
    exit();
}


$delete = $conn->prepare("DELETE FROM blogPost WHERE id = ? AND user_id = ?");
$delete->bind_param("ii", $id, $_SESSION['user_id']);
$delete->execute();
$delete->close();

header("Location: index.php");
exit();
