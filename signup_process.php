<?php
include 'db.php';
session_start();

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $password);
$stmt->execute();

$_SESSION['user_id'] = $conn->insert_id;
echo "<script>window.location.href='home.php';</script>";
?>
