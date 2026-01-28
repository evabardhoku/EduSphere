<div style="min-height: 400px;width:100%;background-color: white;text-align: center;">
    <div style="padding: 20px;max-width:350px;display: inline-block;">
        <form method="post" action="update_settings.php" enctype="multipart/form-data">
            <?php
            include_once "classes/autoload.php";
            include_once "classes/settings.php";
            $settings_class = new Settings();
            $settings = $settings_class->get_settings($_SESSION['mybook_userid']);

            if (is_array($settings)) {
                echo "<input type='text' id='textbox' name='first_name' value='".htmlspecialchars($settings['first_name'])."' placeholder='First name' required />";
                echo "<input type='text' id='textbox' name='last_name' value='".htmlspecialchars($settings['last_name'])."' placeholder='Last name' required />";

                echo "<select id='textbox' name='gender' style='height:30px;' required>
                            <option value='".htmlspecialchars($settings['gender'])."'>".htmlspecialchars($settings['gender'])."</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>";

                echo "<input type='email' id='textbox' name='email' value='".htmlspecialchars($settings['email'])."' placeholder='Email' required />";
                echo "<input type='password' id='textbox' name='password' placeholder='Password' />";
                echo "<input type='password' id='textbox' name='password2' placeholder='Confirm Password' />";

                echo "<br>About me:<br>
                            <textarea id='textbox' style='height:200px;' name='about'>".htmlspecialchars($settings['about'])."</textarea>
                        ";

                echo '<input id="post_button" type="submit" value="Save">';
            }
            ?>
        </form>
    </div>
</div>
