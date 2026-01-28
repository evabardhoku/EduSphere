<?php

include("classes/connect.php");
include("classes/signup.php");

$first_name = "";
$last_name = "";
$gender = "";
$email = "";

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $signup = new Signup();
    $result = $signup->evaluate($_POST);

    if($result != "")
    {
        echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>";
        echo "<br>The following errors occurred:<br><br>";
        echo $result;
        echo "</div>";
    } else {
        // Set default profile image if not provided
        $profile_image = isset($_POST['profile_image']) ? $_POST['profile_image'] : 'images/cover_image.jpg';

        // Assuming your `create_user` method handles inserting data into the database
        $_POST['profile_image'] = $profile_image;

        // Call create_user or save method to store the user's data
        $signup->create_user($_POST);

        // Redirect to login page after successful signup
        header("Location: login.php");
        die;
    }

    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333; /* Set a default text color */
        }

        #bar {
            height: 100px;
            background: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);
            color: #3b5998; /* Dark blue for text */
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #bar .title {
            font-size: 40px;
            margin: 0;
        }

        #signup_button {
            background-color: #eb56ab;
            color: white;
            width: 70px;
            text-align: center;
            padding: 10px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        #bar2 {
            background-color: #ffffff; /* White background for the form */
            width: 400px;
            margin: auto;
            margin-top: 50px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        #text {
            height: 40px;
            width: calc(100% - 20px); /* Full width minus padding */
            border-radius: 4px;
            border: solid 1px #ccc;
            padding: 0 10px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        #button {
            width: 100%;
            height: 40px;
            border-radius: 4px;
            font-weight: bold;
            border: none;
            background: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca); /* Gradient button background */
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        #button:hover {
            background: linear-gradient(375deg, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca, #eb56ab); /* Slight hover effect */
        }

        .signup-header {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333; /* Darker color for better readability */
        }

        select {
            height: 40px;
            width: calc(100% - 20px); /* Full width minus padding */
            border-radius: 4px;
            border: solid 1px #ccc;
            padding: 0 10px;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div id="bar">
    <div class="title">Social Media</div>
    <a href="login.php" id="signup_button">Log in</a>
</div>

<div id="bar2">
    <div class="signup-header">Sign up to Social Media</div>
    <form method="post" action="">
        <input value="<?php echo htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8'); ?>" name="first_name" type="text" id="text" placeholder="First name" required><br>
        <input value="<?php echo htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8'); ?>" name="last_name" type="text" id="text" placeholder="Last name" required><br>

        <span style="font-weight: normal;">Gender:</span><br>
        <select id="text" name="gender">
            <option value="">Select gender</option>
            <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
        </select>
        <br><br>
        <input value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" name="email" type="text" id="text" placeholder="Email" required><br>
        <input name="password" type="password" id="text" placeholder="Password" required><br>
        <input name="password2" type="password" id="text" placeholder="Retype Password" required><br>
        <input type="submit" id="button" value="Sign up">
    </form>
</div>
</body>
</html>
