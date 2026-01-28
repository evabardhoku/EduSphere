<?php
// Database connection details
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

// Start session
session_start();


// Get feedback ID to delete
$feedback_id = $_GET['id'] ?? '';

if (!empty($feedback_id)) {
    // Prepare and execute the deletion query
    $query = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $query->bind_param("i", $feedback_id);

    if ($query->execute()) {
        echo "Feedback-u është fshirë me sukses.";
    } else {
        echo "Diçka shkoi keq. Ju lutem provoni përsëri.";
    }

    $query->close();
} else {
    echo "ID e feedback-ut nuk është e vlefshme.";
}

$conn->close();

// Redirect back to the feedback management page
header("Location: feedback.php");
exit();
?>
