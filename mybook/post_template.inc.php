<?php
// Check if the post data is available
if (!isset($ROW) || !isset($ROW_USER)) {
    echo "Post data not available.";
    return;
}

// Check if 'id' exists in the $ROW array
if (!isset($ROW['id'])) {
    echo "Post ID not found.";
    return;
}

// Extract post details
$post_id = intval($ROW['id']);
$user_first_name = isset($ROW_USER['first_name']) ? htmlspecialchars($ROW_USER['first_name']) : 'Unknown User';
$user_last_name = isset($ROW_USER['last_name']) ? htmlspecialchars($ROW_USER['last_name']) : 'Unknown User';
$post_content = isset($ROW['content']) ? htmlspecialchars($ROW['content']) : '';
$post_image = isset($ROW['image']) ? htmlspecialchars($ROW['image']) : '';
$post_likes = isset($ROW['likes']) ? intval($ROW['likes']) : 0;
$post_comments = isset($ROW['comments']) ? intval($ROW['comments']) : 0;

// Resize the post image using the Image class
$image_class = new Image();
$resized_image = $post_image ? $image_class->get_thumb_post($post_image) : '';

// Format the post date
$post_date = isset($ROW['created_at']) ? date('F j, Y, g:i a', strtotime($ROW['created_at'])) : 'Unknown date';

// Determine user profile image
$profile_image = "images/user_male.jpg"; // Default male image
if ($ROW_USER['gender'] == "Female") {
    $profile_image = "images/user_female.jpg"; // Default female image
}
if (file_exists($ROW_USER['profile_image'])) {
    $profile_image = $image_class->get_thumb_profile($ROW_USER['profile_image']);
}
?>

<div class="post" style="border: 1px solid #ddd; margin-bottom: 10px; padding: 10px; background-color: #fff;">
    <div class="post-header" style="margin-bottom: 5px; display: flex; align-items: center;">
        <img src="<?php echo $profile_image ?>" style="width: 75px; margin-right: 10px; border-radius: 50%;">
        <div>
            <strong>
                <a href='profile.php?id=<?php echo $ROW['user_id']; ?>' style="text-decoration: none; color: #405d9b;">
                    <?php echo $user_first_name . " " . $user_last_name; ?>
                </a>
            </strong><br>
            <small><?php echo $post_date; ?></small>
        </div>
    </div>

    <div class="post-content" style="margin-bottom: 10px;">
        <p><?php echo check_tags($post_content); ?></p>
        <?php if ($resized_image): ?>
            <img src="<?php echo $resized_image; ?>" alt="Post Image" style="max-width: 100%; height: auto; margin-bottom: 10px;">
        <?php endif; ?>
    </div>

    <div class="post-actions" style="font-size: 14px; color: #999;">
        <?php $likes_display = ($post_likes > 0) ? "($post_likes)" : ""; ?>
        <a href="like_group_post.php?type=post&id=<?php echo $ROW['id']; ?>">Like<?php echo $likes_display; ?></a>
        <?php $comments_display = ($post_comments > 0) ? "($post_comments)" : ""; ?>
        . <a href="single_post_group.php?id=<?php echo $ROW['id']; ?>">Comment<?php echo $comments_display; ?></a>

        <?php if ($ROW['user_id'] == $_SESSION['mybook_userid']): ?>
            <button onclick="openEditModal(<?php echo $post_id; ?>, '<?php echo addslashes($post_content); ?>')">Edit</button>
            <button onclick="confirmDelete(<?php echo $post_id; ?>)">Delete</button>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Post Modal -->
<div id="editModal" style="display:none;">
    <form id="editForm" method="post" action="edit_group_post.php">
        <textarea name="content" id="editContent" required></textarea>
        <input type="hidden" name="post_id" id="editPostId">
        <input type="submit" value="Update Post">
        <button type="button" onclick="closeEditModal()">Cancel</button>
    </form>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmation" style="display:none;">
    <p>Are you sure you want to delete this post?</p>
    <button id="confirmDeleteButton">Yes</button>
    <button onclick="closeDeleteConfirmation()">No</button>
</div>

<script>
    function openEditModal(postId, content) {
        document.getElementById('editPostId').value = postId;
        document.getElementById('editContent').value = content;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function confirmDelete(postId) {
        document.getElementById('deleteConfirmation').style.display = 'block';
        document.getElementById('confirmDeleteButton').onclick = function() {
            deletePost(postId);
        };
    }

    function closeDeleteConfirmation() {
        document.getElementById('deleteConfirmation').style.display = 'none';
    }

    function deletePost(postId) {
        var form = document.createElement('form');
        form.method = 'post';
        form.action = 'delete_group_post.php';

        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'post_id';
        input.value = postId;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
</script>
