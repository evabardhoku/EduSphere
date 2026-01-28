<div style="width: 800px;margin:auto;font-size: 20px;">
    <div style="padding: 20px;">
        <?php
        // Ensure the Database class is included and the connection is established
        include_once 'classes/connect.php'; // Adjust the path as needed

        // Create a new Database instance
        $DB = new Database();

        // Check if the session has user ID
        if (isset($_SESSION['mybook_userid'])) {
            $user_data['user_id'] = $_SESSION['mybook_userid'];  // Populate user_id from session
        } else {
            echo "User is not logged in.";
            exit();
        }

        // Ensure group_id is provided and valid
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo "Group ID is not provided.";
            exit();
        }

        // Ensure the Database connection is established
        if (!$DB->conn) {
            echo "Database connection failed.";
            exit();
        }

        // Sanitize inputs
        $group_id = mysqli_real_escape_string($DB->conn, $_GET['id']);
        $user_id = mysqli_real_escape_string($DB->conn, $user_data['user_id']);

        // Modify SQL query to filter by both user_id and group_id
        $sql = "SELECT image, id AS postid 
                FROM group_posts 
                WHERE image IS NOT NULL 
                  AND user_id = '$user_id' 
                  AND group_id = '$group_id' 
                ORDER BY id DESC 
                LIMIT 30";
        $images = $DB->read($sql);

        // Create an instance of Image class
        $image_class = new Image();

        // Display images if available
        if (is_array($images) && !empty($images)) {
            foreach ($images as $image_row) {
                if (!empty($image_row['image'])) {
                    echo "<a href='single_post_group.php?id=" . htmlspecialchars($image_row['postid']) . "'>";
                    echo "<img src='" . htmlspecialchars($image_class->get_thumb_post($image_row['image'])) . "' style='width:150px;margin:10px;' />";
                    echo "</a>";
                }
            }
        } else {
            echo "No images were found!";
        }
        ?>
    </div>
</div>
