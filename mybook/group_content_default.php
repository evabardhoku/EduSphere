<?php
// Ensure that you are including the necessary files and initializing required classes
global $group_id;
include_once "classes/functions.php";
include_once "classes/group.php";
include_once "classes/user.php";
include_once "classes/group_post_logic.php";
include_once "classes/image.php";

// Fetch the current user ID from the session
if (!isset($_SESSION['mybook_userid'])) {
    die("User ID not found in session.");
}
$user_id = $_SESSION['mybook_userid'];

// Handle file upload and processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $uploadDir = 'uploads/'; // Directory to save uploaded files
        $fileName = (new Image())->generate_filename(10) . '.jpg'; // Generate unique filename
        $uploadFilePath = $uploadDir . $fileName;

        // Move uploaded file to the server
        if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            // Resize the uploaded image
            $image = new Image();
            $image->resize_image($uploadFilePath, $uploadFilePath, 800, 600); // Resize with max dimensions

            // Save post data to the database
            $groupPost = new GroupPost();
            $groupPost->create_post($user_id, $group_id, $_POST, $_FILES); // Adjusted method parameters

            echo "File uploaded and resized successfully.";
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "No file uploaded or upload error.";
    }
}

// Fetch group data
$group_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$group = new Group();
$group_data = $group->get_group($group_id);

if (!$group_data) {
    die("Group not found.");
}

// Collect posts
$postHandler = new PostHandler();
$posts = $postHandler->getPosts($group_id);

// Collect members
$image_class = new Image();
$user_class = new User();
$members = $group->get_members($group_id, 10); // Fetch members, adjust as needed
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group | Mybook</title>
    <style type="text/css">
        body {
            font-family: tahoma;
            background-color: #d0d8e4;
        }

        #blue_bar {
            height: 50px;
            background: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);
            color: #d9dfeb;
        }

        #search_box {
            width: 400px;
            height: 20px;
            border-radius: 5px;
            border: none;
            padding: 4px;
            font-size: 14px;
            background-image: url(search.png);
            background-repeat: no-repeat;
            background-position: right;
        }

        #textbox {
            width: 100%;
            height: 20px;
            border-radius: 5px;
            border: none;
            padding: 4px;
            font-size: 14px;
            border: solid thin grey;
            margin: 10px;
        }

        #profile_pic {
            width: 150px;
            border-radius: 50%;
            border: solid 2px white;
        }

        #menu_buttons {
            width: 100px;
            display: inline-block;
            margin: 2px;
            text-align: center;
            color: #405d9b;
            font-weight: bold;
            cursor: pointer;
        }

        #friends_img {
            width: 75px;
            float: left;
            margin: 8px;
        }

        #friends_bar {
            background-color: white;
            min-height: 400px;
            margin-top: 20px;
            color: #aaa;
            padding: 8px;
        }

        #friends {
            clear: both;
            font-size: 12px;
            font-weight: bold;
            color: #405d9b;
        }

        textarea {
            width: 100%;
            border: none;
            font-family: tahoma;
            font-size: 14px;
            height: 60px;
        }

        #post_button {
            float: right;
            background-color: #405d9b;
            border: none;
            color: white;
            padding: 4px;
            font-size: 14px;
            border-radius: 2px;
            width: 50px;
            min-width: 50px;
            cursor: pointer;
        }

        #post_bar {
            margin-top: 20px;
            background-color: white;
            padding: 10px;
        }

        #post {
            padding: 4px;
            font-size: 13px;
            display: flex;
            margin-bottom: 20px;
        }

        .flex-container {
            display: flex;
        }

        .flex-item {
            min-height: 400px;
        }

        .flex-item-1 {
            flex: 1;
        }

        .flex-item-2 {
            flex: 2.5;
            padding: 20px;
            padding-right: 0;
        }

        .post-container {
            border: solid thin #aaa;
            padding: 10px;
            background-color: white;
        }
    </style>
</head>
<body>
<!-- Group header -->

<!-- Group members and posts area -->
<div style="width: 800px; margin: auto; min-height: 400px;">
    <div class="flex-container">
        <!-- Members area -->
        <div class="flex-item flex-item-1">
            <div id="friends_bar">
                Members<br>
                <?php if(group_access($_SESSION['mybook_userid'],$group_data,'member')):?>
                    <?php

                    $image_class = new Image();
                    //$post_class = new Post();
                    $user_class = new User();

                    $members = $group->get_members($group_data['id'],10);

                    if(is_array($members)){

                        foreach ($members as $member) {
                            # code...
                            $FRIEND_ROW = $user_class->get_user($member['userid']);
                            include("user_group_member.inc.php");
                        }

                    }else{

                        echo "This group has no members";
                    }

                    ?>

                <?php endif; ?>
            </div>
        </div>

        <!-- Posts area -->
        <div class="flex-item flex-item-2">
            <?php if (!($group_data['type'] == 'public' && !group_access($user_id, $group_data, 'member'))): ?>
                <div class="post-container">
                    <form method="post" enctype="multipart/form-data">
                        <textarea name="content" placeholder="What's on your mind?" required></textarea>
                        <input type="file" name="file">
                        <input id="post_button" type="submit" value="Post">
                    </form>
                </div>
            <?php endif; ?>

            <!-- Posts -->
            <div id="post_bar">
                <?php
                if (isset($posts) && $posts) {
                    foreach ($posts as $ROW) {
                        $ROW_USER = $postHandler->getUser($ROW['user_id']);
                        include("post_template.inc.php"); // Include your post template
                    }
                } else {
                    echo "No posts available.";
                }

                // Get current URL for pagination
                $pg = pagination_link();
                ?>
                <a href="<?= $pg['next_page'] ?>">
                    <input id="post_button" type="button" value="Next Page" style="float: right;width:150px;">
                </a>
                <a href="<?= $pg['prev_page'] ?>">
                    <input id="post_button" type="button" value="Prev Page" style="float: left;width:150px;">
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
