<?php
// header.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>Phone IMEI System</title>
</head>
<body>
<nav>
    <!-- A simple navigation bar -->
    <?php if (isset($_SESSION['user'])): ?>
        <a href="dashboard.php">Dashboard</a> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>
<hr>
