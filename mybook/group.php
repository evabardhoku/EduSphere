<?php

ob_start();


global $image_class, $id;
include("classes/autoload.php");
include("classes/group.php");
include("classes/group_post.php"); // Include the new GroupPost class
include("classes/config.php"); // Include the new GroupPost class


$user_id = $_SESSION['mybook_userid'];

$login = new Login();
if (!isset($_SESSION['mybook_userid'])) {
    header("Location: login.php");
    die();
}

$user_data = $login->check_login($_SESSION['mybook_userid'], false);
$USER = $user_data;

// Initialize group data
$group_data = array();
$group_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($group_id > 0) {
    $group = new Group();
    $group_data = $group->get_group($group_id);

    if (is_array($group_data) && !empty($group_data)) {
        $user_data = $group_data;
    } else {
        $user_data = null;
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['group_name']) && isset($_SESSION['mybook_userid'])) {
        $settings_class = new Settings();
        $result = $settings_class->save_group_settings($group_id, $_POST);

        if ($result) {
            echo "<div style='text-align:center;font-size:14px;color:green;'>Settings updated successfully!</div>";
        } else {
            echo "<div style='text-align:center;font-size:12px;color:red;'>Error updating settings!</div>";
        }
    } elseif (isset($_FILES['file'])) {
        // Ensure user is logged in
        if (isset($_SESSION['mybook_userid'])) {
            $user_id = $_SESSION['mybook_userid'];
            $group_post = new GroupPost();
            $result = $group_post->create_post($user_id, $group_id, $_POST, $_FILES);

            if ($result === "") {
                header("Location: group.php?id=" . $group_id);
                exit(); // Use exit() after header() to ensure no further code execution
            } else {
                echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>";
                echo "<br>The following errors occurred:<br><br>";
                echo $result;
                echo "</div>";
            }
        } else {
            echo "<div style='text-align:center;font-size:12px;color:red;'>User not logged in!</div>";
        }
    } else {
        echo "<div style='text-align:center;font-size:12px;color:red;'>Invalid request!</div>";
    }
}

// Collect posts
$group_post = new GroupPost();
$posts = $group_post->get_posts_by_group($group_id);

// Collect members
$user = new User();
$members = $user->get_following($group_id, "group");

// Image class instance
$image_class = new Image();

// Check if this is from a notification
if (isset($_GET['notif'])) {
    notification_seen($_GET['notif']);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Group | Mybook</title>
</head>
<style type="text/css">
    /* Your existing CSS styles */
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
        margin-top: -300px;
        border-radius: 50%;
        border: solid 2px white;
    }

    #menu_buttons {
        width: 100px;
        display: inline-block;
        margin: 2px;
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
        background: linear-gradient(375deg,  #00ccd9, #41d3ca);

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
</style>
<body style="font-family: tahoma; background-color: #d0d8e4;">
<br>
<?php include("header.php"); ?>

<!-- Change group image area -->

<div id="change_group_image" style="display:none;position:absolute;width: 100%;height: 100%;background-color: #000000aa;">
    <div style="max-width:600px;margin:auto;min-height: 400px;flex:2.5;padding: 20px;padding-right: 0px;">

        <form method="post" action="update_group_image.php?id=<?php echo htmlspecialchars($group_id); ?>" enctype="multipart/form-data">
            <div style="border:solid thin #aaa; padding: 10px;background-color: white;">
                <input type="file" name="file"><br>
                <input id="post_button" type="submit" style="width:120px;" value="Change">
                <br>
                <div style="text-align: center;">
                    <br><br>
                    <?php
                    if ($user_data && isset($user_data['cover_image'])) {
                        echo "<img src='{$user_data['cover_image']}' style='max-width:500px;'>";
                    } else {
                        echo "Group image not found.";
                    }
                    ?>
                </div>
            </div>
        </form>


    </div>
</div>

<!-- Cover area -->
<div style="width: 800px;margin:auto;min-height: 400px;">
    <div style="background-color: white;text-align: center;color: #405d9b">
        <?php
        $cover_image = "images/cover_image.jpg";
        if (isset($user_data['cover_image']) && file_exists($user_data['cover_image'])) {
            $cover_image = $image_class->get_thumb_cover($user_data['cover_image']);
        }
        ?>
        <img src="<?php echo htmlspecialchars($cover_image); ?>" style="width:100%;">

        <?php
        // Assuming $user_id is already set from the session and $group_data is available and contains 'owner_id'
        if (isset($group_data['owner_id']) && $group_data['owner_id'] == $user_id): ?>
            <a onclick="show_change_group_image(event)" style="text-decoration: none; color: #f0f;" href="#">Change Group Image</a>
        <?php endif; ?>

        <br>
        <div style="font-size: 20px;color: black;">
            <?php echo isset($user_data['name']) ? htmlspecialchars($user_data['name']) : 'Group Name'; ?>
        </div>
        <br>



        <!-- Navigation Menu -->
        <div id="menu_container">
            <a href="group.php?id=<?php echo htmlspecialchars($user_data['id']); ?>">
                <div id="menu_buttons">Discussion</div>
            </a>
            <a href="group.php?section=about&id=<?php echo htmlspecialchars($user_data['id']); ?>">
                <div id="menu_buttons">About</div>
            </a>
            <a href="group.php?section=members&id=<?php echo htmlspecialchars($user_data['id']); ?>">
                <div id="menu_buttons">Members</div>
            </a>
            <a href="group.php?section=photos&id=<?php echo htmlspecialchars($user_data['id']); ?>">
                <div id="menu_buttons">Photos</div>
            </a>

            <?php if ($user_data['owner_id'] == $user_id): ?>
                <a href="group.php?section=requests&id=<?php echo htmlspecialchars($user_data['id']); ?>">
                    <div id="menu_buttons">Requests</div>
                </a>
                <a href="group.php?section=settings&id=<?php echo htmlspecialchars($user_data['id']); ?>">
                    <div id="menu_buttons">Settings</div>
                </a>
            <?php endif; ?>
        </div>

        <!-- Group Actions -->
        <div id="group_actions">
             <!-- Check if user is not a member -->
            <?php if (!group_access($user_id, $group_data, 'member')): ?>
                <!-- Check if user has not sent a request -->
                <?php if (!group_access($user_id, $group_data, 'request')): ?>
                    <a href="join.php?id=<?php echo htmlspecialchars($user_data['id']); ?>">
                        <input id="post_button" type="button" value="Join Group" style="margin-right:10px;background-color: #821b91;width:auto;">
                    </a>
                <?php else: ?>
                    <input id="post_button" type="button" value="Request sent" style="margin-right:10px;background-color: #821b91;width:auto;">
                <?php endif; ?>
            <?php else: ?>
                <p>User is a member.</p>
            <?php endif; ?>

            <!-- Check if user is a member -->
            <?php if (group_access($user_id, $group_data, 'member')): ?>
                <a href="group.php?section=invite&id=<?php echo htmlspecialchars($user_data['id']); ?>">
                    <input id="post_button" type="button" value="Invite" style="margin-right:10px;background-color: #1b9186;width:auto;">
                </a>
            <?php endif; ?>
        </div>


    </div>

    </div>
</div>

<!-- Below cover area -->
<?php
// Fetch group data including privacy status
$group_data = $group->get_group($group_id);
$is_private = $group_data['type'] === 'private'; // Adjust this if your privacy is represented differently

// Check if the user is a member or an admin
$is_member_or_admin = false;
if ($is_private) {
    $user_role = $group->get_user_role_in_group($user_id, $group_id); // Create this method to fetch user role
    if ($user_role === 'admin' || $user_role === 'member') {
        $is_member_or_admin = true;
    }
}

if ($is_private && !$is_member_or_admin) {
    // User is not a member or admin of a private group
    echo "<div style='text-align:center;font-size:14px;color:red;'>The group is private! Send a request to join it.</div>";
} else {
    // User has access to the group content
    $section = "default";
    if (isset($_GET['section'])) {
        $section = $_GET['section'];
    }

    if ($section == "default") {
        include("group_content_default.php");
    } elseif ($section == "requests") {
        include("group_content_requests.php");
    } elseif ($section == "invite") {
        include("group_content_invite.php");
    } elseif ($section == "invited") {
        include("group_content_invited.php");
    } elseif ($section == "members") {
        include("group_content_members.php");
    } elseif ($section == "about") {
        include("group_content_about.php");
    } elseif ($section == "settings") {
        include("group_content_settings.php");
    } elseif ($section == "photos") {
        include("group_content_photos.php");
    }
}


ob_end_flush();


?>
</body>
</html>

<script type="text/javascript">
    function show_change_group_image(event) {
        event.preventDefault();
        var group_image = document.getElementById("change_group_image");
        group_image.style.display = "block";
    }

    function hide_change_group_image() {
        var group_image = document.getElementById("change_group_image");
        group_image.style.display = "none";
    }


</script>
