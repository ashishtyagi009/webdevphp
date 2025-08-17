<?php
// logout.php
session_start();

// Destroy all session data
$_SESSION = [];
session_destroy();

// Redirect to auth.php
header("Location: index.php");
exit();
?>
