<?php //
//
//class Signup
//{
//
//    private $error = "";
//
//    public function evaluate($data)
//    {
//        foreach ($data as $key => $value) {
//            if (empty($value)) {
//                $this->error .= "$key is empty!<br>";
//            }
//
//            if ($key == "email") {
//                if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $value)) {
//                    $this->error .= "Invalid email address!<br>";
//                }
//            }
//
//            if ($key == "first_name" || $key == "last_name") {
//                if (is_numeric($value)) {
//                    $this->error .= "$key cannot be a number<br>";
//                }
//                if (strstr($value, " ")) {
//                    $this->error .= "$key cannot have spaces<br>";
//                }
//            }
//        }
//
//        $DB = new Database();
//
//        // Ensure tag_name is set
//        $data['tag_name'] = strtolower($data['first_name'] . $data['last_name']);
//
//        // Ensure uniqueness for tag_name
//        $sql = "SELECT id FROM users WHERE tag_name = '{$data['tag_name']}' LIMIT 1";
//        $check = $DB->read($sql);
//        while (is_array($check)) {
//            $data['tag_name'] = strtolower($data['first_name'] . $data['last_name']) . rand(0, 9999);
//            $sql = "SELECT id FROM users WHERE tag_name = '{$data['tag_name']}' LIMIT 1";
//            $check = $DB->read($sql);
//        }
//
//        $data['userid'] = $this->create_userid();
//
//        // Ensure uniqueness for userid
//        $sql = "SELECT id FROM users WHERE userid = '{$data['userid']}' LIMIT 1";
//        $check = $DB->read($sql);
//        while (is_array($check)) {
//            $data['userid'] = $this->create_userid();
//            $sql = "SELECT id FROM users WHERE userid = '{$data['userid']}' LIMIT 1";
//            $check = $DB->read($sql);
//        }
//
//        // Check email uniqueness
//        $sql = "SELECT id FROM users WHERE email = '{$data['email']}' LIMIT 1";
//        $check = $DB->read($sql);
//        if (is_array($check)) {
//            $this->error .= "Another user is already using that email<br>";
//        }
//
//        if ($this->error == "") {
//            // No error
//            $this->create_user($data);
//        } else {
//            return $this->error;
//        }
//    }
//
//
//    public function create_user($data)
//    {
//        $first_name = ucfirst($data['first_name']);
//        $last_name = ucfirst($data['last_name']);
//        $gender = $data['gender'];
//        $email = $data['email'];
//        $password = $data['password'];
//        $userid = $data['userid'];
//        $tag_name = $data['tag_name'];
//        $about = isset($data['about']) ? $data['about'] : '';
//
//        $password = hash("sha1", $password);
//        $url_address = strtolower($first_name) . "." . strtolower($last_name);
//
//        $query = "INSERT INTO users
//        (userid, first_name, last_name, gender, email, password, url_address, tag_name, about)
//        VALUES
//        ('$userid', '$first_name', '$last_name', '$gender', '$email', '$password', '$url_address', '$tag_name', '$about')";
//
//        $DB = new Database();
//        $DB->save($query);
//    }
//
//
//
//    private function create_userid()
//    {
//        $length = rand(4, 19);
//        $number = "";
//        for ($i = 0; $i < $length; $i++) {
//            $new_rand = rand(0, 9);
//            $number .= $new_rand;
//        }
//        return (int)$number; // Ensure the return value is an integer
//    }
//}


class Signup
{
    private $error = false;

    public function evaluate($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $this->error = true; // Set error flag
                break; // Exit loop on first error
            }

            if ($key == "email" && !preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $value)) {
                $this->error = true; // Set error flag
                break; // Exit loop on first error
            }

            if (($key == "first_name" || $key == "last_name") && (is_numeric($value) || strstr($value, " "))) {
                $this->error = true; // Set error flag
                break; // Exit loop on first error
            }
        }

        $DB = new Database();

        // Ensure tag_name is set
        $data['tag_name'] = strtolower($data['first_name'] . $data['last_name']);

        // Ensure uniqueness for tag_name
        $sql = "SELECT id FROM users WHERE tag_name = '{$data['tag_name']}' LIMIT 1";
        $check = $DB->read($sql);
        while (is_array($check)) {
            $data['tag_name'] = strtolower($data['first_name'] . $data['last_name']) . rand(0, 9999);
            $sql = "SELECT id FROM users WHERE tag_name = '{$data['tag_name']}' LIMIT 1";
            $check = $DB->read($sql);
        }

        $data['userid'] = $this->create_userid();

        // Ensure uniqueness for userid
        $sql = "SELECT id FROM users WHERE userid = '{$data['userid']}' LIMIT 1";
        $check = $DB->read($sql);
        while (is_array($check)) {
            $data['userid'] = $this->create_userid();
            $sql = "SELECT id FROM users WHERE userid = '{$data['userid']}' LIMIT 1";
            $check = $DB->read($sql);
        }

        // Check email uniqueness
        $sql = "SELECT id FROM users WHERE email = '{$data['email']}' LIMIT 1";
        $check = $DB->read($sql);
        if (is_array($check)) {
            $this->error = true; // Set error flag for email uniqueness
        }

        if (!$this->error) {
            // No error, proceed to create the user
            $this->create_user($data);
            header("Location: login.php"); // Redirect to login page
            exit(); // Ensure no further processing
        } else {
            // Redirect to login page if there's an error
            header("Location: login.php");
            exit(); // Ensure no further processing
        }
    }

    public function create_user($data)
    {
        $first_name = ucfirst($data['first_name']);
        $last_name = ucfirst($data['last_name']);
        $gender = $data['gender'];
        $email = $data['email'];
        $password = hash("sha1", $data['password']); // Store hashed password
        $userid = $data['userid'];
        $tag_name = $data['tag_name'];
        $about = isset($data['about']) ? $data['about'] : '';
        $profile_image = isset($data['profile_image']) ? $data['profile_image'] : '';
        $cover_image = isset($data['cover_image']) ? $data['cover_image'] : '';
        $url_address = strtolower($first_name) . "." . strtolower($last_name);
        $owner = 0; // Adjust as necessary
        $type = 'user'; // Adjust as necessary

        $query = "INSERT INTO users 
        (userid, first_name, last_name, gender, email, password, url_address, tag_name, about, profile_image, cover_image, date, online, likes, owner, type) 
        VALUES 
        ('$userid', '$first_name', '$last_name', '$gender', '$email', '$password', '$url_address', '$tag_name', '$about', '$profile_image', '$cover_image', CURRENT_TIMESTAMP, 1, 0, '$owner', '$type')";

        $DB = new Database();
        $DB->save($query);
    }

    private function create_userid()
    {
        $length = rand(4, 19);
        $number = "";
        for ($i = 0; $i < $length; $i++) {
            $new_rand = rand(0, 9);
            $number .= $new_rand;
        }
        return (int)$number; // Ensure the return value is an integer
    }
}
