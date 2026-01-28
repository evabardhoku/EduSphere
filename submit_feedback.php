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

// Ensure the user is logged in
if (!isset($_SESSION['mybook_userid'])) {
    die("Ju lutem logohuni për të dhënë feedback.");
}

// Get the username and feedback from the form
$form_username = $_POST['username'] ?? ''; // Username from the form
$feedback_text = $_POST['feedback'] ?? ''; // Feedback text from the form

// Check if both username and feedback are provided
if (!empty($form_username) && !empty($feedback_text)) {
    // Prepare and execute the query to insert feedback
    $query = $conn->prepare("INSERT INTO feedback (username, feedback_text) VALUES (?, ?)");

    if ($query) {
        $query->bind_param("ss", $form_username, $feedback_text); // Bind username and feedback_text

        if ($query->execute()) {
            echo "Faleminderit për feedback-un tuaj!";
        } else {
            echo "Diçka shkoi keq. Ju lutem provoni përsëri.";
        }

        $query->close();
    } else {
        echo "Diçka shkoi keq me përgatitjen e pyetjes.";
    }
} else {
    echo "Ju lutem jepni username dhe feedback-un tuaj për të vazhduar.";
}

// Close the database connection
$conn->close();
