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

// Fetch admin's name
$admin_name = "Admin"; // Default name, update based on actual admin data
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT username FROM admins WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($username);
    $stmt->fetch();
    $admin_name = $username;
    $stmt->close();
}

// Handle news form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['news_title']) && isset($_POST['news_description'])) {
    $title = $_POST['news_title'];
    $description = $_POST['news_description'];
    $author_id = $_SESSION['admin_id'];
    $created_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO news (title, description, author_id, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $title, $description, $author_id, $created_at);
    $stmt->execute();
    $stmt->close();
}

// Fetch all news
$sql = "SELECT id, title FROM news ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
        <style>
            body {
                font-family: 'Roboto', sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }

            header {
                background-image: linear-gradient(to right bottom, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);
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
                background-color: #719CEB; /* Updated color */
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                transition: background-color 0.3s;
            }

            .news-form button:hover {
                background-color: #4176C0; /* Darker shade for hover effect */
            }

            .news-list li {
                background: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                margin-bottom: 10px;
                padding: 15px;
                position: relative;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .news-list a {
                text-decoration: none;
                color: #41D3CA; /* Updated color */
                font-weight: bold;
                font-size: 18px;
                display: block;
                flex-grow: 1;
            }

            .news-list button {
                background-color: #719CEB; /* Updated color */
                color: white;
                border: none;
                border-radius: 5px;
                padding: 10px;
                cursor: pointer;
                font-size: 14px;
                transition: background-color 0.3s;
                margin-left: 5px; /* Add some spacing between buttons */
            }

            .news-list button:hover {
                background-color: #4176C0; /* Darker shade for hover effect */
            }

            .news-list button.delete {
                background-color: #D95DDD; /* Updated color */
            }

            .news-list button.delete:hover {
                background-color: #B74AB6; /* Darker shade for hover effect */
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
                background-color: #719CEB; /* Updated color */
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .popup form button:hover {
                background-color: #4176C0; /* Darker shade for hover effect */
            }

            .button {
                display: inline-block;
                padding: 10px 20px;
                margin: 0 10px; /* Add space between buttons */
                background-color: #41D3CA; /* Updated color */
                color: white; /* Text color */
                text-align: center;
                text-decoration: none;
                font-size: 16px;
                border-radius: 4px;
                border: none;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .button:hover {
                background-color: #36bfb3; /* Darker shade for hover effect */
            }

            .buttons-container {
                display: flex;
                justify-content: center; /* Center the buttons */
                align-items: center;
                gap: 20px; /* Space between buttons */
                margin: 20px 0; /* Space above and below */
            }
            }


        </style>
</head>
<body>
<?php
include "main_header.php";
?>
    <h1 style="margin-left: 40%;color: black">Hello, <?php echo htmlspecialchars($admin_name); ?>! Welcome back!</h1>

<h2 style="margin-left: 46%">Dashboard</h2>
<div class="buttons-container">
    <!-- Add Chatbot Update Button -->
    <a href="chatbot_data.php" class="button">Chatbot Update</a>
    <!-- Add Professors Update Button -->
    <a href="professors_update.php" class="button">Professors Update</a>
    <!-- Add Feedback Button -->
    <a href="feedback.php" class="button">Users Feedback</a>

</div>





<div class="container">
    <h2>Post New School News</h2>
    <form class="news-form" method="post" action="">
        <input type="text" name="news_title" placeholder="Title" required>
        <textarea name="news_description" placeholder="Description" rows="5" required></textarea>
        <button type="submit">Submit</button>
    </form>

    <h2>All News Posts</h2>
    <ul class="news-list">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <a href="news_detail.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a>
                <button onclick="openEditModal(<?php echo $row['id']; ?>)">Edit</button>
                <button onclick="openDeleteModal(<?php echo $row['id']; ?>)">Delete</button>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- Edit Modal -->
<div id="editModal" class="popup">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit News</h2>
        <form id="editForm" method="post" action="update_news.php">
            <input type="hidden" id="editNewsId" name="news_id">
            <input type="text" id="editNewsTitle" name="news_title" placeholder="Title" required>
            <textarea id="editNewsDescription" name="news_description" placeholder="Description" rows="5" required></textarea>
            <button type="submit">Update</button>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="popup">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h2>Are you sure you want to delete this news post?</h2>
        <form id="deleteForm" method="post" action="delete_news.php">
            <input type="hidden" id="deleteNewsId" name="news_id">
            <button type="submit">Yes, Delete</button>
            <button type="button" onclick="closeDeleteModal()">Cancel</button>
        </form>
    </div>
</div>
<footer>
<?php
include "main_footer.php";
?>

</footer>
<script>
    function openEditModal(id) {
        document.getElementById('editNewsId').value = id;

        // AJAX request to fetch news data
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_news.php?id=' + id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.error) {
                    alert('Error: ' + response.error);
                } else {
                    // Populate the form fields
                    document.getElementById('editNewsTitle').value = response.title;
                    document.getElementById('editNewsDescription').value = response.description;
                    // Show the modal
                    document.getElementById('editModal').classList.add('show');
                }
            } else {
                alert('Request failed. Returned status of ' + xhr.status);
            }
        };
        xhr.send();
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
    }

    function openDeleteModal(id) {
        document.getElementById('deleteNewsId').value = id;
        document.getElementById('deleteModal').classList.add('show');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }
</script>
<?php
$conn->close();
?>
</body>
</html>
