<?php

class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "ba#83.!";
    private $db = "mybook_db";

    public $conn;

    function __construct()
    {
        $this->connect();
    }

    function connect()
    {
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->db);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    function read($query)
    {
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Error executing query: " . mysqli_error($this->conn));
        } else {
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            return !empty($data) ? $data : false;
        }
    }

    function save($query)
    {
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Error executing query: " . mysqli_error($this->conn));
        }

        return true;
    }

    // Correct the prepare method to use $conn
    public function prepare($query)
    {
        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $query)) {
            die("Prepare failed: " . mysqli_error($this->conn));
        }
        return $stmt;
    }


    public function write($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }

        if ($params) {
            $types = str_repeat('s', count($params)); // Assuming all parameters are strings
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->affected_rows;
    }
}
?>
