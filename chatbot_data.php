<?php
session_start();

// Database connection
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

// Initialize a token for the form if it doesn't exist
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['chatbot_query']) && isset($_POST['chatbot_reply']) && isset($_POST['form_token'])) {
    // Check if the token matches
    if (isset($_POST['form_token']) && $_POST['form_token'] === $_SESSION['form_token']) {
        // Clear the token after successful form submission
        unset($_SESSION['form_token']);

        // Trim and check if inputs are not empty
        $query = trim($_POST['chatbot_query']);
        $reply = trim($_POST['chatbot_reply']);

        if (!empty($query) && !empty($reply)) {
            $sql = "INSERT INTO chatbot (queries, replies) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $query, $reply);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch existing chatbot data
$sql = "SELECT id, queries, replies FROM chatbot ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Data Management</title>
    <style>
        /* Existing CSS */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .container {
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }

        h2 {
            color: #343a40;
            margin-bottom: 20px;
        }

        .news-form {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }

        .news-form input, .news-form textarea {
            display: block;
            width: 96%;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .news-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .news-form button:hover {
            background-color: #0056b3;
        }

        .news-list {
            width: 100%;
            border-collapse: collapse;
        }

        .news-list th, .news-list td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .news-list th {
            background-color: #007bff;
            color: #ffffff;
            font-weight: bold;
        }

        .news-list tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .news-list tr:hover {
            background-color: #e9ecef;
        }

        .news-list .edit-button, .news-list .delete-button {
            background-color: #007bff;
            margin-bottom: 7px;
            width: 63px;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }

        .news-list .edit-button:hover {
            background-color: #0056b3;
        }

        .news-list .delete-button {
            background-color: #dc3545;
        }

        .news-list .delete-button:hover {
            background-color: #c82333;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            padding: 20px;
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .popup.show {
            display: block;
        }

        .popup .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            cursor: pointer;
        }

        .popup form {
            display: flex;
            flex-direction: column;
        }

        .popup form input {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .popup form button {
            padding: 10px;
            margin: 3px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<header>
    <h1>Chatbot Data Management</h1>
</header>


<div class="container">
    <!-- Form to Add New Chatbot Data -->
    <div class="news-form">
        <h2>Add New Chatbot Data</h2>
        <form method="post" action="chatbot_data.php">
            <?php if (isset($_SESSION['form_token'])): ?>
                <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($_SESSION['form_token']); ?>">
            <?php endif; ?>
            <input type="text" name="chatbot_query" placeholder="Query" required>
            <textarea name="chatbot_reply" placeholder="Reply" rows="5" required></textarea>
            <button type="submit">Add Data</button>
        </form>
    </div>

    <!-- Table to Display Existing Chatbot Data -->
    <div class="news-form">
        <h2>Existing Chatbot Data</h2>
        <table class="news-list">
            <thead>
            <tr>
                <th>Query</th>
                <th>Reply</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['queries']); ?></td>
                    <td><?php echo htmlspecialchars($row['replies']); ?></td>
                    <td>
                        <button class="edit-button" onclick="openEditPopup(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['queries'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['replies'], ENT_QUOTES); ?>')">Edit</button>
                        <button class="delete-button" onclick="openDeletePopup(<?php echo $row['id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Popup -->
<div id="edit-popup" class="popup">
    <span class="close" onclick="closeEditPopup()">&times;</span>
    <h2>Edit Chatbot Data</h2>
    <form id="edit-form">
        <input type="hidden" name="id" id="edit-id">
        <input type="text" name="query" id="edit-query" placeholder="Query" required>
        <textarea name="reply" id="edit-reply" placeholder="Reply" rows="5" required></textarea>
        <button type="submit">Update</button>
    </form>
</div>

<!-- Delete Popup -->
<div id="delete-popup" class="popup">
    <span class="close" onclick="closeDeletePopup()">&times;</span>
    <h2>Delete Chatbot Data</h2>
    <p>Are you sure you want to delete this entry?</p>
    <form id="delete-form">
        <input type="hidden" name="id" id="delete-id">
        <button type="submit">Yes, Delete</button>
        <button type="button" onclick="closeDeletePopup()">Cancel</button>
    </form>
</div>

<script>
    function openEditPopup(id, query, reply) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-query').value = query;
        document.getElementById('edit-reply').value = reply;
        document.getElementById('edit-popup').classList.add('show');
    }

    function closeEditPopup() {
        document.getElementById('edit-popup').classList.remove('show');
    }

    function openDeletePopup(id) {
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-popup').classList.add('show');
    }

    function closeDeletePopup() {
        document.getElementById('delete-popup').classList.remove('show');
    }

    document.getElementById('edit-form').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'update');

        fetch('update_chat_data.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(() => {
            location.reload();
        });
    };

    document.getElementById('delete-form').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'delete');

        fetch('delete_chat_data.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(() => {
            location.reload();
        });
    };
</script>
<?php $conn->close(); ?>
</body>
</html>
