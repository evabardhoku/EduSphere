<?php
// Include necessary files
global $USER, $user_data;
include_once "classes/config.php";
include_once "classes/connect.php"; // Ensure this file contains the Database class
include_once "classes/group.php";
include_once "classes/image.php";

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize classes
$image_class = new Image();
$group_class = new Group($db);

// Fetch groups for the logged-in user
$groups = $group_class->get_my_groups($USER['userid']);

// Handle leaving the group
if (isset($_GET['leave_group']) && isset($_GET['id'])) {
    $group_id = $_GET['id'];
    $user_id = $USER['userid'];

    if ($group_class->remove_member($group_id, $user_id)) {
        echo "<p>You have successfully left the group.</p>";
    } else {
        echo "<p>Failed to leave the group.</p>";
    }

// Redirect to avoid resubmission of form on refresh
header("Location: profile.php?section=groups&id=" . urlencode($USER['userid']));
exit();
}
?>

<div style="min-height: 400px; width: 100%; background-color: white; text-align: center;">
    <div style="padding: 20px;">
        <!-- Create Group Button -->
        <div style="margin-bottom: 20px;">
            <a href="create_group.php">
                <input id="post_button" type="button" value="Create Group" style="float:none; margin-right:10px; background-color: #1b9186; width:auto;">
            </a>
        </div>

        <?php
        if (is_array($groups)) {
            foreach ($groups as $group) {
                // Default cover image path
                $default_cover_image = "images/cover_image.jpg";

                // Determine if a valid cover image exists
                $image_path = isset($group['cover_image']) ? $group['cover_image'] : $default_cover_image;

                // Get the thumbnail image path
                $thumbnail_image = $image_class->get_thumb_profile($image_path);

                ?>
                <div style="display: inline-block; width: 200px; background-color: #eee; margin: 10px; padding: 10px; text-align: center;">
                    <a style="text-decoration: none;" href="group.php?id=<?php echo htmlspecialchars($group['id']); ?>">
                        <?php
                        // Check if the file exists
                        if (file_exists($image_path)) {
                            // Get the thumbnail image
                            $thumbnail_image = $image_class->get_thumb_profile($image_path);
                            echo '<img src="' . htmlspecialchars($thumbnail_image) . '" alt="' . htmlspecialchars($group['name']) . '" style="width: 100%; height: auto;">';
                        } else {
                            // Display default image if cover image is not found
                            echo '<img src="' . htmlspecialchars($default_cover_image) . '" alt="Default Cover Image" style="width: 100%; height: auto;">';
                        }
                        ?>
                        <br>
                        <?php echo htmlspecialchars($group['name']); ?>
                        <br><br>
                        <span><?php echo htmlspecialchars($group['type']); ?></span>
                    </a>
                    <br><br>
                    <!-- Leave Group Button -->
                    <a href="profile.php?section=groups&id=<?php echo urlencode($USER['userid']); ?>&leave_group=true" onclick="return confirm('Are you sure you want to leave this group?');">
                        <input id="post_button" type="button" value="Leave Group" style="font-size:11px;background-color: #91261b;width:auto;">
                    </a>

                </div>
                    <?php
            }
        } else {
            echo "No groups found.";
        }
        ?>
    </div>
</div>

<!-- Add the section for group invitations -->
<div style="min-height: 400px; width: 100%; background-color: white; text-align: center;">
    <div style="padding: 20px;">
        <?php
        // Fetch invitations for the logged-in user
        $query = "SELECT gi.id, gi.groupid, gi.inviter, g.name AS group_name, 
                         CONCAT(u.first_name, ' ', u.last_name) AS inviter_name, u.userid AS inviter_id, u.profile_image AS inviter_image
                  FROM group_invites gi
                  JOIN `group_table` g ON gi.groupid = g.id
                  JOIN `users` u ON gi.inviter = u.userid
                  WHERE gi.invited_user_id = ? AND gi.disabled = 0 AND u.owner IS NULL 
                  ORDER BY gi.id DESC";
        $stmt = $db->prepare($query);
        if ($stmt === false) {
            die("Error preparing statement: " . $db->error);
        }
        $stmt->bind_param("i", $USER['userid']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($invite = $result->fetch_assoc()) {
                // Default image for inviter
                $inviter_image = "images/user_male.jpg";

                // Check gender and set image accordingly
                if (isset($invite['inviter_image']) && file_exists($invite['inviter_image'])) {
                    $inviter_image = $image_class->get_thumb_profile($invite['inviter_image']);
                }

                echo "<div style='border: solid 1px #ccc; padding: 10px; margin-bottom: 10px; display: inline-block; width: 300px; background-color: #eee;'>";
                echo "<strong>You have been invited to join the group: </strong>";
                echo "<a href='group.php?id=" . htmlspecialchars($invite['groupid']) . "'>";
                echo htmlspecialchars($invite['group_name']) . "</a>";
                echo "<br>";

                // Display inviter's profile image and name
                echo "<a href='profile.php?id=" . htmlspecialchars($invite['inviter_id']) . "'>";
                echo "<img src='" . htmlspecialchars($inviter_image) . "' alt='Inviter Profile Image' style='width: 50px; height: 50px; border-radius: 25px;'> ";
                echo htmlspecialchars($invite['inviter_name']) . "</a>";
                echo "<br><br>";

                // Action buttons
                echo "<a href='group_request_accept.php?group_id=" . htmlspecialchars($invite['groupid']) . "&user_id=" . htmlspecialchars($USER['userid']) . "&action=decline'>";
                echo "<input id='post_button' type='button' value='Decline' style='float:right; margin-right:10px; background-color: #916e1b; width:auto; padding: 5px'>";
                echo "</a>";
                echo "<a href='group_request_accept.php?group_id=" . htmlspecialchars($invite['groupid']) . "&user_id=" . htmlspecialchars($USER['userid']) . "&action=accept'>";
                echo "<input id='post_button' type='button' value='Accept' style='float:left; margin-right:10px; background-color: #1b9186; width:auto; padding: 5px'>";
                echo "</a>";

                echo "</div>";
            }
        } else {
            echo "No group invitations.";
        }
        ?>
    </div>

</div>

<script>
    function confirmLeaveGroup(groupId) {
        if (confirm("Are you sure you want to leave the group?")) {
            // Redirect to the leave group URL
            window.location.href = 'profile.php?section=groups&id=<?php echo $user_data['userid'] ?>;
        }
    }
</script>
