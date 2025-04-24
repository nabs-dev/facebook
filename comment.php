<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['comment'], $_POST['post_id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$comment = $conn->real_escape_string(trim($_POST['comment']));
$post_id = (int)$_POST['post_id'];

if (!empty($comment)) {
    $conn->query("INSERT INTO comments (user_id, post_id, comment) VALUES ($user_id, $post_id, '$comment')");
}

echo "<script>window.location.href='index.php';</script>";
?>
