<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Post creation handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = $conn->real_escape_string(trim($_POST['content']));
    if (!empty($content)) {
        $conn->query("INSERT INTO posts (user_id, content) VALUES ($user_id, '$content')");
    }
}

// Get number of friends
$friends_count = $conn->query("SELECT COUNT(*) as count FROM friends WHERE user_id=$user_id OR friend_id=$user_id")->fetch_assoc();
$friends_count = $friends_count['count'] / 2;

// Fetch all posts
$posts = $conn->query("SELECT posts.*, users.name FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facebook Clone - Home</title>
    <style>
        body { font-family: Arial; background-color: #f4f6f7; margin: 0; }
        .navbar { background: #1877f2; padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center; font-size: 24px; font-weight: bold; }
        .navbar .title { margin-left: 10px; }
        .navbar .logout-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
        }
        .navbar .logout-btn:hover { background: #d32f2f; }
        .container { max-width: 800px; margin: 20px auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .profile-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .profile-info { display: flex; align-items: center; }
        .profile-info img { width: 80px; height: 80px; border-radius: 50%; margin-right: 15px; }
        .button { background: #1877f2; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; text-decoration: none; }
        .button:hover { background: #145db2; }
        textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc; }
        .post { margin-top: 20px; padding: 15px; border-radius: 10px; background: #fff; box-shadow: 0 0 5px #ccc; }
        .post h4 { margin: 0 0 10px; color: #1877f2; }
        .comment-box input { width: 70%; padding: 6px; border-radius: 6px; border: 1px solid #ccc; }
        .comment-box button { padding: 6px 10px; background: #1877f2; color: white; border: none; border-radius: 6px; }
        .comments { margin-top: 10px; font-size: 14px; color: #333; }
        .comments p { margin: 5px 0; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="title">Facebook Clone</div>
    <form method="POST" action="logout.php" style="margin:0;">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>

<div class="container">
    <!-- Profile -->
    <div class="profile-section">
        <div class="profile-info">
            <img src="<?= isset($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default.jpg' ?>" alt="Profile Picture">
            <div>
                <h2><?= htmlspecialchars($user['name']) ?></h2>
                <p>Friends: <?= $friends_count ?></p>
            </div>
        </div>
        <a href="profile.php" class="button">Go to Profile</a>
    </div>

    <!-- Post Form -->
    <form method="POST">
        <textarea name="content" rows="3" placeholder="What's on your mind?" required></textarea>
        <button type="submit" class="button">Post</button>
    </form>

    <!-- All Posts -->
    <?php while ($row = $posts->fetch_assoc()) {
        $post_id = $row['id'];
        $likes = $conn->query("SELECT COUNT(*) as count FROM likes WHERE post_id=$post_id")->fetch_assoc();
        $comments = $conn->query("SELECT comments.comment, users.name FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = $post_id ORDER BY comments.id ASC");
    ?>
        <div class="post">
            <h4><?= htmlspecialchars($row['name']) ?></h4>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>

            <form method="GET" action="like.php" style="display:inline;">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <button class="button">❤️ Like</button>
            </form>
            <span style="margin-left:10px;"><?= $likes['count'] ?> Likes</span>

            <!-- Comment Form -->
            <div class="comment-box" style="margin-top:10px;">
                <form method="POST" action="comment.php">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <input type="text" name="comment" placeholder="Write a comment..." required>
                    <button type="submit">Comment</button>
                </form>
            </div>

            <!-- Show Comments -->
            <div class="comments">
                <?php while ($c = $comments->fetch_assoc()) {
                    echo "<p><strong>" . htmlspecialchars($c['name']) . ":</strong> " . htmlspecialchars($c['comment']) . "</p>";
                } ?>
            </div>
        </div>
    <?php } ?>
</div>

</body>
</html>
