<?php
// Start session and include necessary files
session_start();
include("classes/autoload.php");
include "classes/group.php";

// Check if user is logged in
if (!isset($_SESSION['mybook_userid'])) {
    header("Location: login.php");
    exit();
}

// Initialize the Login class and check user login status
$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid']);

// Retrieve the group ID from the URL and validate it
$groupid = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($groupid) {
    // Initialize the Group class and process the join request
    $group_class = new Group();
    $group_class->join_group($groupid, $_SESSION['mybook_userid']); // Call the join_group method

    // Determine where to redirect the user after processing the request
    $return_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "profile.php";
    header("Location: " . $return_to);
    exit();
} else {
    // Handle the case where the group ID is invalid
    echo "Invalid group ID!";
}
?>
