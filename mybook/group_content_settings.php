<div style="min-height: 400px;width:100%;background-color: transparent; text-align: center;">
    <div style="padding: 20px;max-width:350px;display: inline-block;text-align: center;">
        <form method="post" enctype="multipart/form-data">
            <?php
            global $group_data;
            include_once "classes/settings.php";

            $settings_class = new Settings();

            // Retrieve settings for the group
            $settings = $settings_class->get_group_settings($group_data['id']);

            // Check if the settings were retrieved successfully
            if (is_array($settings)) {
                // Display the form with the current settings
                echo "<label for='name'>Group Name:</label><br>";
                echo "<input id='textbox' type='text' name='name' value='" . htmlspecialchars($settings['name']) . "'><br><br>";

                // Group type selection
                echo "<label for='group_type'>Group Type:</label><br>";
                echo "<select id='textbox' name='group_type' style='height:30px;width:100%;'>
                        <option value='".htmlspecialchars($settings['type'])."'>" . htmlspecialchars($settings['type']) . "</option>
                        <option value='Public'>Public</option>
                        <option value='Private'>Private</option>
                      </select><br><br>";

                // Group description
                echo "<label for='description'>Description:</label><br>";
                echo "<textarea id='textbox' style='height:200px;width:100%;' name='description'>" . htmlspecialchars($settings['description']) . "</textarea><br><br>";

                echo "<input id='post_button' type='submit' value='Save'><br><br>";
            } else {
                echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Failed to load group settings!</div>";
            }
            ?>

            <!-- Add a delete button -->
            <input id="delete_button" type="button" value="Delete Group" onclick="confirmDelete()">
        </form>

        <?php
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['name']) && isset($_POST['group_type']) && isset($_POST['description'])) {
                $group_id = $group_data['id'];
                $data = array();
                $data['name'] = $_POST['name'];
                $data['type'] = $_POST['group_type'];
                $data['description'] = $_POST['description'];

                // Save the updated settings
                $settings_class->save_group_settings($group_id, $data);

                // Check if the update was successful
                echo "<div style='text-align:center;font-size:14px;color:green;'>Settings updated successfully!</div>";

                // Reload the page to reflect changes
                header("Location: group.php?id=" . $group_id);
                exit(); // Use exit() instead of die() after header()
            } else {
                echo "<div style='text-align:center;font-size:14px;color:red;'>Error: Please fill in all fields!</div>";
            }
        }
        error_reporting(0);
        ?>
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm("Are you sure you want to delete this group? This action cannot be undone.")) {
            // Redirect to a PHP script to handle the deletion
            window.location.href = "delete_group.php?id=<?php echo htmlspecialchars($group_data['id']); ?>";
        }
    }
</script>
