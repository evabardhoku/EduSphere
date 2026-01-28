<?php
//// Start the session
//
//if (session_status() === PHP_SESSION_NONE) {
//    session_start();
//}
//require_once 'classes/connect.php';
//require_once 'classes/image.php';
//require_once 'classes/post.php';
//
//// Debugging: Check if session variables are set
//echo "Session ID: " . session_id() . "<br>";
//echo "User ID in Session: " . (isset($_SESSION['userid']) ? $_SESSION['userid'] : "Not set") . "<br>";
//
//if (!isset($_SESSION['userid'])) {
//    echo "User is not logged in.";
//    exit;
//}
//
//$userid = $_SESSION['userid'];
//
//// Initialize user data
//$DB = new Database();
//$query = "SELECT * FROM users WHERE userid = '$userid' LIMIT 1";
//$result = $DB->read($query);
//
//if ($result) {
//    $user_data = $result[0];
//} else {
//    echo "User data not found.";
//    exit;
//}
//
//
//if (isset($_GET['change']) && ($_GET['change'] == "profile" || $_GET['change'] == "cover")) {
//    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
//        $file_type = mime_content_type($_FILES['file']['tmp_name']);
//        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
//        $allowed_size = (1024 * 1024) * 7; // 7MB
//
//        if (in_array($file_type, $allowed_types) && $_FILES['file']['size'] <= $allowed_size) {
//            $folder = "uploads/" . $userid . "/";
//
//            // Create folder if it doesn't exist
//            if (!file_exists($folder)) {
//                mkdir($folder, 0777, true);
//            }
//
//            $image = new Image();
//            $filename = $folder . $image->generate_filename(15) . ".jpg";
//            move_uploaded_file($_FILES['file']['tmp_name'], $filename);
//
//            $image->resize_image($filename, $filename, 1500, 1500);
//
//            if (file_exists($filename)) {
//                $update_query = "";
//                if ($_GET['change'] == "cover") {
//                    if (file_exists($user_data['cover_image'])) {
//                        // Optionally unlink the old cover image
//                        // unlink($user_data['cover_image']);
//                    }
//                    $update_query = "UPDATE users SET cover_image = '$filename' WHERE userid = '$userid' LIMIT 1";
//                } else {
//                    if (file_exists($user_data['profile_image'])) {
//                        // Optionally unlink the old profile image
//                        // unlink($user_data['profile_image']);
//                    }
//                    $update_query = "UPDATE users SET profile_image = '$filename' WHERE userid = '$userid' LIMIT 1";
//                }
//
//                if ($DB->save($update_query)) {
//                    $post = new Post();
//                    $post->create_post($userid, ['is_' . $_GET['change'] . '_image' => 1], $filename);
//
//                    header("Location: profile.php");
//                    exit;
//                } else {
//                    echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Error updating image!</div>";
//                }
//            } else {
//                echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Image upload failed!</div>";
//            }
//        } else {
//            echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Invalid file type or size! Only JPEG, PNG, GIF up to 7MB allowed.</div>";
//        }
//    } else {
//        echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Please add a valid image!</div>";
//    }
////} else {
////    echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Invalid change request.</div>";
//}
//?>
<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'classes/connect.php';
require_once 'classes/image.php';
require_once 'classes/post.php';

// Debugging: Check if session variables are set
echo "Session ID: " . session_id() . "<br>";
echo "User ID in Session: " . (isset($_SESSION['userid']) ? $_SESSION['userid'] : "Not set") . "<br>";

if (!isset($_SESSION['userid'])) {
    echo "User is not logged in.";
    exit;
}

$userid = $_SESSION['userid'];

// Initialize user data
$DB = new Database();
$query = "SELECT * FROM users WHERE userid = '$userid' LIMIT 1";
$result = $DB->read($query);

if ($result) {
    $user_data = $result[0];
} else {
    echo "User data not found.";
    exit;
}

// Function to update user image in the database
function updateImage($DB, $userid, $user_data, $imageType, $filename) {
    // Determine if we are changing cover or profile image
    $image_column = ($imageType == "cover") ? "cover_image" : "profile_image";
    $current_image = $user_data[$image_column];

    // Remove existing image if it exists
    if (!empty($current_image) && file_exists($current_image)) {
        unlink($current_image);
    }

    // Build the query to update the image
    $update_query = "UPDATE users SET $image_column = '$filename' WHERE userid = '$userid' LIMIT 1";

    return $DB->save($update_query);
}

// Check if a profile or cover image change is requested
if (isset($_GET['change']) && ($_GET['change'] == "profile" || $_GET['change'] == "cover")) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_type = mime_content_type($_FILES['file']['tmp_name']);
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $allowed_size = (1024 * 1024) * 7; // 7MB limit

        if (in_array($file_type, $allowed_types) && $_FILES['file']['size'] <= $allowed_size) {
            $folder = "uploads/" . $userid . "/";

            // Create folder if it doesn't exist
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $image = new Image();
            $filename = $folder . $image->generate_filename(15) . ".jpg";
            move_uploaded_file($_FILES['file']['tmp_name'], $filename);

            // Resize image to max 1500x1500
            $image->resize_image($filename, $filename, 1500, 1500);

            if (file_exists($filename)) {
                // Update the image in the database
                if (updateImage($DB, $userid, $user_data, $_GET['change'], $filename)) {
                    // Create a post about the image change
                    $post = new Post();
                    $post->create_post($userid, ['is_' . $_GET['change'] . '_image' => 1], $filename);

                    // Redirect to profile page
                    header("Location: profile.php");
                    exit;
                } else {
                    echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Error updating image in the database!</div>";
                }
            } else {
                echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Image upload failed!</div>";
            }
        } else {
            echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Invalid file type or size! Only JPEG, PNG, GIF up to 7MB allowed.</div>";
        }
    } else {
        echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Please add a valid image!</div>";
    }
} else {
    echo "<div style='text-align:center;font-size:12px;color:white;background-color:grey;'>Invalid change request.</div>";
}
?>


