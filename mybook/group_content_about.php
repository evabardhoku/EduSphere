<div style="width: 800px;margin:auto;font-size: 30px;">

<div style="padding: 20px; max-width: 350px; display: inline-block;">
        <form method="post" enctype="multipart/form-data">

            <?php
            global $group_data;
            $settings_class = new Settings();

            // Retrieve settings for the group using the group ID
            $settings = $settings_class->get_group_settings($group_data['id']);

            // Check if $settings is an array and contains the 'description' field
            if (is_array($settings) && isset($settings['description'])) {
                echo "<br>About the Group:<br>
                    <div id='textbox' style='height:200px;border:none;' >".htmlspecialchars($settings['description'])."</div>
                ";
            } else {
                // Handle the case where settings are not found or 'description' field is missing
                echo "<br>No description available<br>";
            }
            ?>

        </form>
    </div>
</div>
