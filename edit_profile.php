<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $bio = htmlspecialchars($_POST['bio']);

    $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$id");
    echo "<script>alert('Profile updated!'); window.location.href='profile.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; }
        .box {
            width: 40%;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #1877f2;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover { background: #145db2; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Edit Your Profile</h2>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= $user['name'] ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" required>

            <label>Bio:</label>
            <textarea name="bio" rows="4" placeholder="Write a short bio...">Web developer & social media enthusiast 💻📱</textarea>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
