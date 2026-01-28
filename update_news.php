<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "ba#83.!";
$dbname = "mybook_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['news_id']) && isset($_POST['news_title']) && isset($_POST['news_description'])) {
    $news_id = $_POST['news_id'];
    $title = $_POST['news_title'];
    $description = $_POST['news_description'];

    $sql = "UPDATE news SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $description, $news_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: dashboard.php"); // Redirect back to the dashboard
?>
