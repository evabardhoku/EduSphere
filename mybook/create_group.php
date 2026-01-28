<?php
session_start();

include("classes/connect.php");
include("classes/group.php");

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_name = $_POST['group_name'];
    $description = $_POST['description'];
    $type = $_POST['type']; // Type of the group (public, private, etc.)

    if (!empty($group_name)) {
        $group = new Group();
        $user_id = $_SESSION['mybook_userid'];

        // Handle file upload
        $cover_image = '';
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['cover_image']['tmp_name'];
            $fileName = $_FILES['cover_image']['name'];
            $fileSize = $_FILES['cover_image']['size'];
            $fileType = $_FILES['cover_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Define allowed file extensions and size limit
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
                // Create a unique name for the file
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = './uploads/';
                $destFilePath = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destFilePath)) {
                    $cover_image = $newFileName;
                } else {
                    echo "<div style='color: red;'>Error uploading file. Please try again.</div>";
                }
            } else {
                echo "<div style='color: red;'>Invalid file type or size. Please upload a valid image.</div>";
            }
        }

        // Create the data array to pass to the create_group method
        $data = [
            'userid' => $user_id,
            'group_name' => $group_name,
            'description' => $description,
            'cover_image' => $cover_image,
            'type' => $type,
            'owner_id' => $user_id, // Setting the owner ID to the user creating the group
        ];

        // Create the group
        $result = $group->create_group($data);

        if ($result) {
            // Redirect to the group page or a success page
            header("Location: profile.php?section=groups&id=$user_id");
            die;
        } else {
            echo "<div style='color: red;'>Failed to create the group. Please try again.</div>";
        }
    } else {
        echo "<div style='color: red;'>Group name cannot be empty.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create a Group</title>
</head>
<body style="font-family: Arial, sans-serif;">

<div style="width: 300px; margin: auto; padding: 20px; background-color: #f3f3f3; border-radius: 5px;">
    <h2>Create a Group</h2>

    <form method="post" enctype="multipart/form-data">
        <div style="margin-bottom: 15px;">
            <label for="group_name">Group Name:</label>
            <input type="text" name="group_name" id="group_name" required style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" style="width: 100%; padding: 8px;"></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="cover_image">Cover Image:</label>
            <input type="file" name="cover_image" id="cover_image" accept="image/*" style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="type">Group Type:</label>
            <select name="type" id="type" style="width: 100%; padding: 8px;">
                <option value="public">Public</option>
                <option value="private">Private</option>
                <!-- Add more options if needed -->
            </select>
        </div>

        <input type="submit" value="Create Group" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
    </form>
</div>

</body>
</html>
