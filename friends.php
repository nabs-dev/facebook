<?php
include 'db.php';
session_start();
$user_id = $_SESSION['user_id'];

$friends = $conn->query("SELECT users.name FROM friends 
JOIN users ON users.id = friends.friend_id 
WHERE friends.user_id = $user_id");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Friends</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f2f5; }
        h2 { color: #1877f2; }
        ul { list-style: none; padding: 0; }
        li { background: white; margin-bottom: 10px; padding: 10px; border-radius: 6px; }
    </style>
</head>
<body>
    <h2>Your Friends</h2>
    <ul>
        <?php while ($row = $friends->fetch_assoc()) { ?>
            <li><?= $row['name'] ?></li>
        <?php } ?>
    </ul>
    <button onclick="goBack()">Back to Home</button>
    <script>
        function goBack() {
            window.location.href = "home.php";
        }
    </script>
</body>
</html>
