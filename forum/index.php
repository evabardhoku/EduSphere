<?php

require('config.inc.php');
require('functions.php');

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$page = (int)$page;

if ($page < 1)
    $page = 1;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - PHP Forum</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>

<style>

    @keyframes appear {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }

    .hide {
        display: none;
    }
    .search-box {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
    }

    .search-box input[type="text"] {
        width: 400px; /* Set the width of the search bar */
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px; /* Rounded corners on the left */
        font-size: 16px;
        border-right: none; /* Remove right border to merge with button */
    }

    .search-box button {
        width: 120px; /* Set a fixed width for the button */
        padding: 10px;
        border: none;
        background-image: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca); /* Apply gradient background */
        color: white;
        font-size: 16px;
        border-radius: 6px;
        margin-left: 3px;
        cursor: pointer;
    }

    .search-box button:hover {
        background-color: #2980b9;
    }

</style>
<header>

</header>
<section class="class_1">
    <?php include('header.inc.php') ?>
    <div class="class_11">
        <?php include('success.alert.inc.php') ?>
        <?php include('fail.alert.inc.php') ?>
                    <div class="search-box">
                <form action="search.php" method="GET">
                    <input type="text" name="query" placeholder="Search for posts..." required>
                    <button type="submit">Search</button>
                </form>
            </div>


        <h1 class="class_41">
            Posts
        </h1>

        <?php if (logged_in()): ?>
            <form onsubmit="mypost.submit(event)" method="post" class="class_42">
                <div class="class_43">
                    <textarea placeholder="Whats on your mind?" name="post" class="js-post-input class_44"></textarea>
                </div>
                <div class="class_45">
                    <button class="class_46">
                        Post
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="class_13">
                <i class="bi bi-info-circle-fill class_14">
                </i>
                <div onclick="login.show()" class="class_15" style="cursor:pointer;text-align: center;">
                    You're not logged in <br>Click here to login and post
                </div>
            </div>
        <?php endif; ?>

        <section class="js-posts">
            <div style="padding:10px;text-align:center;">Loading posts....</div>
        </section>

        <div class="class_37" style="display: flex;justify-content: space-between;">
            <button onclick="mypost.prev_page()" class="class_54">
                Prev page
            </button>
            <div class="js-page-number">Page 1</div>
            <button onclick="mypost.next_page()" class="class_39">
                Next page
            </button>

        </div>

    </div>
    <br><br>
    <?php include('signup.inc.php') ?>
    <?php include('login.inc.php') ?>
    <?php include('post.edit.inc.php') ?>

</section>

<!--post card template-->
<div class="js-post-card hide class_42" style="animation: appear 3s ease;">
    <a href="users_profile.php?id=<?= isset($row['user']['id']) ? $row['user']['id'] : 0 ?>"" class="js-profile-link class_45">
        <img src="assets/images/user.jpg" class="js-image class_47">
        <h2 class="js-username class_48" style="font-size:16px">
            Jane Name
        </h2>
    </a>
    <div class="class_49">
        <h4 class="js-date class_41">
            3rd Jan 23 14:35 pm
        </h4>
        <div class="js-post class_15">
            is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard
            dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a
            type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting,
            remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets c
        </div>
        <div class="class_51">
            <i class="bi bi-chat-left-dots class_52">
            </i>
            <div class="js-comment-link class_53" style="color:blue;cursor: pointer;">
                Comments
            </div>
        </div>
        <div class="js-action-buttons class_51">
            <div class="js-edit-button class_53" style="color:blue;cursor: pointer;">
                Edit
            </div>
            <div onclick="mypost.delete()" class="js-delete-button class_53" style="color:orangered;cursor: pointer;">
                Delete
            </div>
        </div>
    </div>
</div>
<!--end post card template-->


</body>

<script>
    var home_page = true;
    var page_number = <?=$page?>;
</script>
<script src="./assets/js/mypost.js?v1"></script>
</html>
