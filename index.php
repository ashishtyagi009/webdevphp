<?php
session_start();
include 'db.php';

$error = "";
$success = "";

// Decide which form to show (login or register)
$formType = isset($_GET['form']) && $_GET['form'] === "register" ? "register" : "login";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] == "register") {
        // Registration
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            $success = "Registration successful! You can now login.";
            header("Location: index.php?form=login");
            exit();
        } else {
            $error = "Username already exists!";
        }
        $stmt->close();
    } elseif ($_POST['action'] == "login") {
        // Login
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "User not found!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo ucfirst($formType); ?> - Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
    <h1>My Blog</h1>
</div>

<main>

<div class="form-container">
    <h2><?php echo ucfirst($formType); ?></h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>

    <form method="POST" action="index.php?form=<?php echo $formType; ?>">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="hidden" name="action" value="<?php echo $formType; ?>">
        <button type="submit"><?php echo ucfirst($formType); ?></button>
    </form>

    <p>
        <?php if ($formType == "login"): ?>
            Don’t have an account? <a href="index.php?form=register">Register</a>
        <?php else: ?>
            Already have an account? <a href="index.php?form=login">Login</a>
        <?php endif; ?>
    </p>
    </div>
</main>
    <div class="footer">
    <p>&copy; <?php echo date("Y"); ?> My Blog. All rights reserved.</p>
</div>
</body>
</html>
