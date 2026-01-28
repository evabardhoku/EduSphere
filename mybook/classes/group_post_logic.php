<?php
include_once "connect.php"; // Include your database class
include_once "user.php"; // Include the user class

class PostHandler {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getPosts($groupId) {
        // Updated query to fetch posts in descending order of creation date
        $query = "SELECT * FROM group_posts WHERE group_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $groupId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array
    }

    public function getUser($userId) {
        $user = new User();
        return $user->get_user($userId);
    }
}
?>
