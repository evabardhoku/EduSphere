<?php
include("classes/autoload.php");

// Check if user is logged in
if (!isset($_SESSION['mybook_userid'])) {
    header("Location: login.php");
    die();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Get the current user profile image
    $user = new User();
    $user_data = $user->get_user($id);

    // Handle file upload
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // Validate file
        if ($file['error'] == 0) {
            $upload_dir = "uploads/profile_images/";
            $file_path = $upload_dir . basename($file['name']);

            // Move file to the upload directory
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Update the profile image path in the database
                $image_class = new Image();
                $image_class->update_profile_image($id, $file_path);

                // Redirect back to the profile page
                header("Location: profile.php?id=$id");
                die();
            } else {
                echo "File upload failed.";
            }
        } else {
            echo "Error uploading file.";
        }
    }
}
?>
