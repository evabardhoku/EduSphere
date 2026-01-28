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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['news_id'])) {
    $news_id = $_POST['news_id'];

    $sql = "DELETE FROM news WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: dashboard.php"); // Redirect back to the dashboard
?>
