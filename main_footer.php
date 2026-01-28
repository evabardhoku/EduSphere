<!-- main_footer.php -->
<style>
    /* Footer Styles */
    footer {
        background-image: linear-gradient(to right bottom, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);
        color: white;
        padding: 40px 0;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        text-align: left;
    }

    .footer-section {
        flex: 1;
        padding: 0 20px;
    }

    .footer-section h2 {
        font-size: 20px;
        margin-bottom: 20px;
    }

    .footer-section p {
        font-size: 16px;
        line-height: 1.5;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li {
        margin-bottom: 10px;
    }

    .footer-section ul li a {
        color: white;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-section ul li a:hover {
        color: #ffcc00;
    }

    .footer-section .social-icons a {
        color: white;
        font-size: 24px;
        margin-right: 15px;
        transition: color 0.3s ease;
    }

    .footer-section .social-icons a:hover {
        color: #ffcc00;
    }

    .footer-bottom {
        text-align: center;
        padding: 20px;
        background-color: rgba(0, 0, 0, 0.1);
        font-size: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.3);
    }

    @media (max-width: 900px) {
        .footer-container {
            flex-direction: column;
            text-align: center;
        }

        .footer-section {
            padding: 20px 0;
        }

        .footer-bottom {
            padding: 10px 0;
        }
    }

</style>
<footer>
    <div class="footer-container">
        <div class="footer-section about">
            <h2>About Us</h2>
            <p>
                Welcome to the Student App, a platform designed to bring students closer through social networking, forums, and chat services. Learn, connect, and grow with us.
            </p>
        </div>
        <div class="footer-section links">
            <h2>Quick Links</h2>
            <ul>
                <li> <a href="mybook/index.php" class="nav-item">Social Network</a></li>
                <li><a href="forum/index.php" class="nav-item">Forum</a></li>
                <li><a href="chatapp/index.php" class="nav-item">Messenger</a></li>
                <li><a href="chatbot - php & ajax/bot.php" class="nav-item">Chatbot</a></li>
                <li><a href="about.php" class="nav-item">About</a></li>
            </ul>
        </div>
        <div class="footer-section contact">
            <h2>Contact Us</h2>
            <p>Email: support@studentapp.com</p>
            <p>Phone: +123 456 7890</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 Fakulteti i Teknologjise se Informacionit. All rights reserved.</p>
    </div>
</footer>
