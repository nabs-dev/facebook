<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    exit();
}
$content = $_POST['content'];
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $content);
$stmt->execute();
echo "<script>window.location.href='home.php';</script>";
?>
