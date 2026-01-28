<?php

session_start();

include("classes/connect.php");
include("classes/login.php");

$email = "";
$password = "";

if($_SERVER['REQUEST_METHOD'] == 'POST')
{


    $login = new Login();
    $result = $login->evaluate($_POST);

    if($result != "")
    {

        echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>";
        echo "<br>The following errors occured:<br><br>";
        echo $result;
        echo "</div>";
    }else
    {   if (isset($_SESSION['mybook_userid'])) {
        $user_id = $_SESSION['mybook_userid'];
        $login->set_online_status($user_id, 1);}

        header("Location: profile.php");
        die;
    }


    $email = $_POST['email'];
    $password = $_POST['password'];


}




?>

<html>

<!DOCTYPE html>
<html>
<head>
    <title> Log in</title>
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
            background-color: #eb56ab; /* Green button */
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

        .login-header {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333; /* Darker color for better readability */
        }
    </style>
</head>
<body>
<div id="bar">
    <div class="title">Social Media</div>
    <a href="signup.php" id="signup_button">Signup</a>
</div>

<div id="bar2">
    <div class="login-header">Log in to Social Media</div>
    <form method="post">
        <input name="email" value="<?php echo htmlspecialchars($email); ?>" type="text" id="text" placeholder="Email" required><br>
        <input name="password" value="<?php echo htmlspecialchars($password); ?>" type="password" id="text" placeholder="Password" required><br>
        <input type="submit" id="button" value="Log in">
    </form>
</div>
</body>
</html>
