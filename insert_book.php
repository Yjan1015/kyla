<?php
// Include necessary files
include('db.php');
include('config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $published_year = $_POST['published_year'];
    $description = $_POST['description'];

    // Validation
    if (empty($title) || empty($author) || empty($genre) || empty($published_year) || empty($description)) {
        die('Please fill in all fields.');
    }

    if (!is_numeric($published_year) || strlen($published_year) !== 4) {
        die('Invalid published year.');
    }

    // Insert data into the database
    $stmt = $pdo->prepare("INSERT INTO books (title, author, genre, published_year, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $author, $genre, $published_year, $description]);

    // Redirect to index page after insertion
    header('Location: index.php');
    exit();
}
?>
