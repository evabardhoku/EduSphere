<div style="width: 800px;margin:auto;font-size: 30px;">
<div style="padding: 20px;">
        <?php
        include_once "classes/connect.php";
        include_once "classes/config.php";
        include_once "classes/Group.php";
        include_once "classes/User.php";
        include_once "classes/Image.php";

        global $group_data, $db;
        $group = new Group($db); // Ensure $db is properly initialized in connect.php
        $user_class = new User($db);
        $image_class = new Image();

        // Check if the user is a member of the group
        if (group_access($_SESSION['mybook_userid'], $group_data, 'member')):

            // Handle removing a confirmed member (only admin access)
            if (isset($_GET['remove_confirmed']) && group_access($_SESSION['mybook_userid'], $group_data, 'admin')) {

                // Remove member
                $group->remove_member($group_data['id'], $_GET['remove_confirmed']);
                echo "This user was successfully removed from the group!<br><br>";

                // Show removed user information
                $FRIEND_ROW = $user_class->get_user($_GET['remove_confirmed']);
                include("user.php");

                echo '<br><br>
                <a href="group/'.$group_data['id'].'/members">
                    <input id="post_button" type="button" value="Back" style="font-size:11px;margin-right:10px;background-color: #1b9186;width:auto;">
                </a>';
            }
            // Handle editing access for a member (only admin access)
            elseif (isset($_GET['edit_access']) && group_access($_SESSION['mybook_userid'], $group_data, 'admin')) {

                // Check if the form is submitted to update access
                if (isset($_POST['role']) && isset($_POST['userid'])) {
                    $group->edit_member_access($group_data['id'], $_POST['userid'], $_POST['role']);
                    echo "User role updated successfully!<br><br>";
                }

                // Show the form for changing the user's role
                echo "<form method='post'>
                    Change user access<br><br>
                    <div style='background-color:orange;color:white;padding:1em;text-align:center;'>
                    Warning! Giving users admin access also gives them the power to remove you as admin.</div>";

                // Display user information
                $FRIEND_ROW = $user_class->get_user($_GET['edit_access']);
                include("user.php");

                // Get current role
                $role = $group->get_member_role($group_data['id'], $_GET['edit_access']);
                echo '<br><br>
                    <select name="role" style="padding:5px;width:200px;">
                        <option value="'.$role.'">'.$role.'</option>
                        <option value="member">member</option>
                        <option value="admin">admin</option>
                    </select>
                    <input type="hidden" name="userid" value="'.htmlspecialchars($_GET['edit_access']).'">
                    <br><br>
                    <input id="post_button" type="submit" value="Save" style="font-size:11px;background-color: #91261b;width:auto;">
                    <a href="group/'.$group_data['id'].'/members">
                        <input id="post_button" type="button" value="Cancel" style="font-size:11px;background-color: #1b9186;width:auto;">
                    </a>
                </form>';
            }
            // Handle confirming removal of a member (only admin access)
            elseif (isset($_GET['remove']) && group_access($_SESSION['mybook_userid'], $group_data, 'admin')) {

                echo "Are you sure you want to remove this user from the group?<br><br>";

                // Display user information before removing
                $FRIEND_ROW = $user_class->get_user($_GET['remove']);
                include("user.php");

                echo '<br><br>
    <a href="group.php?section=members&id=' . htmlspecialchars($group_data['id']) . '&remove_confirmed=' . htmlspecialchars($FRIEND_ROW['userid']) . '">
        <input id="post_button" type="button" value="Remove" style="font-size:11px;background-color: #91261b;width:auto;">
    </a>
    <a href="group.php?section=members&id=' . htmlspecialchars($group_data['id']) . '">
        <input id="post_button" type="button" value="Cancel" style="font-size:11px;background-color: #1b9186;width:auto;">
    </a>';
            }

            // Display group members
            else {

                $members = $group->get_members($group_data['id']);
                if (is_array($members)) {
                    foreach ($members as $member) {
                        $FRIEND_ROW = $user_class->get_user($member['userid']);
                        include("user_group_member.inc.php");
                    }
                } else {
                    echo "This group has no members.";
                }
            }
        else:
            echo "You don't have access to this content!";
        endif;
        ?>
    </div>
</div>
