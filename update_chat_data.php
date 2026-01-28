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

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $query = $_POST['query'];
    $reply = $_POST['reply'];

    $sql = "UPDATE chatbot SET queries=?, replies=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $query, $reply, $id);
    if ($stmt->execute()) {
        echo "Update successful";
    } else {
        echo "Update failed: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>
