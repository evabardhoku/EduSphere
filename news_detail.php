<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "ba#83.!";
$dbname = "mybook_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch news details
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT title, description, created_at FROM news WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($title, $description, $created_at);
$stmt->fetch();
$stmt->close();

if (!$title) {
    echo "News not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">

    <title>News Details</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .news-detail {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            color: #333;
        }

        .news-detail h1 {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .news-detail p {
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .news-detail em {
            display: block;
            margin-bottom: 20px;
            color: #555;
            font-style: normal;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        /* Button styling for any potential buttons or links */
        .news-detail a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s;
        }

        .news-detail a:hover {
            border-color: #007bff;
        }

        /* Responsive design */
        @media (max-width: 600px) {
            .news-detail {
                padding: 15px;
            }

            .news-detail h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<header>
    <?php
    include "main_header.php";
    ?>
</header>
<body>
<div class="news-detail">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p><em>Posted on <?php echo htmlspecialchars($created_at); ?></em></p>
    <p><?php echo nl2br(htmlspecialchars($description)); ?></p>
</div>
</body>
<footer>
    <?php
    include "main_footer.php";
    ?>
</footer>
</html>

<?php
$conn->close();
?>
