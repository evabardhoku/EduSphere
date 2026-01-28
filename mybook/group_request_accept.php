<?php

include("classes/autoload.php");
include_once "classes/config.php";
include_once "classes/group.php";

$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid']);

// Check if referer is set and use it for redirection
$return_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "profile.php";

// Get and validate parameters
$groupid = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;
$userid = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$action = isset($_GET['action']) ? trim($_GET['action']) : null;


// Validate parameters
if ($groupid <= 0 || $userid <= 0 || !in_array($action, ['accept', 'decline'])) {
    // Redirect if parameters are invalid
    header("Location: " . $return_to);
    die;
}

// Create Group object and call accept_request method
$group_class = new Group();
try {
    $group_class->accept_request($groupid, $userid, $action);
} catch (Exception $e) {
    // Handle exceptions, maybe log them or show a user-friendly message
    error_log("Error: " . $e->getMessage());
}

// Redirect back to the original page
header("Location: " . $return_to);
die;
?>
