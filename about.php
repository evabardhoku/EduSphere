    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>About Us</title>
        <!-- Linking Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

        <!-- Custom CSS to match with home.php -->
        <link rel="stylesheet" href="styles.css">
        <style>
            /* CSS for About Us page */

            /* About section */
            .about-section {
                padding: 50px 20px;
                background-color: #f9f9f9;
                text-align: center;
                font-family: 'Arial', sans-serif;
            }

            .about-section h2 {
                font-size: 2.5rem;
                margin-bottom: 20px;
                color: #333;
            }

            .about-section p {
                font-size: 1.1rem;
                line-height: 1.6;
                margin-bottom: 20px;
                color: #555;
            }

            /* Map section */
            .map-section {
                padding: 50px 20px;
                background-color: #fff;
                text-align: center;
                font-family: 'Arial', sans-serif;
            }

            .map-section h2 {
                font-size: 2.5rem;
                margin-bottom: 20px;
                color: #333;
            }

            .map-container {
                margin: 0 auto;
                max-width: 600px;
                border: 2px solid #ddd;
                box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            }

            /* Wrapper section for map and feedback */
            .map-feedback-container {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin: 50px auto;
                max-width: 1200px; /* Adjust width as needed */
                gap: 20px;
            }

            /* Map section styling */
            .map-section {
                flex: 1;
                padding: 20px;
                background-color: #fff;
                text-align: center;
                font-family: 'Arial', sans-serif;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }

            .map-section h2 {
                font-size: 1.8rem;
                margin-bottom: 20px;
                color: #333;
            }

            .map-container {
                width: 100%;
                border: 2px solid #ddd;
                box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            }

            .feedback-form h2 {
                font-size: 1.8rem;
                margin-bottom: 20px;
                color: #333;
            }

            .feedback-form label {
                font-size: 1.2rem;
                color: #333;
                font-weight: bold;
            }

            .feedback-form textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 1rem;
                color: #333;
            }

            .feedback-form input[type="submit"] {
                background: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);
                color: #fff;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                font-size: 1.1rem;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .feedback-form input[type="submit"]:hover {
                background: linear-gradient(375deg, #eb56ab, #da66c2, #c475d5, #ac83e2, #928fe9, #779bf0, #59a5f3, #3baef1, #00b9ee, #00c3e5, #00ccd9, #41d3ca);
            }

            /* Responsive design for smaller screens */
            @media (max-width: 768px) {
                .map-feedback-container {
                    flex-direction: column;
                }

                .map-section, .feedback-form {
                    width: 100%;
                }

                .map-section h2, .feedback-form h2 {
                    font-size: 1.5rem;
                }

                .feedback-form input[type="submit"] {
                    width: 100%;
                }
            }


        </style>

    </head>
    <body>

    <?php
    include 'main_header.php';
    ?>


    <section class="about-section">
        <h2>Rreth Fakultetit</h2>
        <p>
            Mirë se vini në Fakultetin e Teknologjisë së Informacionit, ku fokusohemi në ofrimin e një arsimi cilësor dhe promovimin e inovacionit. Jemi krenarë që diplomojmë studentë të shkëlqyer në fusha si Inxhinieria, Teknologjia dhe më shumë, me ndihmën e një stafi pedagogjik të njohur për lidershipin dhe kërkimet shkencore.
        </p>
        <p>
            Studentët tanë përfitojnë nga ambiente moderne, laboratorë të avancuar dhe një mjedis dinamik të të mësuarit. Jemi të përkushtuar të formojmë profesionistët dhe inovatorët e së ardhmes në një botë teknologjike gjithnjë në ndryshim.
        </p>
    </section>

    <section class="map-feedback-container">
        <!-- Map Section -->
        <div class="map-section">
            <h2>Vendndodhja</h2>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2996.661667959689!2d19.819129775530225!3d41.31622330040718!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x135030e26036f881%3A0xdf5dc3ad387e1db5!2sFaculty%20of%20Information%20Technology!5e0!3m2!1sit!2s!4v1726782388389!5m2!1sit!2s"
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>

        <!-- Feedback Form Section -->
        <div class="feedback-form">
            <h2>Feedback</h2>
            <form action="submit_feedback.php" method="post">
                <label for="username">Emri juaj:</label><br>
                <input type="text" id="username" name="username" placeholder="Shkruani emrin tuaj..." required><br><br>

                <label for="feedback">Jepni mendimin tuaj për përmirësime të mundshme në platformë:</label><br>
                <textarea id="feedback" name="feedback" rows="4" cols="50" placeholder="Shkruani mendimet tuaja këtu..." required></textarea><br><br>

                <input type="submit" value="Dërgo Feedback-un">
            </form>
        </div>

    </section>


    </main>

    <?php
    include "main_footer.php";
    ?>


</body>
</html>
