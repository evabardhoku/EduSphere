<?php 
include_once "connect.php";
Class Settings
{
    private $db;

    public function __construct() {
        $this->db = new Database(); // Ensure your Database class is correctly instantiated
    }

	public function get_settings($id)
	{
		$DB = new Database();
		$sql = "select * from users where userid = '$id' limit 1";
		$row = $DB->read($sql);

		if(is_array($row)){

			return $row[0];
		}
	}

	public function save_settings($data,$id){

		$DB = new Database();

		$password = $data['password'];
		if(strlen($password) < 30){

			if($data['password'] == $data['password2']){
				$data['password'] = hash("sha1", $password);
			}else{

				unset($data['password']);
			}
		}

		unset($data['password2']);

		$sql = "update users set ";
		foreach ($data as $key => $value) {
			# code...

			$sql .= $key . "='" . $value. "',";
		}

		$sql = trim($sql,",");
		$sql .= " where userid = '$id' limit 1";
		$DB->save($sql);
	}

    public function get_group_settings($group_id) {
        // Validate the group_id to be a numeric value
        if (!is_numeric($group_id)) {
            return null;
        }

        // Prepare the query
        $query = "SELECT * FROM group_table WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);

        // Bind parameters and execute
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the settings
        if ($result->num_rows > 0) {
            return $result->fetch_assoc(); // Return the settings as an associative array
        } else {
            return null; // No settings found for the given group ID
        }
    }


//    public function save_group_settings($group_id, $data)
//    {
//        if (!is_numeric($group_id) || !is_array($data)) {
//            return false;
//        }
//
//        // Prepare the query
//        $query = "UPDATE group_table SET name = ? WHERE id = ?";
//        $stmt = $this->db->prepare($query);
//
//        if ($stmt === false) {
//            // Output error if preparation fails
//            echo "Error preparing statement: " . $this->db->error;
//            return false;
//        }
//
//        // Bind parameters and execute
//        $stmt->bind_param("si", $data['name'], $group_id);
//        $result = $stmt->execute();
//
//        if ($result === false) {
//            // Output error if execution fails
//            echo "Error executing statement: " . $stmt->error;
//        }
//
//        return $result;
//    }

    public function save_group_settings($group_id, $data)
    {
        // Check if group ID is valid and data is an array
        if (!is_numeric($group_id) || !is_array($data)) {
            return false;
        }

        // Prepare dynamic query based on which fields are set
        $fields = [];
        $params = [];
        $types = "";

        // Dynamically add fields to update
        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
            $types .= "s";  // 's' for string
        }
        if (isset($data['type'])) {
            $fields[] = "type = ?";
            $params[] = $data['type'];
            $types .= "s";
        }
        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $params[] = $data['description'];
            $types .= "s";
        }
        if (isset($data['cover_image'])) {
            $fields[] = "cover_image = ?";
            $params[] = $data['cover_image'];
            $types .= "s";
        }

        // If no fields are provided, return false
        if (empty($fields)) {
            return false;
        }

        // Add the group_id to parameters and types
        $params[] = $group_id;
        $types .= "i";  // 'i' for integer

        // Convert the fields array into a string for the SQL query
        $fields_sql = implode(", ", $fields);

        // Prepare the final SQL query
        $query = "UPDATE group_table SET $fields_sql WHERE id = ?";

        // Prepare the statement
        $stmt = $this->db->prepare($query);
        if ($stmt === false) {
            // Output error if preparation fails
            echo "Error preparing statement: " . $this->db->error;
            return false;
        }

        // Bind parameters dynamically
        $stmt->bind_param($types, ...$params);

        // Execute the statement and check for errors
        $result = $stmt->execute();
        if ($result === false) {
            // Output error if execution fails
            echo "Error executing statement: " . $stmt->error;
            return false;
        }

        return true;
    }

    public function can_view_group($user_id, $group_id)
    {
        // Fetch the group information
        $query = "SELECT owner_id, type FROM group_table WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $group = $result->fetch_assoc();

        if (!$group) {
            return false; // Group not found
        }

        // Check if the group is public or private
        if ($group['type'] === 'Public') {
            return true; // Everyone can view public groups
        }

        // If the group is private, check if the user is the owner or a member
        if ($group['owner_id'] == $user_id) {
            return true; // The group owner can view
        }

        // Check if the user is a member of the group
        $member_query = "SELECT id FROM group_members WHERE groupid = ? AND userid = ? AND disabled = 0";
        $member_stmt = $this->db->prepare($member_query);
        $member_stmt->bind_param("ii", $group_id, $user_id);
        $member_stmt->execute();
        $member_result = $member_stmt->get_result();

        if ($member_result->num_rows > 0) {
            return true; // The user is a group member
        }

        return false; // Not allowed to view private group content
    }



}