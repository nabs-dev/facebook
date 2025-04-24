<?php
include 'db.php';
session_start();
$from_id = $_GET['from'];
$to_id = $_SESSION['user_id'];

// Accept request
$conn->query("INSERT INTO friends (user_id, friend_id) VALUES ($from_id, $to_id)");
$conn->query("INSERT INTO friends (user_id, friend_id) VALUES ($to_id, $from_id)");

// Remove from requests
$conn->query("DELETE FROM friend_requests WHERE from_id=$from_id AND to_id=$to_id");

echo "<script>window.location.href='home.php';</script>";
?>
