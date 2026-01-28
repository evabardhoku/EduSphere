<?php
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

// Fetch news titles
$sql = "SELECT id, title FROM news ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);

//$db = new Database();
$query = "SELECT * FROM professors";
$professors = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student App Home</title>
    <!-- Linking Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link rel="stylesheet" href="styles.css">

</head>
<body>

<?php
include 'main_header.php';
?>

<main>

    <section class="news-section">
        <h2>Latest News</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="news-item">
                    <a href="news_detail.php?id=<?php echo urlencode($row['id']); ?>">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No news available.</p>
        <?php endif; ?>
    </section>

    <section class="department-section">
        <div class="cards">
            <h2 class="header">
                Departamentet Tona
            </h2>
            <div class="services">
                <div class="content content-1">
                    <i class="fa-solid fa-laptop-code" style="font-size: 100px"></i> <!-- Ikona për Inxhinieri Informatike -->
                    <h2>
                        Inxhinieri Informatike
                    </h2>
                    <p>
                        Ky departament fokusohet në teknologjinë e informacionit dhe ofron një gamë të gjerë kursesh që përgatitin studentët për zhvillimin e softuerit, rrjetet dhe bazat e të dhënave.
                    </p>
                    <a href="#">Lexo më shumë</a>
                </div>
                <div class="content content-2">
                    <i class="fa-solid fa-microchip" style="font-size: 100px"></i> <!-- Ikona për Inxhinieri Elektronike -->
                    <h2>
                        Inxhinieri Elektronike
                    </h2>
                    <p>
                        Departamenti i Inxhinierisë Elektronike ofron një formim të avancuar në dizajnimin dhe zhvillimin e sistemeve elektronike dhe aplikacioneve moderne të elektronikës.
                    </p>
                    <a href="#">Lexo më shumë</a>
                </div>
                <div class="content content-3">
                    <i class="fa-solid fa-satellite-dish" style="font-size: 100px"></i> <!-- Ikona për Inxhinieri Telekomunikacioni -->
                    <h2>
                        Inxhinieri Telekomunikacioni
                    </h2>
                    <p>
                        Në këtë departament, studentët përgatiten për të punuar në fushën e telekomunikacioneve, përfshirë rrjetet celulare, komunikimin satelitor dhe internetin e gjërave (IoT).
                    </p>
                    <a href="#">Lexo më shumë</a>
                </div>
            </div>
        </div>
    </section>


    <section class="section professors-section">
        <h2 class="section-title">Our Professors</h2>
        <div class="wrapper-container"> <!-- New container to hold all cards -->
            <?php foreach ($professors as $professor): ?>
                <div class="wrapper"> <!-- Each professor has a wrapper for the card -->
                    <div class="card front-face">
                        <img src="uploads/<?php echo htmlspecialchars($professor['photo']); ?>" alt="Professor Photo">
                    </div>
                    <div class="card back-face">
                        <img src="uploads/<?php echo htmlspecialchars($professor['photo']); ?>" alt="Professor Photo">
                        <div class="info">
                            <div class="title">
                                <?php echo htmlspecialchars($professor['name']); ?>
                            </div>
                            <p>
                                <?php echo nl2br(htmlspecialchars($professor['short_summary'])); ?>
                            </p>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>


</main>

<?php
include "main_footer.php";
?>

<button class="admin-button" onclick="showPopup()">Are you an admin?</button>
<div id="admin-popup" class="popup">
    <span class="close" onclick="closePopup()">&times;</span>
    <h2>Admin Login</h2>
    <form id="admin-form">
        <input type="text" id="admin-username" name="username" placeholder="Username" required>
        <input type="password" id="admin-password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@latest/swiper-bundle.min.js"></script>
<script>
    function showPopup() {
        document.getElementById('admin-popup').classList.add('show');
    }

    function closePopup() {
        document.getElementById('admin-popup').classList.remove('show');
    }

    document.getElementById('admin-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var username = document.getElementById('admin-username').value;
        var password = document.getElementById('admin-password').value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "admin_login.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                if (xhr.responseText === 'success') {
                    window.location.href = 'dashboard.php'; // Redirect to admin dashboard
                } else {
                    alert('Invalid credentials');
                }
            }
        };
        xhr.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
    });


</script>

</body>
</html>

<?php
$conn->close();
?>
