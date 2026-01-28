
<?php
// Assuming you have a function to get user information based on their ID
function get_user_about($user_id) {
    // Create an instance of the User class (make sure this class has a method to get user info)
    $user_class = new User();
    // Fetch user data based on user ID
    $user_data = $user_class->get_user($user_id);

    // Return the 'about' section if available, or an empty string if not
    return !empty($user_data['about']) ? htmlspecialchars($user_data['about']) : "No information available.";
}

// Get the user ID from the URL parameter
$user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['mybook_userid']; // Fallback to the current user's ID if no ID is provided

// Fetch the 'about' section for the user
$about = get_user_about($user_id);
?>

<div style="min-height: 400px; width: 100%; background-color: white; text-align: center;">
    <div style="padding: 20px; max-width: 350px; display: inline-block;">
        <form method="post" enctype="multipart/form-data">
            <br>About me:<br>
            <div id='textbox' style='height:200px; border:none;'><?php echo $about; ?></div>
        </form>
    </div>
</div>
