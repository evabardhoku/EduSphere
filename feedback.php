<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "ba#83.!";
$dbname = "mybook_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();


// Fetch feedback from the database
$query = "SELECT id, username, feedback_text, feedback_date FROM feedback ORDER BY feedback_date DESC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Feedback</title>
    <style>
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white; /* Keeps the background white */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow */
        }

        thead {
            background-color: #007BFF; /* Header background color */
            color: white; /* Header text color */
        }

        th, td {
            padding: 12px; /* Padding inside cells */
            text-align: left; /* Align text to the left */
            border-bottom: 1px solid #ddd; /* Light bottom border */
        }

        tr:hover {
            background-color: #f1f1f1; /* Row background color on hover */
        }

        th {
            font-weight: bold; /* Makes header text bold */
        }

        /* Button Styles */
        .button {
            background-color: #dc3545; /* Red color for delete button */
            color: white; /* Button text color */
            padding: 8px 12px; /* Padding inside buttons */
            text-decoration: none; /* No underline */
            border-radius: 4px; /* Rounded corners */
            transition: background-color 0.3s; /* Smooth transition for hover */
        }

        .button:hover {
            background-color: #c82333; /* Darker red on hover */
        }

        td a {
            text-decoration: none; /* No underline for links */
        }

    </style>
</head>
<header>
    <?php
    include "main_header.php";
    ?>
</header>

<body>

<h1 style="color: black">Users Feedback</h1>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Feedback</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['feedback_text']}</td>
                        <td>{$row['feedback_date']}</td>
                        <td>
                            <a href='delete_feedback.php?id={$row['id']}' class='button'>Delete</a>
                        </td>
                    </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Nuk ka feedback për të shfaqur.</td></tr>";
    }
    ?>
    </tbody>
</table>

</body>
<footer>
    <?php
    include "main_footer.php";
    ?>
</footer>
</html>

<?php
$conn->close();
?>
