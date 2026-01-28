<div id="friends" style="display: inline-block; width: 200px; background-color: #eee;">
    <?php
    global $group_data, $image_class;

    // Ensure FRIEND_ROW array is defined and contains 'userid'
    if (isset($FRIEND_ROW['userid'])):
        // Default image
        $image = "images/user_male.jpg";

        // Check gender and set image accordingly
        if (isset($FRIEND_ROW['gender']) && $FRIEND_ROW['gender'] == "Female") {
            $image = "images/user_female.jpg";
        }

        // Check if profile image exists and update if available
        if (isset($FRIEND_ROW['profile_image']) && file_exists($FRIEND_ROW['profile_image'])) {
            $image = $image_class->get_thumb_profile($FRIEND_ROW['profile_image']);
        }
        ?>
        <!-- User profile link and image -->
        <a href="profile.php?id=<?php echo htmlspecialchars($FRIEND_ROW['userid']); ?>">
            <img id="friends_img" src="<?php echo htmlspecialchars($image); ?>" alt="Profile Image">
            <br>
            <?php
            // Display user's name
            if (isset($FRIEND_ROW['first_name']) && isset($FRIEND_ROW['last_name'])) {
                echo htmlspecialchars($FRIEND_ROW['first_name'] . " " . $FRIEND_ROW['last_name']);
            }
            ?>
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
            <br style="clear: both;">

            <?php
            // Display invitation information if applicable
            if (isset($URL[2]) && $URL[2] == "invited" && isset($INVITER_ROW)) {
                echo "You were invited by " . htmlspecialchars($INVITER_ROW['first_name'] . " " . $INVITER_ROW['last_name']) . "<br><br>";
            }
            ?>

            <a href="group_request_accept.php?group_id=<?php echo htmlspecialchars($group_data['id']); ?>&user_id=<?php echo htmlspecialchars($FRIEND_ROW['userid']); ?>&action=decline">
                <input id="post_button" type="button" value="Decline" style="float:right; margin-right:10px; background-color: #916e1b; width:auto; padding: 5px">
            </a>
            <a href="group_request_accept.php?group_id=<?php echo htmlspecialchars($group_data['id']); ?>&user_id=<?php echo htmlspecialchars($FRIEND_ROW['userid']); ?>&action=accept">
                <input id="post_button" type="button" value="Accept" style="float:left; margin-right:10px; background-color: #1b9186; width:auto; padding: : 5px">
            </a>

            <?php
    else:
        // Handle case where FRIEND_ROW does not contain 'userid'
        echo "User data is incomplete or missing.";
    endif;
    ?>
</div>
