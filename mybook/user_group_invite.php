<div id="friends" style="display: inline-block; vertical-align: top; width: 200px; background-color: #eee;">
    <?php
    global $image_class;
    global $group_data;

    // Ensure $FRIEND_ROW is defined and set correctly
    if (isset($FRIEND_ROW) && !empty($FRIEND_ROW)) {
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
        </a>
        <?php if (!is_invited($group_data['id'], $FRIEND_ROW['userid'])): ?>
        <a href="invite.php?group_id=<?php echo htmlspecialchars($group_data['id']); ?>&user_id=<?php echo htmlspecialchars($FRIEND_ROW['userid']); ?>">
            <input id="post_button" type="button" value="Invite" style="font-size:11px; margin-right:10px; background-color: #1b9186; width:auto;">
        </a>
    <?php else: ?>
        "Invited"
    <?php endif;
} else {
    echo "<p>User details are not available.</p>";
}
    ?>
</div>
