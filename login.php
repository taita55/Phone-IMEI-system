<?php
require_once 'config.php';
include 'header.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Store user info in session
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<p>Invalid username or password.</p>";
    }
}
?>


<h2>Login</h2>
<form method="POST" action="">
    <label>Username:</label><br>
    <input type="text" name="username" required><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>
<p><a href="forgot_password.php">Forgot Password?</a></p>

<?php include 'footer.php'; ?>
