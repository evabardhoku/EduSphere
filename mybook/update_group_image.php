<?php
include("classes/autoload.php");
include("classes/group.php");
include("classes/config.php");

if (!isset($_SESSION['mybook_userid'])) {
    header("Location: login.php");
    die();
}

$user_id = $_SESSION['mybook_userid'];
$group_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($group_id > 0) {
    $group = new Group();
    $group_data = $group->get_group($group_id);

    if ($group_data['owner_id'] != $user_id) {
        echo "<div style='text-align:center;font-size:12px;color:red;'>You do not have permission to change the group image!</div>";
        exit();
    }

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_exts)) {
            if ($file_size <= 5000000) { // 5MB max
                $upload_dir = 'uploads/';
                $new_file_name = $upload_dir . "group_" . $group_id . "." . $file_ext;

                if (move_uploaded_file($file_tmp, $new_file_name)) {
                    $result = $group->update_cover_image($group_id, $new_file_name);

                    if ($result) {
                        header("Location: group.php?id=" . $group_id);
                        exit();
                    } else {
                        echo "<div style='text-align:center;font-size:12px;color:red;'>Error updating group image!</div>";
                    }
                } else {
                    echo "<div style='text-align:center;font-size:12px;color:red;'>Failed to upload image!</div>";
                }
            } else {
                echo "<div style='text-align:center;font-size:12px;color:red;'>File size exceeds the limit!</div>";
            }
        } else {
            echo "<div style='text-align:center;font-size:12px;color:red;'>Invalid file type!</div>";
        }
    } else {
        echo "<div style='text-align:center;font-size:12px;color:red;'>No file uploaded!</div>";
    }
} else {
    echo "<div style='text-align:center;font-size:12px;color:red;'>Invalid group ID!</div>";
}
?>
