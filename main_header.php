<!-- main_header.php -->
<style>

    header {
        background-image: linear-gradient(to right bottom, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);
        padding: 20px 0;
        color: white;
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo img {
        max-width: 170px;
        height: auto;
    }

    .nav-container {
        text-align: center;
    }

    h1 {
        font-size: 24px;
        margin: 0;
        padding: 10px 0;
        color: #fff;
    }

    nav {
        display: flex;
        justify-content: center;
        padding: 10px;
    }

    .nav-item {
        margin: 0 15px;
        color: white;
        text-decoration: none;
        font-size: 18px;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    .nav-item:hover {
        color: #ffcc00;
        text-decoration: underline;
    }

    header a.logo {
        display: flex;
        align-items: center;
    }

    @media (max-width: 900px) {
        .header-container {
            flex-direction: column;
            text-align: center;
        }

        .logo img {
            margin-bottom: 15px;
        }

        nav {
            flex-direction: column;
        }

        .nav-item {
            margin: 10px 0;
        }
    }
</style>

<header>
    <div class="header-container">
        <div class="logo">
            <a href="home.php">
                <img src="final.png" alt="Student App Logo">
            </a>
        </div>
        <div class="nav-container">
            <h1>Student App Home</h1>
            <nav>
                <a href="mybook/index.php" class="nav-item">Social Network</a>
                <a href="forum/index.php" class="nav-item">Forum</a>
                <a href="chatapp/index.php" class="nav-item">Messenger</a>
                <a href="chatbot - php & ajax/bot.php" class="nav-item">Chatbot</a>
                <a href="about.php" class="nav-item">About</a>
            </nav>
        </div>
    </div>
</header>
