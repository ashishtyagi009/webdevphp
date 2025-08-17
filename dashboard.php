<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?form=login");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle Create
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "create") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $content);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
    exit();
}

// Fetch ALL posts with usernames
$result = $conn->query("
    SELECT posts.id, posts.title, posts.content, posts.created_at, posts.user_id, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - My Blog</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="header">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <a href="logout.php">Logout</a>
</div>

<main>
    <!-- Create Post -->
    <h2>Create New Post</h2>
    <form method="POST" action="dashboard.php">
        <input type="text" name="title" placeholder="Post Title" required><br>
        <textarea name="content" placeholder="Write your content..." required></textarea><br>
        <input type="hidden" name="action" value="create">
        <button type="submit">Add Post</button>
    </form>

    <!-- Show All Posts -->
    <h2>All Posts</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="post">
            <h3>
                <a href="post.php?id=<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['title']); ?>
                </a>
            </h3>
            <p><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 100))); ?>...</p>
            <small>
                Posted by <b><?php echo htmlspecialchars($row['username']); ?></b> 
                on <?php echo $row['created_at']; ?>
            </small>
        </div>
        <hr>
    <?php endwhile; ?>
</main>

<div class="footer">
    <p>&copy; <?php echo date("Y"); ?> My Blog. All rights reserved.</p>
</div>
</body>
</html>
