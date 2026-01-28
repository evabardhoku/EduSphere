<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "ba#83.!";
$dbname = "mybook_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle professor addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_professor_name']) && isset($_POST['new_professor_short_summary']) && isset($_POST['new_professor_detailed_summary'])) {
    $name = $_POST['new_professor_name'];
    $short_summary = $_POST['new_professor_short_summary'];
    $detailed_summary = $_POST['new_professor_detailed_summary'];

    $photo = $_FILES['new_professor_photo']['name'];
    $upload_dir = __DIR__ . '/uploads/';
    $target_file = $upload_dir . basename($photo);

    if (move_uploaded_file($_FILES['new_professor_photo']['tmp_name'], $target_file)) {
        echo "The file has been uploaded.";
    } else {
        echo "There was an error uploading the file, please try again.";
    }

    $sql = "INSERT INTO professors (name, photo, short_summary, detailed_summary) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $name, $photo, $short_summary, $detailed_summary);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
}

// Handle professor update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['professor_id']) && isset($_POST['professor_name']) && isset($_POST['professor_short_summary']) && isset($_POST['professor_detailed_summary'])) {
    $id = $_POST['professor_id'];
    $name = $_POST['professor_name'];
    $short_summary = $_POST['professor_short_summary'];
    $detailed_summary = $_POST['professor_detailed_summary'];

    $photo = $_FILES['professor_photo']['name'];
    if ($photo) {
        $upload_dir = __DIR__ . '/uploads/';
        $target_file = $upload_dir . basename($photo);

        if (move_uploaded_file($_FILES['professor_photo']['tmp_name'], $target_file)) {
            echo "The file has been uploaded.";
        } else {
            echo "There was an error uploading the file, please try again.";
        }
    } else {
        // Keep the existing photo if none is uploaded
        $photo = $_POST['existing_photo'];
    }

    $sql = "UPDATE professors SET name=?, photo=?, short_summary=?, detailed_summary=? WHERE id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $name, $photo, $short_summary, $detailed_summary, $id);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
}

// Handle professor deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_professor_id'])) {
    $id = $_POST['delete_professor_id'];

    $sql = "DELETE FROM professors WHERE id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
}

// Fetch all professors for display
$sql = "SELECT id, name, photo, short_summary, detailed_summary FROM professors";
$result = $conn->query($sql);

// Fetch professor details for editing
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT name, photo, short_summary, detailed_summary FROM professors WHERE id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $photo, $short_summary, $detailed_summary);

    if ($stmt->fetch()) {
        echo json_encode(['name' => $name, 'photo' => $photo, 'short_summary' => $short_summary, 'detailed_summary' => $detailed_summary]);
    } else {
        echo json_encode(['error' => 'Professor not found']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professors Update</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
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

        .professor-form, .update-form {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }

        .professor-form input, .professor-form textarea,
        .update-form input, .update-form textarea {
            display: block;
            width: 96%;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .professor-form button, .update-form button {
            padding: 10px 20px;
            background-color: #41D3CA; /* Main color from gradient */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .professor-form button:hover, .update-form button:hover {
            background-color: #3baef1; /* Slightly darker shade from gradient */
        }

        .professor-list li {
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

        .professor-list a {
            text-decoration: none;
            color: #41D3CA; /* Main color from gradient */
            font-weight: bold;
            font-size: 18px;
            display: block;
            flex-grow: 1;
        }

        .professor-list button {
            background-color: #41D3CA; /* Main color from gradient */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            margin-left: 5px; /* Add some spacing between buttons */
        }

        .professor-list button:hover {
            background-color: #3baef1; /* Slightly darker shade from gradient */
        }

        .professor-list button.delete {
            background-color: #dc3545; /* Red color for delete button */
        }

        .professor-list button.delete:hover {
            background-color: #c82333; /* Darker red for hover effect */
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

        .popup form input, .popup form textarea {
            margin-bottom: 10px;
        }

        .popup form button {
            padding: 10px;
            margin: 3px;
            background-color: #41D3CA; /* Main color from gradient */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup form button:hover {
            background-color: #3baef1; /* Slightly darker shade from gradient */
        }

        .h1-container {
            display: flex;
            justify-content: center; /* Horizontally center */
            align-items: center; /* Vertically center */
            text-align: center; /* Center text */
        }


    </style>

</head>
<body>
<header>
    <?php
    include "main_header.php";
    ?>

</header>
<div class="h1-container">
    <h1 style="color: black">Manage Professors</h1>
</div>

<div class="container">
    <div class="professor-form">
        <h2>Add New Professor</h2>
        <form id="add-professor-form" enctype="multipart/form-data">
            <input type="text" name="new_professor_name" placeholder="Name" required>
            <input type="file" name="new_professor_photo" accept="image/*" required>
            <textarea name="new_professor_short_summary" rows="4" placeholder="Short Summary" required></textarea>
            <textarea name="new_professor_detailed_summary" rows="4" placeholder="Detailed Summary" required></textarea>
            <button type="submit">Add Professor</button>
        </form>
    </div>
    <div class="professor-list">
        <h2>All Professors</h2>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <a href="#"><?= htmlspecialchars($row['name']) ?></a>
                    <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" style="width: 50px; height: auto;">
                    <button class="update-professor" data-id="<?= htmlspecialchars($row['id']) ?>">Update</button>
                    <button class="delete-professor" data-id="<?= htmlspecialchars($row['id']) ?>">Delete</button>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>
<div class="popup" id="update-popup">
    <span class="close">&times;</span>
    <h2>Update Professor</h2>
    <form id="update-form" enctype="multipart/form-data">
        <input type="hidden" name="professor_id" id="update-professor-id">
        <input type="text" name="professor_name" id="update-professor-name" placeholder="Name" required>
        <input type="file" name="professor_photo" id="update-professor-photo" accept="image/*">
        <input type="hidden" name="existing_photo" id="existing-photo">
        <textarea name="professor_short_summary" id="update-professor-short-summary" rows="4" placeholder="Short Summary" required></textarea>
        <textarea name="professor_detailed_summary" id="update-professor-detailed-summary" rows="4" placeholder="Detailed Summary" required></textarea>
        <button type="submit">Update Professor</button>
    </form>
</div>
</body>
<footer>
    <?php
    include "main_footer.php";
    ?>
</footer>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.update-professor').forEach(button => {
            button.addEventListener('click', function() {
                const professorId = this.getAttribute('data-id');
                fetch('professors_update.php?id=' + professorId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            document.getElementById('update-professor-id').value = professorId;
                            document.getElementById('update-professor-name').value = data.name;
                            document.getElementById('update-professor-short-summary').value = data.short_summary;
                            document.getElementById('update-professor-detailed-summary').value = data.detailed_summary;
                            document.getElementById('existing-photo').value = data.photo;
                            document.getElementById('update-popup').classList.add('show');
                        }
                    });
            });
        });

        document.querySelector('.popup .close').addEventListener('click', function() {
            document.getElementById('update-popup').classList.remove('show');
        });

        document.getElementById('update-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('professors_update.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
                .then(text => {
                    alert(text);
                    location.reload();
                });
        });

        document.getElementById('add-professor-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('professors_update.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
                .then(text => {
                    alert(text);
                    location.reload();
                });
        });

        document.querySelectorAll('.delete-professor').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this professor?')) {
                    const professorId = this.getAttribute('data-id');
                    fetch('professors_update.php', {
                        method: 'POST',
                        body: new URLSearchParams('delete_professor_id=' + professorId)
                    }).then(response => response.text())
                        .then(text => {
                            alert(text);
                            location.reload();
                        });
                }
            });
        });
    });
</script>
