<?php

global $conn;
session_start();
include_once "config.php";

if (isset($_SESSION['unique_id']) && isset($_POST['msg_id']) && isset($_POST['msg'])) {
    $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);
    $msg = mysqli_real_escape_string($conn, $_POST['msg']);
    $current_time = date("Y-m-d H:i:s");

    // Check if the message is editable (within 4 minutes)
    $sql = "SELECT timestamp FROM messages WHERE msg_id = {$msg_id}";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);

    if ($row) {
        $message_time = new DateTime($row['timestamp']);
        $current_time = new DateTime($current_time);
        $interval = $message_time->diff($current_time);
        $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        if ($minutes <= 4) {
            $update_sql = "UPDATE messages SET msg = '{$msg}' WHERE msg_id = {$msg_id}";
            if (mysqli_query($conn, $update_sql)) {
                echo "Message updated successfully";
            } else {
                echo "Error updating message";
            }
        } else {
            echo "Message cannot be edited after 4 minutes";
        }
    }
} else {
    echo "Invalid request";
}

?>