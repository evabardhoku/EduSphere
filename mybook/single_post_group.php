<?php
include_once "classes/autoload.php";

$user_id = $_SESSION['mybook_userid'];

$login = new Login();
if (!isset($_SESSION['mybook_userid'])) {
    header("Location: login.php");
    die();
}

$user_data = $login->check_login($_SESSION['mybook_userid'], false);
$USER = $user_data;

// Check if the post ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid post ID.";
    exit;
}

$post_id = intval($_GET['id']);
$db = new Database();
$image_class = new Image();

// Fetch the post details
$sql = "SELECT p.*, u.first_name, u.last_name, u.gender, u.profile_image 
        FROM group_posts p 
        JOIN users u ON p.user_id = u.userid 
        WHERE p.id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post_result = $stmt->get_result();
$post = $post_result->fetch_assoc();

if (!$post) {
    echo "Post not found.";
    exit;
}

// Fetch comments for the post (order from newest to oldest)
$sql_comments = "SELECT c.*, u.first_name, u.last_name, u.profile_image 
                 FROM group_post_comments c 
                 JOIN users u ON c.user_id = u.userid 
                 WHERE c.post_id = ? AND u.owner IS NULL 
                 ORDER BY c.created_at DESC"; // Changed to DESC for newest first
$stmt_comments = $db->prepare($sql_comments);
$stmt_comments->bind_param("i", $post_id);
$stmt_comments->execute();
$comments_result = $stmt_comments->get_result();

// Determine user profile image for the post
$profile_image = "images/user_male.jpg"; // Default male image
if (isset($post['gender']) && $post['gender'] == "Female") {
    $profile_image = "images/user_female.jpg"; // Default female image
}
if (!empty($post['profile_image']) && file_exists($post['profile_image'])) {
    $profile_image = $image_class->get_thumb_profile($post['profile_image']);
}

// Handle comment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment_content = trim($_POST['comment']);
    if (!empty($comment_content)) {
        $user_id = $_SESSION['mybook_userid']; // Ensure the user is logged in and session is set
        $sql_insert_comment = "INSERT INTO group_post_comments (user_id, post_id, content, created_at) 
                                VALUES (?, ?, ?, NOW())";
        $stmt_insert_comment = $db->prepare($sql_insert_comment);
        $stmt_insert_comment->bind_param("iis", $user_id, $post_id, $comment_content);
        $stmt_insert_comment->execute();

        // Redirect to avoid resubmission of form
        header("Location: single_post_group.php?id=$post_id");
        exit;
    }
}

// Handle comment deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $comment_id = intval($_GET['delete']);
    $sql_delete_comment = "DELETE FROM group_post_comments WHERE id = ? AND user_id = ?";
    $stmt_delete_comment = $db->prepare($sql_delete_comment);
    $stmt_delete_comment->bind_param("ii", $comment_id, $user_id);
    $stmt_delete_comment->execute();
    header("Location: single_post_group.php?id=$post_id");
    exit;
}

// Handle comment editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment_id']) && isset($_POST['edit_comment_content'])) {
    $edit_comment_id = intval($_POST['edit_comment_id']);
    $edit_comment_content = trim($_POST['edit_comment_content']);
    if (!empty($edit_comment_content)) {
        $sql_update_comment = "UPDATE group_post_comments SET content = ? WHERE id = ? AND user_id = ?";
        $stmt_update_comment = $db->prepare($sql_update_comment);
        $stmt_update_comment->bind_param("sii", $edit_comment_content, $edit_comment_id, $user_id);
        $stmt_update_comment->execute();
        header("Location: single_post_group.php?id=$post_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Single Group Post</title>
    <style type="text/css">
        #blue_bar {
            height: 50px;
            background: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);

            color: #d9dfeb;
        }

        textarea {
            width: 100%;
            border: none;
            font-family: tahoma;
            font-size: 14px;
            height: 60px;
        }

        #post_button {
            float: right;
            background: linear-gradient(375deg,  #00ccd9, #41d3ca);

            border: none;
            color: white;
            padding: 4px;
            font-size: 14px;
            border-radius: 2px;
            width: 50px;
        }

        #post_bar {
            margin-top: 20px;
            background-color: white;
            padding: 10px;
        }

        #post {
            padding: 4px;
            font-size: 13px;
            display: flex;
            margin-bottom: 20px;
        }

        .comment {
            border-bottom: solid 1px #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }

        .comment img {
            width: 50px;
            border-radius: 50%;
        }

        .login-warning {
            color: red;
            font-weight: bold;
        }

        /* General styles for popup containers */
        .popup {
            display: none; /* Hidden by default */
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            justify-content: center;
            align-items: center;
            z-index: 1000; /* Ensure it's on top of other content */
        }

        /* Styles for popup content */
        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Styles for buttons inside popups */
        .popup-button {
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        /* Delete Confirmation Popup Buttons */
        .confirm-delete {
            background-color: #e74c3c; /* Red for Delete button */
        }

        .cancel-delete {
            background-color: #e67e22; /* Orange for Cancel button */
        }

        /* Edit Comment Popup Buttons */
        .save-edit {
            background-color: #2ecc71; /* Green for Save Changes button */
        }

        .cancel-edit {
            background-color: #e67e22; /* Orange for Cancel button */
        }

        /* Hover effects for popup buttons */
        .popup-button:hover {
            opacity: 0.8;
            transform: scale(1.05); /* Slightly enlarge button on hover */
        }

        /* Button active state for pressed effect */
        .popup-button:active {
            transform: scale(0.98); /* Slightly shrink button on press */
        }

        /* Styles for textarea in edit popup */
        #editCommentContent {
            width: 100%;
            height: 80px;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 14px;
            font-family: tahoma, sans-serif;
        }

        /* Optional styles for popup text */
        .popup p {
            margin-bottom: 20px;
        }


        /* Styles for comment action buttons */
        .comment-buttons {
            text-align: right;
            margin-top: 10px;
        }

        .comment-buttons button {
            background-color: #2ecc71; /* Green */
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 10px;
        }

        .comment-buttons button.edit {
            background-color: #e67e22; /* Brown */
        }

        .comment-buttons button.delete {
            background-color: #e74c3c; /* Red */
        }

        .comment-buttons button:hover {
            opacity: 0.8;
        }


    </style>
    <script>
        function showDeletePopup(commentId) {
            document.getElementById('deletePopup').style.display = 'flex';
            document.getElementById('confirmDelete').href = '?id=<?php echo $post_id; ?>&delete=' + commentId;
        }

        function showEditPopup(commentId, commentContent) {
            document.getElementById('editPopup').style.display = 'flex';
            document.getElementById('editCommentId').value = commentId;
            document.getElementById('editCommentContent').value = commentContent;
        }

        function closePopups() {
            document.getElementById('deletePopup').style.display = 'none';
            document.getElementById('editPopup').style.display = 'none';
        }
    </script>
</head>
<body style="font-family: tahoma; background-color: #d0d8e4;">
<?php include("header.php"); ?>

<div style="width: 800px; margin: auto; min-height: 400px;">
    <div style="display: flex;">
        <div style="min-height: 400px; flex: 2.5; padding: 20px; padding-right: 0px;">
            <div style="border: solid thin #aaa; padding: 10px; background-color: white;">
                <?php if ($post): ?>
                    <div class="post">
                        <div class="post-header">
                            <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" style="width: 75px; border-radius: 50%;">
                            <strong><?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></strong><br>
                            <small><?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></small>
                        </div>
                        <div class="post-content">
                            <?php if (!empty($post['image'])): ?>
                                <img src="<?php echo htmlspecialchars($image_class->get_thumb_post($post['image'])); ?>" alt="Post Image" style="max-width: 100%; height: auto;">
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($post['content']); ?></p>
                        </div>
                    </div>

                    <!-- Comment form moved above comments -->
                    <?php if (isset($user_id)): ?>
                        <form method="post">
                            <textarea name="comment" placeholder="Add a comment..." required></textarea>
                            <button id="post_button" type="submit">Submit</button>
                        </form>
                    <?php else: ?>
                        <p class="login-warning">Please <a href="login.php">log in</a> to comment.</p>
                    <?php endif; ?>

                <!-- Comments section -->
                <div class="post-comments">
                    <h3>Comments:</h3>
                    <?php while ($comment = $comments_result->fetch_assoc()): ?>
                        <div class="comment">
                            <?php
                            // Default image path
                            $comment_profile_image = "images/user_male.jpg";

                            // Check for a profile image
                            if (!empty($comment['profile_image']) && file_exists($comment['profile_image'])) {
                                $comment_profile_image = $image_class->get_thumb_profile($comment['profile_image']);
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($comment_profile_image); ?>" alt="Profile Image">
                            <strong><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></strong><br>
                            <small><?php echo date('F j, Y, g:i a', strtotime($comment['created_at'])); ?></small>
                            <p><?php echo htmlspecialchars($comment['content']); ?></p>
                            <?php if ($user_id == $comment['user_id']): ?>
                                <div class="comment-buttons">
                                    <button class="edit" onclick="showEditPopup(<?php echo $comment['id']; ?>, '<?php echo addslashes(htmlspecialchars($comment['content'])); ?>')">Edit</button>
                                    <button class="delete" onclick="showDeletePopup(<?php echo $comment['id']; ?>)">Delete</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                    <p><?php echo htmlspecialchars($ERROR ?? 'An error occurred.'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Popup -->
<div id="deletePopup" class="popup">
    <div class="popup-content">
        <p>Are you sure you want to delete this comment?</p>
        <a id="confirmDelete" class="popup-button confirm-delete" href="#">Delete</a>
        <button class="popup-button cancel-delete" onclick="closePopups()">Cancel</button>
    </div>
</div>

<!-- Edit Comment Popup -->
<div id="editPopup" class="popup">
    <div class="popup-content">
        <form method="post">
            <input type="hidden" id="editCommentId" name="edit_comment_id">
            <textarea id="editCommentContent" name="edit_comment_content" required></textarea><br>
            <button type="submit" class="popup-button save-edit">Save Changes</button>
            <button type="button" class="popup-button cancel-edit" onclick="closePopups()">Cancel</button>
        </form>
    </div>
</div>


</body>
</html>
