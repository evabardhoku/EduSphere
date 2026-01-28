<div style="width: 800px;margin:auto;font-size: 30px;">
<div style="padding: 20px;">
        <?php
        global $group, $group_data, $USER;

        if (group_access($_SESSION['mybook_userid'], $group_data, 'member')):
            $image_class = new Image();
            $post_class = new Post();
            $user_class = new User();

            $followers = $group->get_invites($group_data['id'], $USER['userid'], "user");

            if (is_array($followers)) {
                foreach ($followers as $follower) {
                    $FRIEND_ROW = $user_class->get_user($follower['userid']);
                    // Pass $FRIEND_ROW directly to the included file
                    include("user_group_invite.php");
                }
            } else {
                echo "No followers to invite were found!";
            }
        else:
            echo "You must be a member to invite others";
        endif;
        ?>

    </div>
</div>