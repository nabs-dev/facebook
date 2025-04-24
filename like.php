<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['post_id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = (int)$_GET['post_id'];

// Check if already liked
$check = $conn->query("SELECT * FROM likes WHERE user_id=$user_id AND post_id=$post_id");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)");
}

echo "<script>window.location.href='index.php';</script>";
?>
