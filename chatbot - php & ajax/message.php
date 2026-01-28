<?php

// Include the NLP and Calculator classes
include 'NLP.php';
include 'Calculator.php';

// Create instances of the NLP and Calculator classes
$documents = [
    "Sa kredite ka lenda 'Analizë Matematike 1'?",
    "Ku ndodhet Fakulteti i Teknologjisë dhe Informacionit?",
    "Cilat janë lëndët në Semestrin 1 të vitit të parë?"
];

$nlp = new NLP($documents);
$calculator = new Calculator();

// Connecting to the database
$conn = mysqli_connect("localhost", "root", "ba#83.!", "mybook_db") or die("Database Error");

// Getting user message through ajax
$getMesg = $_POST['text']; // Directly retrieve the input without urldecode

// Replace any potential spaces with `+`
$getMesg = str_replace(' ', '+', $getMesg);

// Decode URL-encoded input
$getMesg = urldecode($getMesg);

// Escape the input for database interaction
$getMesgEscaped = mysqli_real_escape_string($conn, $getMesg);

// Sanitize the input for HTML output
$getMesgSanitized = htmlspecialchars($getMesg, ENT_NOQUOTES, 'UTF-8');

// Retrieve all queries from the database
$check_data = "SELECT queries FROM chatbot";
$run_query = mysqli_query($conn, $check_data) or die("Error");
$databaseQueries = [];

while ($row = mysqli_fetch_assoc($run_query)) {
    $databaseQueries[] = $row['queries'];
}

// Using NLP algorithms for matching
$bestMatch = $nlp->correctTypos($getMesgSanitized, $databaseQueries);

if (empty($bestMatch)) {
    $bestMatch = $nlp->findSimilarPhrases($getMesgSanitized, $databaseQueries);
}

// Try using Cosine Similarity if no match found yet
if (empty($bestMatch)) {
    $bestMatch = $nlp->findSimilarPhrasesWithCosine($getMesgSanitized, $databaseQueries);
}

// Check for relevant documents using TF-IDF
if (empty($bestMatch)) {
    $relevantDocs = $nlp->getRelevantDocuments($getMesgSanitized);
    if (!empty($relevantDocs)) {
        // Take the first relevant document found
        $bestMatch = key($relevantDocs);
    }
}

// Check if the raw user input is a mathematical expression using the Calculator class method
if ($calculator->isMathematicalExpression($_POST['text'])) {
    $result = $calculator->calculate($_POST['text']);
    echo "Rezultati: " . htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
} else {
    // Check for the best match and retrieve the reply
    if (!empty($bestMatch)) {
        $check_data = "SELECT replies FROM chatbot WHERE queries LIKE '%" . mysqli_real_escape_string($conn, $bestMatch) . "%'";
        $run_query = mysqli_query($conn, $check_data) or die("Error");

        if (mysqli_num_rows($run_query) > 0) {
            $fetch_data = mysqli_fetch_assoc($run_query);
            $reply = $fetch_data['replies'];
            echo htmlspecialchars($reply, ENT_QUOTES, 'UTF-8');
        } else {
            echo "Me vjen keq por nuk mund te ju kuptoj!";
        }
    } else {
        echo "Me vjen keq por nuk mund te ju kuptoj!";
    }
}
?>
