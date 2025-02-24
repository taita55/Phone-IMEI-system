<?php
require_once 'config.php';

if (!isset($_GET['token'])) {
    echo "Invalid password reset link.";
    exit;
}

$token = $_GET['token'];

// Retrieve the reset request data (join with users table to get the username if desired)
$stmt = $pdo->prepare("SELECT pr.user_id, pr.expires_at, u.username FROM password_resets pr JOIN users u ON pr.user_id = u.user_id WHERE token = :token");
$stmt->execute([':token' => $token]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Invalid or expired token.";
    exit;
}

if (strtotime($data['expires_at']) < time()) {
    echo "This password reset link has expired.";
    exit;
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Hash the new password
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update the user's password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
        $stmt->execute([':password' => $password_hash, ':user_id' => $data['user_id']]);
        
        // Remove the token after successful password reset
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute([':token' => $token]);
        
        echo "<p>Password has been reset successfully. You can now <a href='login.php'>login</a>.</p>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        form { margin-top: 20px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Reset Password for <?= htmlspecialchars($data['username']) ?></h2>
    <?php if (isset($error)) { echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; } ?>
    <form method="POST" action="">
        <label for="new_password">New Password:</label><br>
        <input type="password" name="new_password" id="new_password" required><br><br>
        
        <label for="confirm_password">Confirm New Password:</label><br>
        <input type="password" name="confirm_password" id="confirm_password" required><br><br>
        
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
