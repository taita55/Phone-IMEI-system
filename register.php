<?php
require_once 'config.php';
include 'header.php';

// If form submitted, process registration
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $role = $_POST['role']; // vendor, customer, law_enforcement, telecom, regulatory
    $rdb_certificate = $_POST['rdb_certificate'] ?? null; 
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert into users table
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, phone, role, rdb_certificate, username, password) 
                               VALUES (:name, :phone, :role, :rdb_certificate, :username, :password)");
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':role' => $role,
            ':rdb_certificate' => $rdb_certificate,
            ':username' => $username,
            ':password' => $password
        ]);
        echo "<p>Registration successful. <a href='login.php'>Login</a> now.</p>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<h2>Register</h2>
<form method="POST" action="">
    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Phone:</label><br>
    <input type="text" name="phone" required><br>

    <label>Role:</label><br>
    <select name="role" required>
        <option value="vendor">Vendor</option>
        <option value="customer">Customer</option>
        <option value="law_enforcement">Law Enforcement</option>
        <option value="telecom">Telecom</option>
        <option value="regulatory">Regulatory</option>
    </select><br>

    <label>RDB Certificate (only if vendor):</label><br>
    <input type="text" name="rdb_certificate"><br>

    <label>Username:</label><br>
    <input type="text" name="username" required><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="register">Register</button>
</form>

<?php include 'footer.php'; ?>
