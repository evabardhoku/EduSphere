<?php
// Autoload classes, assuming Database class is autoloaded properly
global $id;
include_once "classes/autoload.php";
include_once "classes/group_post.php"; // GroupPost class

// Check if the user is logged in
if (!isset($_SESSION['mybook_userid'])) {
    header("Location: login.php");
    die();
}

$user_id = intval($_SESSION['mybook_userid']); // Sanitize session user_id

$login = new Login();
$user_data = $login->check_login($user_id, false);
$USER = $user_data; // Assign user data

// Check if the post ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid post ID.";
    exit();
}

// Initialize database connection
$database = new Database(); // Assuming Database is properly autoloaded
$db = $database->getConnection();

// Validate that post ID and type are provided in the URL
if (!isset($_GET['id']) || !isset($_GET['type'])) {
    die("Invalid request.");
}

// Sanitize inputs
$group_id = intval($_GET['id']); // This should be group_id if you are getting post by group
$type = $_GET['type']; // Assuming type is validated elsewhere

// Initialize GroupPost class
$group_class = new GroupPost($db);

// Check if the post exists
$posts = $group_class->get_posts_by_group($group_id);
if (empty($posts)) {
    echo "Post not found."; // Debugging: Show the group_id and query results
    echo "<pre>";
    print_r($posts); // Debugging: Show the posts array
    echo "</pre>";
    exit();
}

// Assuming you want to like the first post in the array
$post_id = $posts[0]['id']; // Get the ID of the first post

// Check if the user has already liked the post
$like_query = "SELECT * FROM group_post_likes WHERE user_id = ? AND post_id = ?";
$stmt = $db->prepare($like_query);
if ($stmt === false) {
    die("Error preparing statement: " . $db->error);
}
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User has already liked the post
    header("Location: single_post_group.php?id=" . urlencode($user_id) . "&error=already_liked");
    exit();
}

// Insert the like into the group_post_likes table
$insert_like_query = "INSERT INTO group_post_likes (user_id, post_id, created_at) VALUES (?, ?, NOW())";
$stmt = $db->prepare($insert_like_query);
if ($stmt === false) {
    die("Error preparing statement: " . $db->error);
}
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();

// Optionally, update the likes count in group_posts table
$update_likes_query = "UPDATE group_posts SET likes = (SELECT COUNT(*) FROM group_post_likes WHERE post_id = ?) WHERE id = ?";
$stmt = $db->prepare($update_likes_query);
$stmt->bind_param("ii", $post_id, $post_id);
$stmt->execute();

// Redirect back to the post page or group page
header("Location: single_post_group.php?id=" . urlencode($post_id));
exit();
?>
