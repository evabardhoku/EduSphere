<?php
require 'classes/connect.php';

$database = new Database();
$conn = $database->getConnection();

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']); // Ensure to get the post ID from the request
    $content = $_POST['content']; // Get the updated content

    $query = "UPDATE group_posts SET content = ? WHERE id = ?";
    $params = [$content, $post_id];

    $affected_rows = $database->write($query, $params);
    if ($affected_rows > 0) {
        echo "Post updated successfully.";
    } else {
        echo "Failed to update post.";
    }
}

