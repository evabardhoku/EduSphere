<?php

include("classes/autoload.php");
include_once "classes/group.php";
include_once "classes/login.php";

$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid']);

// Redirect back to profile if HTTP_REFERER is not set
$return_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "profile.php";

// Retrieve group ID and user ID from GET parameters
$groupid = isset($_GET['group_id']) ? (int)$_GET['group_id'] : null;
$userid = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

if ($groupid && $userid) {
    $group = new Group();
    $g_data = $group->get_group($groupid);

    // Check if group data was retrieved
    if (is_array($g_data) && !empty($g_data)) {
        $group_data = $g_data;

        // Debugging output
        echo "Group Data: " . print_r($group_data, true) . "<br>";

        // Check if the user has permission to invite
        if (group_access($_SESSION['mybook_userid'], $group_data, 'member')) {
            $me = $_SESSION['mybook_userid'];
            $result = $group->invite_to_group($groupid, $userid, $me);

            if ($result) {
                // Successfully invited, redirect to the previous page
                header("Location: " . $return_to);
                exit;
            } else {
                // Handle failure to invite
                echo "Failed to send invitation. Please try again.";
            }
        } else {
            echo "You do not have permission to invite users to this group. Your role or group data might be incorrect.";
        }
    } else {
        echo "Group data not found.";
    }
} else {
    echo "Invalid group or user ID.";
}

?>
