<?php

class GroupPost
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(); // Initialize the Database class
    }

    public function create_post($user_id, $group_id, $post_data, $file_data)
    {
        // Validate inputs
        if (!is_numeric($user_id) || !is_numeric($group_id) || empty($post_data['content'])) {
            return "Invalid input.";
        }

        // Extract post content
        $content = trim($post_data['content']);
        $is_cover_image = isset($post_data['is_group_cover_image']) ? (int)$post_data['is_group_cover_image'] : 0;
        $image_path = '';

        // Handle file upload
        if (isset($file_data['file']) && $file_data['file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $file_name = basename($file_data['file']['name']);
            $upload_file = $upload_dir . $file_name;

            // Validate file type (e.g., only allow image files)
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file_data['file']['type'], $allowed_types)) {
                return "Invalid file type.";
            }

            // Validate file size (e.g., maximum 5MB)
            if ($file_data['file']['size'] > 5 * 1024 * 1024) {
                return "File size exceeds the maximum limit of 5MB.";
            }

            // Move uploaded file
            if (move_uploaded_file($file_data['file']['tmp_name'], $upload_file)) {
                $image_path = $upload_file;
            } else {
                return "Failed to upload file.";
            }
        }

        // Prepare and execute query
        $query = "INSERT INTO group_posts (user_id, group_id, content, image, is_group_cover_image, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            return "Failed to prepare query.";
        }

        // Bind parameters
        $stmt->bind_param("iissi", $user_id, $group_id, $content, $image_path, $is_cover_image);

        // Execute and check result
        if ($stmt->execute()) {
            return ""; // Success
        } else {
            return "Error executing query: " . $this->db->error;
        }
    }

    public function get_posts_by_group($group_id)
    {
        if (!is_numeric($group_id)) {
            return [];
        }

        $query = "SELECT * FROM group_posts WHERE group_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            return [];
        }

        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        return $posts;
    }
}
?>
