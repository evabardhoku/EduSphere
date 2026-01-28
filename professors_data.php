<?php
// professors_data.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch professor details
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM professors WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();

if (!$professor) {
    echo "Professor not found.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>Professor Details</h1>
    <a href="home.php">Back to Home</a>
</header>

<main>
    <section class="professor-details">
        <img src="<?php echo htmlspecialchars($professor['photo']); ?>" alt="Professor Photo" class="professor-photo">
        <h2><?php echo htmlspecialchars($professor['name']); ?></h2>
        <p><?php echo htmlspecialchars($professor['short_summary']); ?></p>
        <div class="professor-full-summary">
            <h3>Full Summary</h3>
            <p><?php echo htmlspecialchars($professor['detailed_summary']); ?></p>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Student App. All Rights Reserved.</p>
</footer>
</body>
</html>

<?php
$conn->close();
?>
