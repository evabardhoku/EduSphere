<div style="display: flex;">
    <!-- Friends Area -->
    <div style="min-height: 400px; flex: 1;">
        <div id="friends_bar">
            <strong>Following</strong><br>
            <?php
            global $user_data;
            global $image_class;

            // Ensure $user is initialized
            $user = new User();
            $logged_in_user_id = $_SESSION['mybook_userid']; // Get the logged-in user's ID

            if (!empty($friends)) {
                foreach ($friends as $friend) {
                    $FRIEND_ROW = $user->get_user($friend['userid']);

                    if ($FRIEND_ROW) {
                        $online_status = is_user_online($FRIEND_ROW['userid']) ? "Online" : "Not Online";
                        $profile_image = "images/user_male.jpg";

                        if ($FRIEND_ROW['gender'] == "Female") {
                            $profile_image = "images/user_female.jpg";
                        }

                        if (file_exists($FRIEND_ROW['profile_image'])) {
                            $profile_image = $image_class->get_thumb_profile($FRIEND_ROW['profile_image']);
                        }

                        echo "<a href='profile.php?id=" . $FRIEND_ROW['userid'] . "' style='text-decoration: none; color: inherit;'>";
                        echo "<div style='display: flex; align-items: center; margin-bottom: 10px;'>";
                        echo "<img src='" . $profile_image . "' alt='Profile Picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;'>";
                        echo "<div>";
                        echo "<span style='font-weight: bold;'>" . $FRIEND_ROW['first_name'] . " " . $FRIEND_ROW['last_name'] . "</span><br>";
                        if ($FRIEND_ROW['userid'] !== $logged_in_user_id) {
                            // Show online status only if it's not the logged-in user's profile
                            echo "<span style='color: " . ($online_status == "Online" ? "green" : "red") . "; font-weight: bold;'>" . $online_status . "</span>";
                        }
                        echo "</div>";
                        echo "</div>";
                        echo "</a>";
                    }
                }
            } else {
                echo "You are not following anyone yet.";
            }
            ?>
        </div><br>

    </div><br>

    <!-- Posts Area -->
    <div style="min-height: 400px; flex: 2.5; padding: 20px; padding-right: 0px;">
        <div style="border: solid thin #aaa; padding: 10px; background-color: white;">
            <form method="post" enctype="multipart/form-data">
                <textarea name="post" placeholder="What's on your mind?"></textarea>
                <input type="file" name="file">
                <input id="post_button" type="submit" value="Post">
                <br>
            </form>
        </div>

        <!-- Display Posts -->
        <div id="post_bar">
            <?php
            if (!empty($posts)) {
                foreach ($posts as $ROW) {
                    $ROW_USER = $user->get_user($ROW['userid']);
                    include("post.php");
                }
            } else {
                echo "No posts to display.";
            }

            // Pagination Links
            $pg = pagination_link();
            ?>
            <div style="display: flex; justify-content: space-between;">
                <a href="<?= $pg['prev_page'] ?>">
                    <input id="post_button" type="button" value="Prev Page" style="width: 150px;">
                </a>
                <a href="<?= $pg['next_page'] ?>">
                    <input id="post_button" type="button" value="Next Page" style="width: 150px;">
                </a>
            </div>
        </div>
    </div>
</div>
