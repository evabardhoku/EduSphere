<div style="width: 800px;margin:auto;font-size: 18px;">
    <div style="padding: 20px;">

        <?php
        global $group_data, $group;

        if (group_access($_SESSION['mybook_userid'], $group_data, 'moderator')):
            $image_class = new Image();
            $user_class = new User();

            // Get group requests
            $requests = $group->get_requests($group_data['id']); // Ensure $group_data['userid'] is the correct group ID

            if (is_array($requests) && !empty($requests)):
                foreach ($requests as $request):

                    // Check if 'userid' key exists in the $request array
                    if (isset($request['userid'])):
                        // Get user details for the request
                        $FRIEND_ROW = $user_class->get_user($request['userid']);
                        // Include the user group request view
                        include("user_group_request.inc.php");
                    else:
                        // Display a message if 'userid' is missing
                        echo "User ID not found in request.";
                    endif;
                endforeach;
            else:
                // Display a message if no requests are found
                echo "No requests were found!";
            endif;
        else:
            // Display a message if the user does not have access
            echo "You don't have access to this content!";
        endif;
        ?>

    </div>
</div>
