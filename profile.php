<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['bio'])) {
        $bio = htmlspecialchars($_POST['bio']);
        $conn->query("UPDATE users SET bio='$bio' WHERE id=$user_id");
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
        $conn->query("UPDATE users SET profile_pic='$target_file' WHERE id=$user_id");
    }

    // Handle sending friend request
    if (isset($_POST['friend_id'])) {
        $friend_id = (int)$_POST['friend_id'];
        $check = $conn->query("SELECT * FROM friends WHERE (user_id=$user_id AND friend_id=$friend_id) OR (user_id=$friend_id AND friend_id=$user_id)");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO friends (user_id, friend_id) VALUES ($user_id, $friend_id), ($friend_id, $user_id)");
        }
    }

    echo "<script>window.location.href='profile.php';</script>";
    exit();
}

// Get total number of friends
$friends_count = $conn->query("SELECT COUNT(*) as count FROM friends WHERE user_id=$user_id")->fetch_assoc()['count'];

// Get users who previously logged in and are not already friends
$logged_in_users = $conn->query("
    SELECT * FROM users 
    WHERE id != $user_id 
      AND id NOT IN (
        SELECT friend_id FROM friends WHERE user_id = $user_id
    )
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Facebook Clone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f7;
        }
        .navbar {
            background-color: #1877f2;
            padding: 15px;
            color: white;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .profile-info {
            display: flex;
            align-items: center;
        }
        .profile-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .profile-info h2 {
            margin: 0;
        }
        .bio-section, .friends-section {
            margin-top: 20px;
        }
        textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: none;
        }
        .button {
            background-color: #1877f2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #145db2;
        }
        .user-card {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            justify-content: space-between;
        }
        .user-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="navbar">Facebook Clone - Profile</div>

<div class="container">
    <div class="profile-section">
        <div class="profile-info">
            <img src="<?= isset($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default.jpg' ?>" alt="Profile Picture">
            <div>
                <h2><?= htmlspecialchars($user['name']) ?></h2>
                <p>Friends: <?= $friends_count ?></p>
            </div>
        </div>
        <a href="index.php" class="button">Go to Feed</a>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="profile_pic">
        <textarea name="bio" rows="3" placeholder="Update your bio..."><?= htmlspecialchars($user['bio']) ?></textarea>
        <button type="submit" class="button">Update Profile</button>
    </form>

    <div class="bio-section">
        <h3>Your Bio</h3>
        <p><?= isset($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : "No bio available." ?></p>
    </div>

    <div class="friends-section">
        <h3>Send Friend Request</h3>
        <?php if ($logged_in_users->num_rows > 0): ?>
            <?php while ($row = $logged_in_users->fetch_assoc()): ?>
                <div class="user-card">
                    <div class="user-info">
                        <img src="<?= $row['profile_pic'] ?: 'uploads/default.jpg' ?>" alt="User">
                        <span><?= htmlspecialchars($row['name']) ?></span>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="friend_id" value="<?= $row['id'] ?>">
                        <button class="button" type="submit">Send Request</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No users available to send requests.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
