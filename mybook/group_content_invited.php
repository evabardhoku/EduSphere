<div style="min-height: 400px; width: 100%; background-color: white; text-align: center;">
    <div style="padding: 20px;">
        <?php
        // Include necessary classes
        include_once "classes/autoload.php";
        include_once "classes/image.php";
        include_once "classes/post.php";
        include_once "classes/user.php";
        include_once "classes/functions.php";
        include_once "classes/Group.php"; // Ensure this is included to use Group class

        // Instantiate necessary classes
        $image_class = new Image();
        $post_class = new Post();
        $user_class = new User();
        $group = new Group(); // Initialize the Group class

        // Check if user session is set
        if (!isset($_SESSION['mybook_userid'])) {
            die("User session not found.");
        }

        // Ensure the group ID is provided
        if (isset($_GET['group_id'])) {
            $group_id = intval($_GET['group_id']);

            // Get invited users for the group
            $invites = $group->get_invited($group_id); // Fetch invitations for the group

            if (is_array($invites) && !empty($invites)) {
                foreach ($invites as $invite) {
                    // Check if 'userid' key exists in the $invite array
                    if (isset($invite['userid'])) {
                        // Get user details for the invite
                        $INVITER_ROW = $user_class->get_user($invite['inviter']);
                        $FRIEND_ROW = $user_class->get_user($invite['userid']);

                        // Check if user data is fetched
                        if ($FRIEND_ROW) {
                            // Include the user group request view
                            include("user_group_request.inc.php");
                        } else {
                            echo "User data is incomplete or missing.";
                        }
                    } else {
                        // Display a message if 'userid' is missing
                        echo "User ID not found in invite.";
                    }
                }
            } else {
                // Display a message if no invites are found
                echo "No invitations were found!";
            }
        } else {
            echo "Invalid group ID.";
        }
        ?>
    </div>
</div>
