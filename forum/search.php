<?php
global $con;
require('config.inc.php');
require('functions.php');

// Check if $con is available
if (!$con) {
    die("Database connection failed.");
}

// Initialize variables to pass to the HTML section
$results = [];
$error = '';
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (!empty($query)) {
    $query = mysqli_real_escape_string($con, $query);

    // Split query into terms
    $terms = explode(' ', $query);

    // Retrieve all posts from the database
    $sql = "SELECT id, post FROM posts"; // We are only retrieving `id` and `title`
    $result = mysqli_query($con, $sql);
    $documents = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $documents[] = $row;
    }

    $numDocs = count($documents);
    $idf = [];

    // Calculate IDF for each term
    foreach ($terms as $term) {
        $term = mysqli_real_escape_string($con, $term);
        $idf_sql = "SELECT COUNT(*) as doc_count FROM posts WHERE post LIKE '%$term%' OR post LIKE '%$term%'";
        $idf_result = mysqli_query($con, $idf_sql);
        $idf_row = mysqli_fetch_assoc($idf_result);
        $idf[$term] = log($numDocs / ($idf_row['doc_count'] + 1)); // +1 to prevent division by zero
    }

    // Calculate TF-IDF scores and collect posts with score > 0
    foreach ($documents as $doc) {
        $docId = $doc['id'];
        $title = $doc['post'];
        $postTerms = explode(' ', $title);
        $tf = [];

        foreach ($postTerms as $postTerm) {
            $postTerm = mysqli_real_escape_string($con, $postTerm);
            if (!isset($tf[$postTerm])) {
                $tf[$postTerm] = 0;
            }
            $tf[$postTerm]++;
        }

        $tfidf = 0;

        foreach ($terms as $term) {
            $term = mysqli_real_escape_string($con, $term);
            $tfidf += ($tf[$term] ?? 0) * ($idf[$term] ?? 0);
        }

        if ($tfidf > 0) {
            $results[] = [
                'id' => $doc['id'],
                'post' => $doc['post'],
                'score' => $tfidf
            ];
        }
    }
} else {
    $error = 'Please enter a search query.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        /* Global styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f4f7fc;
            color: #333;
            font-size: 16px;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .search-box {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-box input[type="text"] {
            width: 400px; /* Set the width of the search bar */
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px; /* Rounded corners on the left */
            font-size: 16px;
            border-right: none; /* Remove right border to merge with button */
        }

        .search-box button {
            width: 120px; /* Set a fixed width for the button */
            padding: 10px;
            border: none;
            background-color: #3498db;
            color: white;
            font-size: 16px;
            border-radius: 0 4px 4px 0; /* Rounded corners on the right */
            cursor: pointer;
        }

        .search-box button:hover {
            background-color: #2980b9;
        }



        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        ul li a {
            text-decoration: none;
            color: #3498db;
            font-size: 18px;
        }

        ul li:hover {
            background-color: #f1f1f1;
        }

        ul li a:hover {
            text-decoration: underline;
        }

        /* No results or error message */
        p {
            text-align: center;
            font-size: 18px;
            color: #888;
        }

        a {
            color: #3498db;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Search Results:</h2>

    <div class="search-box">
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search for posts..." value="<?= htmlspecialchars($query) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php if (!empty($query)): ?>
        <?php if (count($results) > 0): ?>
            <ul>
                <?php foreach ($results as $result): ?>
                    <li><a href="post.php?id=<?= htmlspecialchars($result['id']) ?>"><?= htmlspecialchars($result['post']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No results found. <a href="index.php">Start a new post</a>.</p>
        <?php endif; ?>
    <?php else: ?>
        <p><?= $error ?></p>
    <?php endif; ?>
</div>
</body>
</html>
