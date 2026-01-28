<!--<!---->-->
<?php
////global $conn;
////session_start();
////if(isset($_SESSION['unique_id'])){
////    include_once "config.php";
////    $outgoing_id = $_SESSION['unique_id'];
////    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
////    $output = "";
////    $sql = "SELECT messages.*, users.img
////            FROM messages
////            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
////            WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
////            OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id})
////            ORDER BY msg_id";
////    $query = mysqli_query($conn, $sql);
////    if(mysqli_num_rows($query) > 0){
////        while($row = mysqli_fetch_assoc($query)){
////            $message = $row['msg'];
////            $file_path = $row['file_path'];
////            $timestamp = date("g:i A", strtotime($row['timestamp'])); // Format timestamp
////
////            if($row['outgoing_msg_id'] === $outgoing_id){
////                $output .= '<div class="chat outgoing">
////                            <div class="details">
////                            <div class="timestamp">'.$timestamp.'</div>';
////                if (!empty($file_path)) {
////                    // Ensure that $file_path is correct and points to the right location
////                    $output .= '<img src="php/uploads/' . basename($file_path) . '" alt="image" style="max-width: 200px; max-height: 200px;">';
////                } else {
////                    $output .= '<p>'. $message .'</p>';
////                }
////
////                $output .= '</div></div>';
////            } else {
////                $output .= '<div class="chat incoming">
////                            <img class="profile-img" src="php/images/'.$row['img'].'" alt="">
////                            <div class="details">
////                            <div class="timestamp">'.$timestamp.'</div>';
////                if (!empty($file_path)) {
////                    // Ensure that $file_path is correct and points to the right location
////                    $output .= '<img src="php/uploads/' . basename($file_path) . '" alt="image" style="max-width: 200px; max-height: 200px;">';
////                } else {
////                    $output .= '<p>'. $message .'</p>';
////                }
////
////                $output .= '</div></div>';
////            }
////        }
////    } else {
////        $output .= '<div class="text">No messages are available. Once you send a message they will appear here.</div>';
////    }
////    echo $output;
////} else {
////    header("location: ../login.php");
////}
////?>
<!---->
<!---->
<?php
//global $conn;
//session_start();
//if(isset($_SESSION['unique_id'])){
//    include_once "config.php";?>
<!--    <head><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">-->
<!--    </head>-->
<!--        --><?php
//    $outgoing_id = $_SESSION['unique_id'];
//    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
//    $output = "";
//    $sql = "SELECT messages.*, users.img
//            FROM messages
//            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
//            WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
//            OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id})
//            ORDER BY msg_id";
//    $query = mysqli_query($conn, $sql);
//    if(mysqli_num_rows($query) > 0){
//        while($row = mysqli_fetch_assoc($query)){
//            $message = $row['msg'];
//            $file_path = $row['file_path'];
//            $timestamp = date("g:i A", strtotime($row['timestamp'])); // Format timestamp
//            $msg_id = $row['msg_id']; // Get message ID
//
//            if($row['outgoing_msg_id'] === $outgoing_id){
//                $output .= '<div class="chat outgoing" data-msg-id="'.$msg_id.'">
//                            <div class="details">
//                            <div class="timestamp">'.$timestamp.'</div>';
//                if (!empty($file_path)) {
//                    // Ensure that $file_path is correct and points to the right location
//                    $output .= '<img src="php/uploads/' . basename($file_path) . '" alt="image" style="max-width: 200px; max-height: 200px;">';
//                } else {
//                    $output .= '<p>'. $message .'</p>';
//                }
//
//                $output .= '<div class="chat-actions">
//                <button  onclick="editMessage('.$row['msg_id'].')"><i class="fa fa-pencil"></i></button>
//                <button class="delete-btn" onclick="deleteMessage('.$row['msg_id'].')"><i class="fa fa-trash"></i></button>
//            </div>';
//
//
//                $output .= '</div></div>';
//            } else {
//                $output .= '<div class="chat incoming" data-msg-id="'.$msg_id.'">
//                            <img class="profile-img" src="php/images/'.$row['img'].'" alt="">
//                            <div class="details">
//                            <div class="timestamp">'.$timestamp.'</div>';
//                if (!empty($file_path)) {
//                    // Ensure that $file_path is correct and points to the right location
//                    $output .= '<img src="php/uploads/' . basename($file_path) . '" alt="image" style="max-width: 200px; max-height: 200px;">';
//                } else {
//                    $output .= '<p>'. $message .'</p>';
//                }
//                $output .= '</div></div>';
//            }
//        }
//    } else {
//        $output .= '<div class="text">No messages are available. Once you send a message they will appear here.</div>';
//    }
//    echo $output;
//} else {
//    header("location: ../login.php");
//}
//?>


<?php
global $conn;
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
?>
    <head><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
    <?php
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";
    $sql = "SELECT messages.*, users.img 
            FROM messages 
            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
            OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) 
            ORDER BY msg_id";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $message = $row['msg'];
            $file_path = $row['file_path'];
            $timestamp = date("g:i A", strtotime($row['timestamp'])); // Format timestamp
            $msg_id = $row['msg_id']; // Get message ID

            if ($row['outgoing_msg_id'] === $outgoing_id) {
                $output .= '<div class="chat outgoing" data-msg-id="' . $msg_id . '">
                            <div class="details">
                            <div class="timestamp">' . $timestamp . '</div>';

                if (!empty($file_path)) {
                    // Display file
                    $output .= '<img src="php/uploads/' . basename($file_path) . '" alt="file" style="max-width: 200px; max-height: 200px;">';
                    // Only show delete button for file messages
                    $output .= '<div class="chat-actions">
                                <button class="delete-btn" onclick="deleteMessage(' . $msg_id . ')"><i class="fa fa-trash"></i></button>
                                </div>';
                } else {
                    // Display text message
                    $output .= '<p>' . htmlspecialchars($message) . '</p>';
                    // Show both edit and delete buttons for text messages
                    $output .= '<div class="chat-actions">
                                <button onclick="editMessage(' . $msg_id . ')"><i class="fa fa-pencil"></i></button>
                                <button class="delete-btn" onclick="deleteMessage(' . $msg_id . ')"><i class="fa fa-trash"></i></button>
                                </div>';
                }

                $output .= '</div></div>';
            } else {
                $output .= '<div class="chat incoming" data-msg-id="' . $msg_id . '">
                            <img class="profile-img" src="php/images/' . $row['img'] . '" alt="">
                            <div class="details">
                            <div class="timestamp">' . $timestamp . '</div>';

                if (!empty($file_path)) {
                    // Display file
                    $output .= '<img src="php/uploads/' . basename($file_path) . '" alt="file" style="max-width: 200px; max-height: 200px;">';
                } else {
                    // Display text message
                    $output .= '<p>' . htmlspecialchars($message) . '</p>';
                }

                $output .= '</div></div>';
            }
        }
    } else {
        $output .= '<div class="text">No messages are available. Once you send a message they will appear here.</div>';
    }
    echo $output;
} else {
    header("location: ../login.php");
}
?>
