<?php
include('db.php');
include('config.php');
require_once 'vendor/autoload.php';

$faker = Faker\Factory::create();

// Add 50 Fake Books (only if button is clicked)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fake_books'])) {
    for ($i = 0; $i < 50; $i++) {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, genre, published_year, description) 
                               VALUES (:title, :author, :genre, :published_year, :description)");
        $stmt->execute([
            ':title' => $faker->sentence(3),
            ':author' => $faker->name,
            ':genre' => $faker->word,
            ':published_year' => $faker->year,
            ':description' => $faker->paragraph
        ]);
    }
    header("Location: index.php");
    exit;
}

// Update Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE books SET title = :title, author = :author, genre = :genre, 
                           published_year = :published_year, description = :description WHERE id = :id");
    $stmt->execute([
        ':title' => $_POST['title'],
        ':author' => $_POST['author'],
        ':genre' => $_POST['genre'],
        ':published_year' => $_POST['published_year'],
        ':description' => $_POST['description'],
        ':id' => $_POST['id']
    ]);
    header("Location: index.php");
    exit;
}

// Delete Book
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    header("Location: index.php");
    exit;
}

// Fetch a single book for editing (if edit request exists)
$editBook = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $editBook = $stmt->fetch();
}

// Pagination settings
$booksPerPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $booksPerPage;

// Fetch books from the database with pagination
$stmt = $pdo->prepare("SELECT * FROM books ORDER BY id OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $booksPerPage, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of books
$totalBooksStmt = $pdo->query("SELECT COUNT(*) FROM books");
$totalBooks = $totalBooksStmt->fetchColumn();

// Calculate total pages
$totalPages = ceil($totalBooks / $booksPerPage);

// Determine the range of pages to display
$range = 2; // Show 2 pages before and after the current page
$start = max(1, $page - $range);
$end = min($totalPages, $page + $range);

$backgroundImage = isset($_GET['bg_image']) ? $_GET['bg_image'] : 'image1.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('<?php echo $backgroundImage; ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100%;
            position: relative;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 105, 180, 0.3);
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            padding-top: 30px;
            color: #fff;
        }

        table {
            background-color: rgba(255, 255, 255, 0.8);
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            color: #222;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        table th {
            background-color: rgba(255, 105, 180, 0.9);
            color: black;
            font-weight: bold;
            text-transform: uppercase;
        }

        table tr:hover {
            background-color: rgba(255, 105, 180, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #ff69b4;
            border-color: #ff69b4;
        }

        .pagination .page-link {
            color: #ff69b4;
        }

        .pagination .page-link:hover {
            background-color: rgba(255, 105, 180, 0.2);
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container my-5">
        <h1>Books Collection</h1>

        <!-- Add Fake Books Button -->
        <form method="POST">
            <button type="submit" name="add_fake_books" class="btn btn-primary mb-4">Add 50 Fake Books</button>
        </form>

        <!-- Update Book Form -->
        <?php if ($editBook): ?>
            <h3>Edit Book</h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editBook['id']); ?>">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($editBook['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($editBook['author']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Genre</label>
                    <input type="text" name="genre" class="form-control" value="<?php echo htmlspecialchars($editBook['genre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Published Year</label>
                    <input type="number" name="published_year" class="form-control" value="<?php echo htmlspecialchars($editBook['published_year']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($editBook['description']); ?></textarea>
                </div>
                <button type="submit" name="update" class="btn btn-success">Update Book</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
            <hr>
        <?php endif; ?>

        <!-- Books Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Published Year</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['id']); ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['genre']); ?></td>
                        <td><?php echo htmlspecialchars($book['published_year']); ?></td>
                        <td><?php echo htmlspecialchars($book['description']); ?></td>
                        <td>
                            <a href="index.php?edit=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="index.php?delete=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page == $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
