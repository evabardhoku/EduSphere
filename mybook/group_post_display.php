<?php
// Ensure that you include necessary files for database access and user information
include_once "classes/functions.php";
include_once "classes/user.php";

// Check if the required variables are set
if (!isset($ROW) || !isset($ROW_USER)) {
    die("Post data not available.");
}

// Output the post information
?>
<div style="border: solid thin #ddd; padding: 10px; margin-bottom: 10px; background-color: white;">
    <div style="display: flex; align-items: center;">
        <?php if (isset($ROW_USER['profile_image']) && !empty($ROW_USER['profile_image'])): ?>
            <img src="<?= htmlspecialchars($ROW_USER['profile_image']) ?>" alt="Profile Image" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
        <?php else: ?>
            <img src="images/cover_image.jpg" alt="Default Profile Image" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
        <?php endif; ?>
        <strong><?= htmlspecialchars($ROW_USER['first_name']) ?></strong>
        <span style="font-size: 0.9em; color: #666; margin-left: 10px;">
            <?= date("F j, Y, g:i a", strtotime($ROW['created_at'])) ?>
        </span>
    </div>
    <p><?= nl2br(htmlspecialchars($ROW['content'])) ?></p>
    <?php if (isset($ROW['image']) && !empty($ROW['image'])): ?>
        <img src="<?= htmlspecialchars($ROW['image']) ?>" alt="Post Image" style="max-width: 100%; height: auto; margin-top: 10px;">
    <?php endif; ?>
    <?php if ($ROW['is_group_cover_image']): ?>
        <p><em>This post includes a cover image for the group.</em></p>
    <?php endif; ?>
</div>
