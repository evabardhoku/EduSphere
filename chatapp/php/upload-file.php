<?php
//global $conn;
//session_start();
//include_once "config.php"; // Include your database connection
//
//$response = ['status' => 'error', 'message' => '', 'files' => []];
//
//if(isset($_FILES['files']) && isset($_POST['incoming_id'])) {
//    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
//    $outgoing_id = $_SESSION['unique_id']; // Assuming you are storing outgoing_id in session
//
//    $response['status'] = 'success';
//
//    foreach($_FILES['files']['tmp_name'] as $key => $tmp_name) {
//        $file_name = basename($_FILES['files']['name'][$key]);
//        $file_size = $_FILES['files']['size'][$key];
//        $file_tmp = $_FILES['files']['tmp_name'][$key];
//        $file_type = $_FILES['files']['type'][$key];
//        $message = mysqli_real_escape_string($conn, $_POST['message']);
//
//        $upload_dir = "uploads/";
//        if(!is_dir($upload_dir)){
//            mkdir($upload_dir, 0755, true);
//        }
//        $file_path = $upload_dir . $file_name;
//
//        if(move_uploaded_file($file_tmp, $file_path)) {
//            $sql = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, file_path, file_type, file_size, current_timestamp)
//                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
//            $stmt = $conn->prepare($sql);
//            if ($stmt === false) {
//                $response['message'] = 'Failed to prepare SQL statement.';
//                echo json_encode($response);
//                exit();
//            }
//
//            $message = ''; // Empty message field for file uploads
//            $stmt->bind_param("iisssi", $incoming_id, $outgoing_id, $message, $file_path, $file_type, $file_size);
//
//            if($stmt->execute()) {
//                $response['files'][] = "File uploaded and saved to DB: " . $file_name;
//            } else {
//                $response['files'][] = "Failed to save in DB: " . $file_name;
//            }
//        } else {
//            $response['files'][] = "Failed to upload: " . $file_name;
//        }
//    }
//
//    echo json_encode($response);
//} else {
//    $response['message'] = "No files were uploaded.";
//    echo json_encode($response);
//}
//?>


<?php
global $conn;
session_start();
include_once "config.php"; // Include your database connection

$response = ['status' => 'error', 'message' => '', 'files' => []];

if (isset($_FILES['files']) && isset($_POST['incoming_id'])) {
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $outgoing_id = $_SESSION['unique_id']; // Assuming you are storing outgoing_id in session

    $response['status'] = 'success';

    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES['files']['name'][$key]);
        $file_size = $_FILES['files']['size'][$key];
        $file_tmp = $_FILES['files']['tmp_name'][$key];
        $file_type = $_FILES['files']['type'][$key];
        $message = isset($_POST['message']) ? mysqli_real_escape_string($conn, $_POST['message']) : ''; // Default to empty string if not set

        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $sql = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, file_path, file_type, file_size, timestamp) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())"; // Use NOW() to get current timestamp
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $response['message'] = 'Failed to prepare SQL statement.';
                echo json_encode($response);
                exit();
            }

            $stmt->bind_param("iisssi", $incoming_id, $outgoing_id, $message, $file_path, $file_type, $file_size);

            if ($stmt->execute()) {
                $response['files'][] = "File uploaded and saved to DB: " . $file_name;
            } else {
                $response['files'][] = "Failed to save in DB: " . $file_name;
            }
        } else {
            $response['files'][] = "Failed to upload: " . $file_name;
        }
    }

    echo json_encode($response);
} else {
    $response['message'] = "No files were uploaded.";
    echo json_encode($response);
}
?>
