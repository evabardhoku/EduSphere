<?php

class Image
{
    public function generate_filename($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $filename = '';
        $characters_length = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $filename .= $characters[rand(0, $characters_length - 1)];
        }

        return $filename;
    }

    public function resize_image($sourcePath, $destinationPath, $maxWidth, $maxHeight)
    {
        list($width, $height) = getimagesize($sourcePath);
        $ratio = $width / $height;

        if ($maxWidth / $maxHeight > $ratio) {
            $maxWidth = $maxHeight * $ratio;
        } else {
            $maxHeight = $maxWidth / $ratio;
        }

        $src = imagecreatefromjpeg($sourcePath);
        $dst = imagecreatetruecolor($maxWidth, $maxHeight);

        // Resize image
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $maxHeight, $width, $height);

        imagejpeg($dst, $destinationPath, 90);
        imagedestroy($src);
        imagedestroy($dst);
    }

    public function crop_image($original_file_name, $cropped_file_name, $max_width, $max_height)
    {
        $original_image = imagecreatefromjpeg($original_file_name);
        $original_width = imagesx($original_image);
        $original_height = imagesy($original_image);

        $ratio = $original_width / $original_height;

        if ($max_width / $max_height > $ratio) {
            $new_width = $max_height * $ratio;
            $new_height = $max_height;
        } else {
            $new_width = $max_width;
            $new_height = $max_width / $ratio;
        }

        $new_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

        $x = ($new_width - $max_width) / 2;
        $y = ($new_height - $max_height) / 2;

        $cropped_image = imagecreatetruecolor($max_width, $max_height);
        imagecopy($cropped_image, $new_image, 0, 0, $x, $y, $max_width, $max_height);

        imagejpeg($cropped_image, $cropped_file_name, 90);
        imagedestroy($original_image);
        imagedestroy($new_image);
        imagedestroy($cropped_image);
    }

    // Create thumbnail for cover image
    public function get_thumb_cover($filename)
    {
        $thumbnail = $filename . "_cover_thumb.jpg";
        if (!file_exists($thumbnail)) {
            $this->crop_image($filename, $thumbnail, 1366, 488);
        }
        return $thumbnail;
    }

    // Create thumbnail for profile image
    public function get_thumb_profile($filename)
    {
        $thumbnail_dir = 'uploads/thumbnails/'; // Ensure this directory exists
        $thumbnail = $thumbnail_dir . basename($filename) . "_profile_thumb.jpg";

        // Check if the original file exists
        if (!file_exists($filename)) {
            error_log("Original image file does not exist: $filename");
            return "images/user_male.jpg"; // Fallback image
        }

        // Check if the thumbnail exists
        if (!file_exists($thumbnail)) {
            // Ensure the directory exists
            if (!file_exists($thumbnail_dir)) {
                mkdir($thumbnail_dir, 0777, true);
            }

            // Generate the thumbnail if it does not exist
            $this->crop_image($filename, $thumbnail, 600, 600);
        }

        // Debugging: Log the thumbnail path being returned
        error_log("Returning thumbnail path: $thumbnail");

        return $thumbnail;
    }



    // Create thumbnail for post image
    public function get_thumb_post($filename)
    {
        $thumbnail = $filename . "_post_thumb.jpg";
        if (!file_exists($thumbnail)) {
            $this->crop_image($filename, $thumbnail, 600, 600);
        }
        return $thumbnail;
    }

    public function update_profile_image($user_id, $file_path) {
        $db = new Database();
        $stmt = $db->prepare("UPDATE users SET profile_image = ? WHERE userid = ?");
        $stmt->bind_param("si", $file_path, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Update cover image
    public function update_cover_image($user_id, $file_path) {
        $db = new Database();
        $stmt = $db->prepare("UPDATE users SET cover_image = ? WHERE userid = ?");
        $stmt->bind_param("si", $file_path, $user_id);
        $stmt->execute();
        $stmt->close();
    }


}
?>
