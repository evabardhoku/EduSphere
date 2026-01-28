<?php
include("classes/autoload.php");
include_once "classes/config.php";
include_once "classes/group.php";

$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid']);

$groupid = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($groupid > 0) {
    $group_class = new Group();

    try {
        // Call the delete method
        $group_class->delete_group($groupid);
        // Redirect to a confirmation page or the groups list
        header("Location: profile.php?section=groups&id=" . $user_data['mybook_userid'] . "&error=Invalid group ID");
        exit();
    } catch (Exception $e) {
        error_log("Error deleting group: " . $e->getMessage());
        // Redirect to an error page or display an error message
        header("Location: profile.php?section=groups&id=" . $user_data['mybook_userid'] . "&message=Group deleted successfully");
        exit();
    }
} else {
    header("Location: profile.php?section=groups&id=" . $user_data['mybook_userid'] . "&error=Error deleting group");
    exit();
}
?>
