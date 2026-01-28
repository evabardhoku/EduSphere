<?php
// Include database connection
require 'classes/connect.php';

// Create a new database instance
$database = new Database();
$conn = $database->getConnection();

// Check if post_id is set and is a valid integer
if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);

    // Prepare the delete query
    $query = "DELETE FROM group_posts WHERE id = ?";
    $stmt = $database->prepare($query);

    // Bind the post_id parameter
    mysqli_stmt_bind_param($stmt, 'i', $post_id);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Check if any rows were affected
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Post deleted successfully.";
        } else {
            echo "No post found with that ID.";
        }
    } else {
        echo "Error deleting post: " . mysqli_error($conn);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo "Invalid post ID.";
}

// Close the database connection
mysqli_close($conn);
?>
