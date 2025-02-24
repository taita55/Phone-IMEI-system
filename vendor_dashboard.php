<?php
require_once 'config.php';
include 'header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

// Handle new phone registration
if (isset($_POST['add_phone'])) {
    $imei = $_POST['imei'];

    // 1. Check if phone IMEI is already blocked or stolen
    //    We'll do a quick check in the phones table if status is 'blocked' or 'stolen'.
    $checkStmt = $pdo->prepare("SELECT * FROM phones WHERE imei = :imei AND (status = 'blocked' OR status = 'stolen')");
    $checkStmt->execute([':imei' => $imei]);
    $blockedPhone = $checkStmt->fetch();

    if ($blockedPhone) {
        echo "<p style='color:red;'>This IMEI is blocked/stolen. Please call owner or take it to nearest Police station.</p>";
    } else {
        // 2. If not blocked, insert into database
        $vendor_id = $_SESSION['user']['user_id'];
        $insertStmt = $pdo->prepare("INSERT INTO phones (imei, vendor_id, status, date_inserted) 
                                     VALUES (:imei, :vendor_id, 'in_stock', NOW())");
        try {
            $insertStmt->execute([
                ':imei' => $imei,
                ':vendor_id' => $vendor_id
            ]);
            echo "<p style='color:green;'>Phone IMEI added to inventory successfully.</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}

// Handle marking phone as sold
if (isset($_POST['mark_sold'])) {
    $phone_id = $_POST['phone_id'];
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];

    // First, check if customer already exists or create them
    // For simplicity, we create a new customer user automatically or you can direct vendor to a "find existing customer" page.
    $username = strtolower($customer_name) . rand(1000, 9999); // simple unique username
    $default_password = password_hash('123456', PASSWORD_DEFAULT); // default password

    // Insert new user if doesn't exist:
    // We'll check if a user with same phone (or name) exists
    $checkCustomerStmt = $pdo->prepare("SELECT * FROM users WHERE phone = :phone AND role='customer'");
    $checkCustomerStmt->execute([':phone' => $customer_phone]);
    $existingCustomer = $checkCustomerStmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingCustomer) {
        // Create new customer
        $createCustomerStmt = $pdo->prepare("INSERT INTO users (name, phone, role, username, password) 
                                             VALUES (:name, :phone, 'customer', :username, :password)");
        $createCustomerStmt->execute([
            ':name' => $customer_name,
            ':phone' => $customer_phone,
            ':username' => $username,
            ':password' => $default_password
        ]);
        $customer_id = $pdo->lastInsertId();
    } else {
        $customer_id = $existingCustomer['user_id'];
    }

    // Update phone record
    $updateStmt = $pdo->prepare("UPDATE phones SET customer_id = :customer_id, status='sold', date_sold=NOW()
                                 WHERE phone_id = :phone_id AND vendor_id = :vendor_id");
    $updateStmt->execute([
        ':customer_id' => $customer_id,
        ':phone_id' => $phone_id,
        ':vendor_id' => $_SESSION['user']['user_id']
    ]);

    echo "<p style='color:green;'>Phone marked as sold to $customer_name.</p>";
}

// Display vendor inventory
$vendor_id = $_SESSION['user']['user_id'];
$query = "SELECT * FROM phones WHERE vendor_id = :vendor_id";
$params = [':vendor_id' => $vendor_id];

// Optional search
if (isset($_GET['search_imei']) && !empty($_GET['search_imei'])) {
    $query .= " AND imei LIKE :search_imei";
    $params[':search_imei'] = "%" . $_GET['search_imei'] . "%";
}
if (isset($_GET['status']) && $_GET['status'] != '') {
    $query .= " AND status = :status";
    $params[':status'] = $_GET['status'];
}

$query .= " ORDER BY date_inserted DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$phones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Vendor Dashboard</h2>

<!-- Add new phone form -->
<h3>Add New Phone to Inventory</h3>
<form method="POST" action="">
    <label>IMEI:</label><br>
    <input type="text" name="imei" required><br><br>
    <button type="submit" name="add_phone">Add Phone</button>
</form>
<hr>

<!-- Search and filter -->
<h3>Search Inventory</h3>
<form method="GET" action="">
    <label>Search by IMEI:</label>
    <input type="text" name="search_imei" value="<?= isset($_GET['search_imei']) ? $_GET['search_imei'] : '' ?>">
    <label>Status:</label>
    <select name="status">
        <option value="">All</option>
        <option value="in_stock" <?= (isset($_GET['status']) && $_GET['status'] == 'in_stock') ? 'selected' : '' ?>>In Stock</option>
        <option value="sold" <?= (isset($_GET['status']) && $_GET['status'] == 'sold') ? 'selected' : '' ?>>Sold</option>
        <option value="blocked" <?= (isset($_GET['status']) && $_GET['status'] == 'blocked') ? 'selected' : '' ?>>Blocked</option>
        <option value="stolen" <?= (isset($_GET['status']) && $_GET['status'] == 'stolen') ? 'selected' : '' ?>>Stolen</option>
    </select>
    <button type="submit">Search</button>
</form>

<!-- Display inventory -->
<h3>Your Phone Inventory</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>IMEI</th>
        <th>Status</th>
        <th>Date Inserted</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($phones as $p): ?>
    <tr>
        <td><?= $p['imei'] ?></td>
        <td><?= $p['status'] ?></td>
        <td><?= $p['date_inserted'] ?></td>
        <td>
            <?php if ($p['status'] == 'in_stock'): ?>
                <!-- Mark as sold form -->
                <form method="POST" style="display:inline;" action="">
                    <input type="hidden" name="phone_id" value="<?= $p['phone_id'] ?>">
                    <input type="text" name="customer_name" placeholder="Customer Name" required>
                    <input type="text" name="customer_phone" placeholder="Customer Phone" required>
                    <button type="submit" name="mark_sold">Mark Sold</button>
                </form>
            <?php else: ?>
                N/A
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
include 'footer.php';
?>
