<?php
// get_news.php

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

// Get news ID from the request
if (isset($_GET['id'])) {
    $news_id = intval($_GET['id']);
    $sql = "SELECT title, description FROM news WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($title, $description);
    if ($stmt->fetch()) {
        // Return JSON response
        echo json_encode(['title' => $title, 'description' => $description]);
    } else {
        echo json_encode(['error' => 'News not found']);
    }
    $stmt->close();
}

$conn->close();
?>
