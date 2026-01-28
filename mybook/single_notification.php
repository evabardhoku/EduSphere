<?php

global $User, $DB, $image_class, $Post;
$actor = $User->get_user($notif_row['userid']);
$owner = $User->get_user($notif_row['content_owner']);
$id = esc($_SESSION['mybook_userid']);

$link = "";

if ($notif_row['content_type'] == "post") {
    $link = "single_post.php?id=" . $notif_row['contentid'] . "&notif=" . $notif_row['id'];
} elseif ($notif_row['content_type'] == "profile") {
    $link = "profile.php?id=" . $notif_row['userid'] . "&notif=" . $notif_row['id'];
} elseif ($notif_row['content_type'] == "comment") {
    $link = "single_post.php?id=" . $notif_row['contentid'] . "&notif=" . $notif_row['id'];
}

// Check if the notification was seen
$query = "SELECT * FROM notification_seen WHERE userid = '$id' AND notification_id = '{$notif_row['id']}' LIMIT 1";
$seen = $DB->read($query);

$color = "#dfcccc";
if ($seen !== false && is_array($seen)) {
    $color = "#eee";
}

?>

<a href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>" style="text-decoration: none;">
    <div id="notification" style="background-color: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>">

        <?php
        if (is_array($actor) && is_array($owner)) {
            $image = "images/user_male.jpg";
            if ($actor['gender'] == "Female") {
                $image = "images/user_female.jpg";
            }

            if (file_exists($actor['profile_image'])) {
                $image = $image_class->get_thumb_profile($actor['profile_image']);
            }

            echo "<img src='" . htmlspecialchars($image, ENT_QUOTES, 'UTF-8') . "' style='width:36px;margin:4px;float:left;' />";

            if ($actor['userid'] != $id) {
                echo htmlspecialchars($actor['first_name'] . " " . $actor['last_name'], ENT_QUOTES, 'UTF-8');
            } else {
                echo "You ";
            }

            if ($notif_row['activity'] == "like") {
                echo " liked ";
            } elseif ($notif_row['activity'] == "follow") {
                echo " followed ";
            } elseif ($notif_row['activity'] == "comment") {
                echo " commented ";
            } elseif ($notif_row['activity'] == "tag") {
                echo " tagged ";
            }

            if ($owner['userid'] != $id && $notif_row['activity'] != "tag") {
                echo htmlspecialchars($owner['first_name'] . " " . $owner['last_name'] . "'s ", ENT_QUOTES, 'UTF-8');
            } elseif ($notif_row['activity'] == "tag") {
                echo " you in a ";
            } elseif ($notif_row['activity'] == "follow") {
                echo " you ";
            }else {
                echo " your ";
            }

            $content_row = $Post->get_one_post($notif_row['contentid']);

            if (is_array($content_row)) {
                if ($notif_row['content_type'] == "post") {
                    if ($content_row['has_image']) {
                        echo "image";

                        if (file_exists($content_row['image'])) {
                            $post_image = $image_class->get_thumb_post($content_row['image']);
                            echo "<img src='" . htmlspecialchars($post_image, ENT_QUOTES, 'UTF-8') . "' style='width:40px;float:right;' />";
                        }
                    } else {
                        echo htmlspecialchars($notif_row['content_type'], ENT_QUOTES, 'UTF-8');
                        echo "<span style='float:right;font-size:11px;color:#888;display:inline-block;margin-right:10px;'>" . htmlspecialchars(substr($content_row['post'], 0, 50), ENT_QUOTES, 'UTF-8') . "</span>";
                    }
                } else {
                    echo htmlspecialchars($notif_row['content_type'], ENT_QUOTES, 'UTF-8');
                    echo "<span style='float:right;font-size:11px;color:#888;display:inline-block;margin-right:10px;'>" . htmlspecialchars(substr($content_row['post'], 0, 50), ENT_QUOTES, 'UTF-8') . "</span>";
                }
            } else {
                // Handle the case where $content_row is not an array, if needed
            }

            $date = date("jS M Y H:i:s a", strtotime($notif_row['date']));
            echo "<br><span style='font-size:11px;color:#888;display:inline-block;margin-right:10px;'>" . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . "</span>";
        }
        ?>
    </div>
</a>
