<?php
global $con;
require('config.inc.php');
require('functions.php');

if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
} else {
    die('No user ID specified.');
}

// Fetch user information
$sql = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die('User not found.');
}

// Fetch user posts sorted by comment count
$posts_sql = "
    SELECT *
    FROM posts
    WHERE user_id = $user_id
    ORDER BY comments DESC, date DESC
";
$posts_result = mysqli_query($con, $posts_sql);
$posts = [];

while ($row = mysqli_fetch_assoc($posts_result)) {
    $posts[] = $row;
}

$posts_result = mysqli_query($con, $posts_sql);
$posts = [];

while ($row = mysqli_fetch_assoc($posts_result)) {
    $posts[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['username']) ?>'s Profile</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Global styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f4f7fc;
            color: #333;
            font-size: 16px;
            line-height: 1.6;
            padding: 10%;
            margin-left: 50px;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        /* Profile Header Styling */
        .profile-header {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
        }

        /* Profile Image Styling */
        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
             background-image: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca); /* Apply gradient background */

            object-fit: cover;
            margin-right: 155px;
            margin-left: 20px;
            margin-top: 3px;
        }

        /* Profile Details Styling */
        .profile-header div {
            max-width: 600px;
        }

        .profile-header h2 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .profile-header p {
            color: #777;
            font-size: 16px;
            margin-bottom: 10px;
        }

        /* Profile Links Styling */
        .profile-links {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .profile-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #3498db;
            font-size: 16px;
        }

        .profile-links a:hover {
            text-decoration: underline;
        }


        /* Posts List */
        .posts-list {
            margin-top: 30px;
        }

        .posts-list h3 {
            text-align: left;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .posts-list .post-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            background-color: #fff;
            transition: background-color 0.3s;
        }

        .posts-list .post-item h3 {
            margin-top: 0;
            font-size: 20px;
            color: #2c3e50;
        }

        .posts-list .post-item .date {
            color: #777;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .posts-list .post-item .comment-count {
            color: #3498db;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .posts-list .post-item:hover {
            background-color: #f1f1f1;
        }

        .posts-list a {
            color: #3498db;
            font-size: 16px;
        }

        .posts-list a:hover {
            text-decoration: underline;
        }

        /* Search Box */
        .search-box {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-box input[type="text"] {
            width: 400px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
            border-right: none;
        }

        .search-box button {
            width: 120px;
            padding: 10px;
            border: none;
            background-color: #3498db;
            color: white;
            font-size: 16px;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        .search-box button:hover {
            background-color: #2980b9;
        }

        /* Lists and Links */
        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        ul li a {
            text-decoration: none;
            color: #3498db;
            font-size: 18px;
        }

        ul li:hover {
            background-color: #f1f1f1;
        }

        ul li a:hover {
            text-decoration: underline;
        }

        /* General Text */
        p {
            text-align: center;
            font-size: 18px;
            color: #888;
        }

        a {
            color: #3498db;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

<section class="profile-header">
    <img src="<?= get_image($user['image']) ?>" class="class_21" >
    <div>
        <h2><?= htmlspecialchars($user['username']) ?></h2>
        <p><?= htmlspecialchars($user['bio']) ?></p>
        <div class="profile-links">
            <?php if ($user['fb']): ?>
                <a href="<?= htmlspecialchars($user['fb']) ?>" target="_blank">Facebook</a>
            <?php endif; ?>
            <?php if ($user['tw']): ?>
                <a href="<?= htmlspecialchars($user['tw']) ?>" target="_blank">Twitter</a>
            <?php endif; ?>
            <?php if ($user['yt']): ?>
                <a href="<?= htmlspecialchars($user['yt']) ?>" target="_blank">YouTube</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="posts-list">
    <h3><?= htmlspecialchars($user['username']) ?>'s Posts</h3>
    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-item">
                <h3><?= htmlspecialchars($post['post']) ?></h3>
                <p class="date"><?= htmlspecialchars($post['date']) ?></p>
                <p class="comment-count">Comments: <?= htmlspecialchars($post['comments']) ?></p>
                <a href="post.php?id=<?= $post['id'] ?>">View Post</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts found for this user.</p>
    <?php endif; ?>
</section>



</body>
</html>