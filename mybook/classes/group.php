<?php

require_once 'connect.php'; // Ensure this path is correct

class Group
{
    private $db;
    private $error = "";

    public function __construct()
    {
        // Initialize the Database connection
        $this->db = (new Database())->getConnection();
    }

    public function evaluate($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $this->error .= "$key is empty!<br>";
            }

            if ($key == "group_name" && is_numeric($value)) {
                $this->error .= "Group name can't be a number<br>";
            }

            if ($key == "group_type" && ($value != "Public" && $value != "Private")) {
                $this->error .= "Please enter a valid group type<br>";
            }
        }

        // Check URL address
        $data['url_address'] = str_replace(" ", "_", strtolower($data['group_name']));

        $sql = "SELECT id FROM users WHERE url_address = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $data['url_address']);
        $stmt->execute();
        $check = $stmt->get_result()->fetch_assoc();

        while ($check) {
            $data['url_address'] = str_replace(" ", "_", strtolower($data['group_name'])) . rand(0, 9999);
            $stmt->bind_param("s", $data['url_address']);
            $stmt->execute();
            $check = $stmt->get_result()->fetch_assoc();
        }

        $data['userid'] = $this->create_userid();
        $sql = "SELECT id FROM users WHERE userid = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $data['userid']);
        $stmt->execute();
        $check = $stmt->get_result()->fetch_assoc();

        while ($check) {
            $data['userid'] = $this->create_userid();
            $stmt->bind_param("s", $data['userid']);
            $stmt->execute();
            $check = $stmt->get_result()->fetch_assoc();
        }

        if ($this->error == "") {
            $this->create_group($data);
        } else {
            return $this->error;
        }
    }

    public function create_group($data)
    {
        if (!is_array($data)) {
            throw new Exception('Expected $data to be an array, got ' . gettype($data) . ' instead.');
        }

        // Extract and sanitize input data
        $group_name = ucfirst(addslashes($data['group_name']));
        $userid = $data['userid']; // The ID of the user creating the group
        $description = isset($data['description']) ? addslashes($data['description']) : '';
        $cover_image = isset($data['cover_image']) ? addslashes($data['cover_image']) : '';
        $type = isset($data['type']) ? addslashes($data['type']) : 'group';
        $owner_id = isset($data['owner_id']) ? intval($data['owner_id']) : $_SESSION['mybook_userid'];

        // Set the full path for the cover image
        $cover_image_path = !empty($cover_image) ? 'uploads/' . $cover_image : '';

        // Start a transaction
        $this->db->begin_transaction();

        try {
            // Insert the group into the database
            $query = "INSERT INTO group_table (name, cover_image, description, type, owner_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssssi", $group_name, $cover_image_path, $description, $type, $owner_id);
            if (!$stmt->execute()) {
                throw new Exception("Error inserting group: " . $stmt->error);
            }

            // Get the last inserted group ID
            $group_id = $stmt->insert_id;

            // Insert the group creator into the group_members table
            $query = "INSERT INTO group_members (userid, groupid, role, disabled) VALUES (?, ?, 'admin', 0)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $owner_id, $group_id);
            if (!$stmt->execute()) {
                throw new Exception("Error inserting group member: " . $stmt->error);
            }

            // Commit the transaction
            $this->db->commit();

            return $group_id;

        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $this->db->rollback();
            throw $e;
        }
    }


    public function join_group($groupid, $userid)
    {
        // Sanitize inputs
        $groupid = esc($groupid);
        $userid = esc($userid);

        // Prepare the query to check if a join request already exists
        $query = "SELECT * FROM group_requests WHERE userid = ? AND groupid = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userid, $groupid); // Use "ii" if both are integers
        $stmt->execute();
        $check = $stmt->get_result()->fetch_assoc();

        if ($check) {
            // If a request already exists, enable it if it's disabled
            $query = "UPDATE group_requests SET disabled = 0 WHERE id = ? LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $check['id']);
        } else {
            // If no request exists, insert a new one
            $query = "INSERT INTO group_requests (groupid, userid) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $groupid, $userid); // Use "ii" if both are integers
        }

        // Execute the query
        $stmt->execute();
    }



    public function accept_request($groupid, $userid, $action) {
        // Log the parameters for debugging
        error_log("Executing accept_request with Group ID: $groupid, User ID: $userid, Action: $action");

        // Escape parameters for security
        $groupid = esc($groupid);
        $userid = esc($userid);

        // Start a transaction to ensure atomic operations
        $this->db->begin_transaction();

        try {
            if ($action == 'accept') {
                // Add user to the group
                $query = "INSERT INTO group_members (userid, groupid, role, disabled) VALUES (?, ?, 'member', 0)";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ii", $userid, $groupid);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing insert query: " . $stmt->error);
                }

                // Delete the request from the group_requests table
                $query = "DELETE FROM group_requests WHERE groupid = ? AND userid = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ii", $groupid, $userid);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing delete query: " . $stmt->error);
                }

                // Handle the group_invites table
                $query = "UPDATE group_invites SET disabled = 1 WHERE groupid = ? AND invited_user_id = ? AND disabled = 0";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ii", $groupid, $userid);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing update query on group_invites: " . $stmt->error);
                }

            } elseif ($action == 'decline') {
                // Only delete the request if the action is 'decline'
                $query = "DELETE FROM group_requests WHERE groupid = ? AND userid = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ii", $groupid, $userid);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing delete query: " . $stmt->error);
                }

                // Handle the group_invites table
                $query = "DELETE FROM group_invites WHERE groupid = ? AND invited_user_id = ? AND disabled = 0";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ii", $groupid, $userid);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing delete query on group_invites: " . $stmt->error);
                }
            }

            // Commit the transaction
            $this->db->commit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            error_log("Exception: " . $e->getMessage());
        } finally {
            $stmt->close();
        }
    }

    public function get_user_role_in_group($user_id, $group_id) {
        // Use the instance variable instead of global
        $stmt = $this->db->prepare("SELECT role FROM group_members WHERE userid = ? AND groupid = ?");
        $stmt->bind_param("ii", $user_id, $group_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['role'];
        }

        return null; // User is not a member of the group
    }



    private function add_member($groupid, $userid) {
        $query = "INSERT INTO group_members (userid, groupid, role, disabled) VALUES (?, ?, 'member', 0)";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userid, $groupid);

        if (!$stmt->execute()) {
            throw new mysqli_sql_exception("Error executing query: " . $stmt->error);
        }

        $stmt->close();
    }


public function get_invited($groupid)
    {
        $groupid = addslashes($groupid);
        $me = addslashes($_SESSION['mybook_userid']);

        $query = "SELECT * FROM group_invites WHERE groupid = ? AND userid = ? AND disabled = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $groupid, $me);
        $stmt->execute();
        $check = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $check ?: false;
    }

    public function invite_to_group($groupid, $invited_user_id, $inviter_id) {
        $groupid = (int)$groupid;
        $invited_user_id = (int)$invited_user_id;
        $inviter_id = (int)$inviter_id;

        // Check if the invitation already exists
        $query = "SELECT * FROM group_invites WHERE groupid = ? AND invited_user_id = ? AND inviter = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iii", $groupid, $invited_user_id, $inviter_id);
        $stmt->execute();

        if ($stmt->error) {
            echo "Query error: " . $stmt->error;
            return false; // Indicate failure
        }

        $check = $stmt->get_result()->fetch_assoc();

        if ($check) {
            // Update existing invitation to enabled
            $id = $check['id'];
            $query = "UPDATE group_invites SET disabled = 0 WHERE id = ? LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $id);
        } else {
            // Insert new invitation
            $query = "INSERT INTO group_invites (groupid, userid, inviter, invited_user_id) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("iiii", $groupid, $invited_user_id, $inviter_id, $invited_user_id);
        }

        $stmt->execute();

        if ($stmt->error) {
            echo "Query error: " . $stmt->error;
            return false; // Indicate failure
        }

        // Notify about the invitation
        $group_data = $this->get_group($groupid);
        if (is_array($group_data)) {
            $group_data['owner'] = $inviter_id; // Add owner to group_data
            add_group_notification($this->db, $invited_user_id, "invite", $group_data); // Pass $this->db here
        }

        return true; // Indicate success
    }



    public function get_requests($groupid) {
        $groupid = esc($groupid); // Escape the input to prevent SQL injection

        // Prepare the SQL query
        $query = "SELECT * FROM group_requests WHERE groupid = ? AND disabled = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $groupid); // Adjust parameter type to 'i' for integers if groupid is bigint
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch all results as an associative array
        $requests = $result->fetch_all(MYSQLI_ASSOC);

        // Return results or false if no requests found
        return !empty($requests) ? $requests : false;
    }






    public function get_members($groupid, $limit = 100)
    {
        // Escape the groupid to prevent SQL injection
        $groupid = esc($groupid);

        // Create query without using a placeholder for LIMIT
        $query = "SELECT DISTINCT gm.userid, gm.role, u.first_name, u.last_name, u.profile_image, u.gender, u.online
              FROM group_members AS gm
              LEFT JOIN users AS u ON gm.userid = u.userid
              WHERE gm.groupid = ? AND gm.disabled = 0 AND u.owner IS NULL
              LIMIT $limit";  // Directly append the limit value to the query

        // Prepare the query
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            echo "Error preparing statement: " . $this->db->error;
            return false;
        }

        // Bind the groupid parameter only (no need to bind the limit)
        $stmt->bind_param("i", $groupid);
        if (!$stmt->execute()) {
            echo "Error executing statement: " . $stmt->error;
            return false;
        }

        // Fetch the result
        $members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


        // Return the result or false if no members are found
        return $members ?: false;

        echo "<pre>";
        print_r($group_data);
        echo "</pre>";

    }
    public function get_user_by_userid($userid) {
        $query = "SELECT * FROM users WHERE userid = '$userid' LIMIT 1";
        $DB = new Database();
        $result = $DB->read($query);

        if (is_array($result)) {
            return $result[0]; // Return the user data
        }

        return false;
    }


    public function get_group_members($groupid) {
        $query = "SELECT * FROM group_members WHERE groupid = '$groupid' AND disabled = 0"; // Only fetch active members
        $DB = new Database();
        return $DB->read($query);
    }


    public function get_invites($group_id, $id, $type)
    {
        $group_id = addslashes($group_id);
        $type = addslashes($type);

        if (is_numeric($id)) {
            $sql = "SELECT likes FROM likes WHERE type = ? AND contentid = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $type, $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if ($result) {
                $likes = json_decode($result['likes'], true);

                $members = $this->get_members($group_id, 100000);
                if (is_array($members)) {
                    $members = array_column($members, "userid");

                    if (is_array($likes)) {
                        foreach ($likes as $key => $like) {
                            if (in_array($like['userid'], $members)) {
                                unset($likes[$key]);
                            }
                        }

                        $likes = array_values($likes);
                    }
                }

                return $likes;
            }
        }

        return false;
    }

    private function create_userid()
    {
        $length = rand(4, 19);
        $number = "";
        for ($i = 0; $i < $length; $i++) {
            $number .= rand(0, 9);
        }

        return $number;
    }

//    public function get_my_groups($owner_id)
//    {
//        $query = "SELECT * FROM group_table WHERE owner_id = ?";
//        $stmt = $this->db->prepare($query);
//        $stmt->bind_param("s", $owner_id);
//        $stmt->execute();
//        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
//
//        $query = "SELECT * FROM group_members WHERE disabled = 0 AND userid = ?";
//        $stmt = $this->db->prepare($query);
//        $stmt->bind_param("s", $owner_id);
//        $stmt->execute();
//        $result2 = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
//
//        if (is_array($result2)) {
//            $group_ids = array_column($result2, "groupid");
//            $group_ids = "'" . implode("','", $group_ids) . "'";
//
//            $query = "SELECT * FROM group_table WHERE id IN ($group_ids)";
//            $stmt = $this->db->prepare($query);
//            $stmt->execute();
//            $group_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
//
//            if (is_array($group_rows)) {
//                $result = array_merge($result, $group_rows);
//            }
//        }
//
//        return $result ?: false;
//    }

    public function get_my_groups($owner_id)
    {
        $result = [];

        // Fetch groups owned by the user
        $query = "SELECT * FROM group_table WHERE owner_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fetch groups where the user is a member
        $query = "SELECT groupid FROM group_members WHERE disabled = 0 AND userid = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $owner_id);
        $stmt->execute();
        $result2 = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (is_array($result2) && !empty($result2)) {
            $group_ids = array_column($result2, "groupid");

            if (!empty($group_ids)) {
                $placeholders = implode(',', array_fill(0, count($group_ids), '?'));
                $query = "SELECT * FROM group_table WHERE id IN ($placeholders)";
                $stmt = $this->db->prepare($query);

                // Dynamically bind parameters
                $stmt->bind_param(str_repeat('s', count($group_ids)), ...$group_ids);
                $stmt->execute();
                $group_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                if (is_array($group_rows)) {
                    // Avoid duplicating groups
                    $existing_ids = array_column($result, 'id');
                    foreach ($group_rows as $group) {
                        if (!in_array($group['id'], $existing_ids)) {
                            $result[] = $group;
                        }
                    }
                }
            }
        }

        return !empty($result) ? $result : false;
    }


    public function get_group($group_id)
    {
        // Ensure your SQL query and execution are correct
        $sql = "SELECT * FROM group_table WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $this->db->error);
        }

        $stmt->bind_param("i", $group_id);

        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc(); // Return as associative array
        } else {
            return null; // No group found
        }

    }


    // Method to remove a member from the group
    public function remove_member($group_id, $user_id) {
        $stmt = $this->db->prepare("DELETE FROM group_members WHERE groupid = ? AND userid = ?");
        $stmt->bind_param("ii", $group_id, $user_id);
        return $stmt->execute();
    }

    // Method to update member access
    public function edit_member_access($group_id, $user_id, $role) {
        $stmt = $this->db->prepare("UPDATE group_members SET role = ? WHERE groupid = ? AND userid = ?");
        $stmt->bind_param("sii", $role, $group_id, $user_id);
        return $stmt->execute();
    }

    // Method to get member role
    public function get_member_role($group_id, $user_id) {
        $stmt = $this->db->prepare("SELECT role FROM group_members WHERE groupid = ? AND userid = ?");
        $stmt->bind_param("ii", $group_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['role'];
        }
        return null;
    }


    public function delete_group($groupid) {
        // Log the parameters for debugging
        error_log("Deleting Group ID: $groupid");

        // Escape parameters for security
        $groupid = esc($groupid);

        // Start a transaction to ensure atomic operations
        $this->db->begin_transaction();

        try {
            // Delete group posts comments
            $query = "DELETE FROM group_post_comments WHERE post_id IN (SELECT id FROM group_posts WHERE group_id = ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $groupid);
            if (!$stmt->execute()) {
                throw new Exception("Error executing delete query on group_post_comments: " . $stmt->error);
            }

            // Delete group posts likes
            $query = "DELETE FROM group_post_likes WHERE post_id IN (SELECT id FROM group_posts WHERE group_id = ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $groupid);
            if (!$stmt->execute()) {
                throw new Exception("Error executing delete query on group_post_likes: " . $stmt->error);
            }

            // Delete group posts
            $query = "DELETE FROM group_posts WHERE group_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $groupid);
            if (!$stmt->execute()) {
                throw new Exception("Error executing delete query on group_posts: " . $stmt->error);
            }

            // Delete group invites
            $query = "DELETE FROM group_invites WHERE groupid = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $groupid);
            if (!$stmt->execute()) {
                throw new Exception("Error executing delete query on group_invites: " . $stmt->error);
            }

            // Delete group requests
            $query = "DELETE FROM group_requests WHERE groupid = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $groupid);
            if (!$stmt->execute()) {
                throw new Exception("Error executing delete query on group_requests: " . $stmt->error);
            }

            // Delete group members
            $query = "DELETE FROM group_members WHERE groupid = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $groupid);
            if (!$stmt->execute()) {
                throw new Exception("Error executing delete query on group_members: " . $stmt->error);
            }

            // Delete the group itself
            $query = "DELETE FROM group_table WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $groupid);
            if (!$stmt->execute()) {
                throw new Exception("Error executing delete query on group_table: " . $stmt->error);
            }

            // Commit the transaction
            $this->db->commit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            error_log("Exception: " . $e->getMessage());
        } finally {
            $stmt->close();
        }
    }

    public function update_cover_image($group_id, $cover_image_path) {
        $query = "UPDATE group_table SET cover_image = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $cover_image_path, $group_id);
        return $stmt->execute();
    }


}
