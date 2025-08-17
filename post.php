<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$post_id = intval($_GET['id']);

// Fetch post
$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = $post_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Post not found!";
    exit();
}

$post = $result->fetch_assoc();


// Handle Delete
if (isset($_POST['delete']) && $post['user_id'] == $user_id) {
    $delete_sql = "DELETE FROM posts WHERE id = $post_id AND user_id = $user_id";
    if ($conn->query($delete_sql)) {
        header("Location: dashboard.php?msg=Post deleted successfully");
        exit();
    } else {
        echo "Error deleting post: " . $conn->error;
    }
}

// Handle Edit (Update)
if (isset($_POST['update']) && $post['user_id'] == $user_id) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $update_sql = "UPDATE posts SET title='$title', content='$content' WHERE id=$post_id AND user_id=$user_id";
    if ($conn->query($update_sql)) {
        header("Location: post.php?id=$post_id&msg=Post updated successfully");
        exit();
    } else {
        echo "Error updating post: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="post.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        header {
            background: #2c3e50;
            padding: 15px 20px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h2 {
            margin: 0;
        }
        header nav a {
            color: #fff;
            margin-left: 15px;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            width: 70%;
            margin: 20px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .post-title {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .post-meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .post-content {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .actions button {
            background: #3498db;
            color: #fff;
            border: none;
            padding: 8px 14px;
            margin-right: 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
        }
        .actions button.delete {
            background: #e74c3c;
        }
        .actions button:hover {
            opacity: 0.9;
        }
        .edit-form {
            margin-top: 20px;
            display: none; /* Hidden until Edit is clicked */
        }
        .edit-form input, 
        .edit-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .edit-form button {
            padding: 10px 16px;
        }
    </style>
    <script>
        function toggleEditForm() {
            const form = document.getElementById("editForm");
            form.style.display = (form.style.display === "none") ? "block" : "none";
        }
    </script>
</head>
<body>
    <header>
        <h2>Post Details</h2>
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h2 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
        <p class="post-meta"><strong>By:</strong> <?php echo htmlspecialchars($post['username']); ?> | <small><?php echo $post['created_at']; ?></small></p>
        <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

        <?php if ($post['user_id'] == $user_id): ?>
            <div class="actions">
                <button onclick="toggleEditForm()">✏️ Edit</button>
                <form method="post" style="display:inline;">
                    <button type="submit" name="delete" class="delete" onclick="return confirm('Delete this post?');">🗑️ Delete</button>
                </form>
            </div>

            <!-- Hidden Edit Form -->
            <form method="post" class="edit-form" id="editForm">
                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                <textarea name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                <button type="submit" name="update">💾 Save Changes</button>
                <button type="button" onclick="toggleEditForm()">❌ Cancel</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
