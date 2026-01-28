<?php

	include("classes/autoload.php");

	$login = new Login();
//	$_SESSION['mybook_userid'] = isset($_SESSION['mybook_userid']) ? $_SESSION['mybook_userid'] : 0;
if (!isset($_SESSION['mybook_userid'])) {
    // Redirect to login page or handle as not logged in
    header("Location: login.php");
    die();
}

	$user_data = $login->check_login($_SESSION['mybook_userid'],false);

 	$USER = $user_data;

 	if(isset($_GET['id']) && is_numeric($_GET['id'])){

	 	$profile = new Profile();
	 	$profile_data = $profile->get_profile($_GET['id']);

	 	if(is_array($profile_data)){
	 		$user_data = $profile_data[0];
	 	}

 	}



//posting starts here
if($_SERVER['REQUEST_METHOD'] == "POST")
{

    $post = new Post();
    $id = $_SESSION['mybook_userid'];
    $result = $post->create_post($id, $_POST,$_FILES);

    if($result == "")
    {
        header("Location: profile.php");
        die;
    }else
    {

        echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>";
        echo "<br>The following errors occured:<br><br>";
        echo $result;
        echo "</div>";
    }
}

	//collect posts
	$post = new Post();
	$id = $user_data['userid'];

	$posts = $post->get_posts($id);

	//collect friends
	$user = new User();

	$friends = $user->get_following($user_data['userid'],"user");

	$image_class = new Image();

	//check if this is from a notification
	if(isset($_GET['notif'])){
		notification_seen($_GET['notif']);
	}

?>

<!DOCTYPE html>
	<html>
	<head>
		<title>Profile | Mybook</title>
	</head>

	<style type="text/css">

		#blue_bar{

			height: 50px;
            background: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);
            color: #d9dfeb;

		}

		#search_box{

			width: 400px;
			height: 20px;
			border-radius: 5px;
			border:none;
			padding: 4px;
			font-size: 14px;
			background-image: url(search.png);
			background-repeat: no-repeat;
			background-position: right;

		}

		#textbox{

			width: 100%;
			height: 20px;
			border-radius: 5px;
			border:none;
			padding: 4px;
			font-size: 14px;
			border: solid thin grey;
			margin:10px;

		}

		#profile_pic{

			width: 150px;
			margin-top: -300px;
			border-radius: 50%;
			border:solid 2px white;
		}

		#menu_buttons{

			width: 100px;
			display: inline-block;
			margin:2px;
		}

		#friends_img{

			width: 75px;
			float: left;
			margin:8px;

		}

		#friends_bar{

			background-color: white;
			min-height: 400px;
			margin-top: 20px;
			color: #aaa;
			padding: 8px;
		}

		#friends{

		 	clear: both;
		 	font-size: 12px;
		 	font-weight: bold;
		 	color: #405d9b;
		}

		textarea{

			width: 100%;
			border:none;
			font-family: tahoma;
			font-size: 14px;
			height: 60px;

		}

		#post_button{

			float: right;
            background: linear-gradient(375deg,  #00ccd9, #41d3ca);
            border:none;
			color: white;
			padding: 4px;
			font-size: 14px;
			border-radius: 2px;
			width: 50px;
			min-width: 50px;
			cursor: pointer;
		}

 		#post_bar{

 			margin-top: 20px;
 			background-color: white;
 			padding: 10px;
 		}

 		#post{

 			padding: 4px;
 			font-size: 13px;
 			display: flex;
 			margin-bottom: 20px;
 		}

	</style>

	<body style="font-family: tahoma; background-color: #d0d8e4;">

		<br>
		<?php include("header.php"); ?>

 		<!--change profile image area-->
 		<div id="change_profile_image" style="display:none;position:absolute;width: 100%;height: 100%;background-color: #000000aa;">
 			<div style="max-width:600px;margin:auto;min-height: 400px;flex:2.5;padding: 20px;padding-right: 0px;">

                <form method="post" action="update_profile_image.php?id=<?php echo htmlspecialchars($user_data['userid']); ?>" enctype="multipart/form-data">
                    <div style="border:solid thin #aaa; padding: 10px;background-color: white;">
                        <input type="file" name="file"><br>
                        <input id="post_button" type="submit" style="width:120px;" value="Change">
                        <br>
                        <div style="text-align: center;">
                            <br><br>
                            <?php
                            if ($user_data && isset($user_data['profile_image'])) {
                                echo "<img src='{$user_data['profile_image']}' style='max-width:500px;'>";
                            } else {
                                echo "Profile image not found.";
                            }
                            ?>
                        </div>
                    </div>
                </form>


            </div>
 		</div>

		<!--change cover image area-->
 		<div id="change_cover_image" style="display:none;position:absolute;width: 100%;height: 100%;background-color: #000000aa;">
 			<div style="max-width:600px;margin:auto;min-height: 400px;flex:2.5;padding: 20px;padding-right: 0px;">

                <form method="post" action="update_cover_image.php?id=<?php echo htmlspecialchars($user_data['userid']); ?>" enctype="multipart/form-data">
                    <div style="border:solid thin #aaa; padding: 10px;background-color: white;">
                        <input type="file" name="file"><br>
                        <input id="post_button" type="submit" style="width:120px;" value="Change">
                        <br>
                        <div style="text-align: center;">
                            <br><br>
                            <?php
                            if ($user_data && isset($user_data['cover_image'])) {
                                echo "<img src='{$user_data['cover_image']}' style='max-width:500px;'>";
                            } else {
                                echo "Cover image not found.";
                            }
                            ?>
                        </div>
                    </div>
                </form>


            </div>
 		</div>

		<!--cover area-->
		<div style="width: 800px;margin:auto;min-height: 400px;">

			<div style="background-color: white;text-align: center;color: #405d9b">

					<?php

						$image = "images/cover_image.jpg";
						if(file_exists($user_data['cover_image']))
						{
							$image = $image_class->get_thumb_cover($user_data['cover_image']);
						}
					?>

				<img src="<?php echo $image ?>" style="width:100%;">


				<span style="font-size: 12px;">
					<?php

						$image = "images/user_male.jpg";
						if($user_data['gender'] == "Female")
						{
							$image = "images/user_female.jpg";
						}
						if(file_exists($user_data['profile_image']))
						{
							$image = $image_class->get_thumb_profile($user_data['profile_image']);
						}
					?>

					<img id="profile_pic" src="<?php echo $image ?>"><br/>

					<?php if(i_own_content($user_data)):?>

						<a onclick="show_change_profile_image(event)" style="text-decoration: none;color:#f0f;" href="update_profile_image.php?change=profile">Change Profile Image</a> |
						<a onclick="show_change_cover_image(event)" style="text-decoration: none;color:#f0f;" href="update_profile_image.php?change=cover">Change Cover</a>

					<?php endif; ?>

				</span>
				<br>
					<div style="font-size: 20px;color: black;">
                        <?php
                        // Ensure the $online_status is set before using it
                        $online_status = is_user_online($user_data['userid']) ? "Online" : "Not Online";
                        ?>

                        <?php
                        $logged_in_user_id = $_SESSION['mybook_userid']; // Get the logged-in user's ID
                        ?>

                        <a href="profile.php?id=<?php echo $user_data['userid'] ?>" style="text-decoration: none; color: inherit;">
                            <?php echo $user_data['first_name'] . " " . $user_data['last_name'] ?>
                            <br><span style="font-size:12px;">@<?php echo $user_data['tag_name']; ?></span>
                            <br>
                            <?php
                            if ($user_data['userid'] !== $logged_in_user_id) {
                                // Show online status only if it's not the logged-in user's profile
                                echo "<span style='color: " . ($online_status == "Online" ? "green" : "red") . "; font-weight: bold; font-size: 15px;'>" . $online_status . "</span>";
                            }
                            ?>
                        </a>


                        <?php
							$mylikes = "";
							if($user_data['likes'] > 0){

								$mylikes = "(" . $user_data['likes'] . " Followers)";
							}
						?>
						<br>
						<a href="like.php?type=user&id=<?php echo $user_data['userid'] ?>">
							<input id="post_button" type="button" value="Follow <?php echo $mylikes ?>" style="margin-right:10px;background-color: #9b409a;width:auto;">
						</a>

					</div>
				<br>


				<a href="index.php"><div id="menu_buttons">Timeline</div></a>
				<a href="profile.php?section=about&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">About</div></a>
				<a href="profile.php?section=followers&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">Followers</div></a>
				<a href="profile.php?section=following&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">Following</div></a>
				<a href="profile.php?section=photos&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">Photos</div></a>
				<a href="profile.php?section=groups&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">Groups</div></a>

				<?php
					if($user_data['userid'] == $_SESSION['mybook_userid']){
						echo '<a href="profile.php?section=settings&id='.$user_data['userid'].'"><div id="menu_buttons">Settings</div></a>';
					}
				?>
			</div>

			<!--below cover area-->

	 		<?php

	 			$section = "default";
	 			if(isset($_GET['section'])){

	 				$section = $_GET['section'];
	 			}

	 			if($section == "default"){

	 				include("profile_content_default.php");

                }elseif($section == "following"){

	 				include("profile_content_following.php");

	 			}elseif($section == "followers"){

	 				include("profile_content_followers.php");

	 			}elseif($section == "about"){

	 				include("profile_content_about.php");

	 			}elseif($section == "settings"){

	 				include("profile_content_settings.php");

	 			}elseif($section == "photos"){

	 				include("profile_content_photos.php");

	 			}elseif($section == "groups"){

                    include("profile_content_group.php");
                }



	 		?>

		</div>

	</body>
</html>

<script type="text/javascript">

	function show_change_profile_image(event){

		event.preventDefault();
		var profile_image = document.getElementById("change_profile_image");
		profile_image.style.display = "block";
	}


	function hide_change_profile_image(){

		var profile_image = document.getElementById("change_profile_image");
		profile_image.style.display = "none";
	}


	function show_change_cover_image(event){

		event.preventDefault();
		var cover_image = document.getElementById("change_cover_image");
		cover_image.style.display = "block";
	}


	function hide_change_cover_image(){

		var cover_image = document.getElementById("change_cover_image");
		cover_image.style.display = "none";
	}


	window.onkeydown = function(key){

		if(key.keyCode == 27){

			//esc key was pressed
			hide_change_profile_image();
			hide_change_cover_image();
		}
	}


</script>