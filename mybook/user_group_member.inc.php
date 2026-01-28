<div id="friends" style="display: inline-block; vertical-align: top; width: 200px; background-color: #eee;">
    <?php
    global $image_class, $group_data;

    // Make sure $FRIEND_ROW is defined
    if (isset($FRIEND_ROW) && !empty($FRIEND_ROW)) {
        // Determine the profile image
        $image = "images/user_male.jpg";
        if ($FRIEND_ROW['gender'] == "Female") {
            $image = "images/user_female.jpg";
        }

        if (file_exists($FRIEND_ROW['profile_image'])) {
            $image = $image_class->get_thumb_profile($FRIEND_ROW['profile_image']);
        }
        ?>

        <a href="profile.php?id=<?php echo htmlspecialchars($FRIEND_ROW['userid']); ?>">
            <img id="friends_img" src="<?php echo htmlspecialchars($image); ?>">
            <br>
            <?php echo htmlspecialchars($FRIEND_ROW['first_name']) . " " . htmlspecialchars($FRIEND_ROW['last_name']); ?>
            <br>

            <?php
            // Determine online status

$online = "";

// Determine online status
if (isset($FRIEND_ROW['online'])) {
    if ($FRIEND_ROW['online'] == 1) {
        // Online: green text
        echo "<span style='color: green;font-size: 15px'>Online</span>";
    } elseif ($FRIEND_ROW['online'] == 0) {
        // Not online: red text
        echo "<span style='color: red; font-size: 15px'>Not online</span>";
    } else {
        // Unknown status: default color
        echo "<span style='color: grey;'>Unknown status</span>";
    }
} else {
    // Unknown status: default color
    echo "<span style='color: grey;'>Unknown status</span>";
}
?>



            <span style="color: grey; font-size: 11px; font-weight: normal;"><?php echo $online; ?></span>
            <br>
            <span style="display: inline-block; margin: 6px;"><?php echo htmlspecialchars($member['role']); ?></span>

            <?php if (group_access($_SESSION['mybook_userid'], $group_data, 'admin')): ?>
                <br style="clear: both;">
                <a href="group.php?section=members&id=<?php echo htmlspecialchars($group_data['id']); ?>&remove=<?php echo htmlspecialchars($FRIEND_ROW['userid']); ?>">
                    <input id="post_button" type="button" value="Remove" style="font-size:11px; margin-right:10px; background-color: #916e1b; width:auto;">
                </a>
                <a href="group.php?section=members&id=<?php echo htmlspecialchars($group_data['id']); ?>&edit_access=<?php echo htmlspecialchars($FRIEND_ROW['userid']); ?>">
                    <input id="post_button" type="button" value="Edit Access" style="font-size:11px; margin-right:10px; background-color: #1b9186; width:auto;">
                </a>
            <?php endif; ?>
        </a>
        <?php
    } else {
        // Handle case where $FRIEND_ROW is not set
        echo "<p>User details are not available.</p>";
    }
    ?>
</div>
