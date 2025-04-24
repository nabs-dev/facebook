<?php
include 'db.php';
session_start();
$from_id = $_SESSION['user_id'];
$to_id = $_GET['to'];

$check = $conn->query("SELECT * FROM friend_requests WHERE from_id=$from_id AND to_id=$to_id");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO friend_requests (from_id, to_id) VALUES ($from_id, $to_id)");
}
echo "<script>window.location.href='home.php';</script>";
?>
