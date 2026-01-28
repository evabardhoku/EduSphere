<?php
global $conn;
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id'])){
    $outgoing_id = $_SESSION['unique_id'];
    $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);

    // Get current time and message timestamp
    $current_time = new DateTime();
    $sql = "SELECT timestamp FROM messages WHERE msg_id = {$msg_id} AND outgoing_msg_id = {$outgoing_id}";
    $query = mysqli_query($conn, $sql);

    if(mysqli_num_rows($query) > 0){
        $row = mysqli_fetch_assoc($query);
        $message_time = new DateTime($row['timestamp']);
        $interval = $current_time->diff($message_time);

        // Check if the message is less than 2 minutes old
        if($interval->i < 2 && $interval->h == 0 && $interval->days == 0){
            // Delete the message
            $delete_sql = "DELETE FROM messages WHERE msg_id = {$msg_id} AND outgoing_msg_id = {$outgoing_id}";
            if(mysqli_query($conn, $delete_sql)){
                echo json_encode(["status" => "success", "message" => "Message deleted."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete message."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Message is too old to delete."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Message not found."]);
    }
} else {
    header("location: ../login.php");
}
?>
