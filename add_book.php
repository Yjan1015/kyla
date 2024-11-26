<?php
// Include necessary files
include('db.php');
include('config.php');

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $published_year = $_POST['published_year'];
    $description = $_POST['description'];

    // Check if all required fields are filled
    if (empty($title) || empty($author) || empty($genre) || empty($published_year) || empty($description)) {
        $error = "All fields are required!";
    } else {
        // Insert the new book into the database
        $stmt = $pdo->prepare("INSERT INTO books (title, author, genre, published_year, description) 
                               VALUES (:title, :author, :genre, :published_year, :description)");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':published_year', $published_year);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            $success = "New book added successfully!";
        } else {
            $error = "Error adding book.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <!-- Include Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Background and Pink Overlay */
        body {
            background-image: url('https://via.placeholder.com/1920x1080'); /* Replace with your image URL */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 105, 180, 0.5); /* Pink overlay with transparency */
        }

        .container {
            position: relative;
            z-index: 2;
            padding-top: 30px;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="overlay"></div> <!-- Pink overlay -->
    <div class="container my-5">
        <h1>Add New Book</h1>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <?php if (isset($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <form method="POST" action="add_book.php">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <input type="text" class="form-control" id="genre" name="genre" required>
            </div>
            <div class="mb-3">
                <label for="published_year" class="form-label">Published Year</label>
                <input type="number" class="form-control" id="published_year" name="published_year" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
