<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    
    // Check if a user exists with that username
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Generate a secure token and set expiry (1 hour)
        $token = bin2hex(random_bytes(16));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));
        
        // Delete any previous tokens for this user
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user['user_id']]);
        
        // Insert the new token into the password_resets table
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
        $stmt->execute([':user_id' => $user['user_id'], ':token' => $token, ':expires_at' => $expires_at]);
        
        // Normally, you'd email the reset link. For demonstration, we display it.
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
        $message = "A password reset link has been generated. Click the link below to reset your password:\n\n" . $reset_link;
        echo "<p>" . nl2br(htmlspecialchars($message)) . "</p>";
    } else {
        echo "<p>No user found with that username.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        form { margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Forgot Password</h2>
    <form method="POST" action="">
        <label for="username">Enter your username:</label><br>
        <input type="text" name="username" id="username" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
    <p><a href="login.php">Back to Login</a></p>
</body>
</html>
