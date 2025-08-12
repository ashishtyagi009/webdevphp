<?php
// index.php

// Just a small PHP script
$title = "My PHP Page";
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : "Guest";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
</head>
<body>
    <h1>Welcome, <?php echo $name; ?>!</h1>
    <p>This is the index page of my PHP site.</p>
    <p>Today is <?php echo date("l, F j, Y"); ?>.</p>
</body>
</html>
