<?php
require_once 'config.php';
include 'header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

// Handle SIM activation
if (isset($_POST['activate_sim'])) {
    $phone_id = $_POST['phone_id'];
    // Update phone sim_activated
    $stmt = $pdo->prepare("UPDATE phones SET sim_activated = 1 WHERE phone_id = :phone_id AND customer_id = :customer_id");
    $stmt->execute([':phone_id' => $phone_id, ':customer_id' => $user_id]);
    echo "<p style='color:green;'>SIM activated successfully.</p>";
}

// Handle report stolen
if (isset($_POST['report_stolen'])) {
    $phone_id = $_POST['phone_id'];
    // Insert into theft_reports
    $insertStmt = $pdo->prepare("INSERT INTO theft_reports (phone_id, reported_by) VALUES (:phone_id, :reported_by)");
    $insertStmt->execute([
        ':phone_id' => $phone_id,
        ':reported_by' => $user_id
    ]);
    // Update phone status
    $updatePhone = $pdo->prepare("UPDATE phones SET status = 'stolen' WHERE phone_id = :phone_id AND customer_id = :customer_id");
    $updatePhone->execute([
        ':phone_id' => $phone_id,
        ':customer_id' => $user_id
    ]);
    echo "<p style='color:red;'>Phone reported as stolen.</p>";
}

// Fetch all phones for this customer
$stmt = $pdo->prepare("SELECT * FROM phones WHERE customer_id = :customer_id");
$stmt->execute([':customer_id' => $user_id]);
$phones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Customer Dashboard</h2>
<h3>Your Phones</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>IMEI</th>
        <th>Status</th>
        <th>SIM Activated</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($phones as $phone): ?>
    <tr>
        <td><?= $phone['imei'] ?></td>
        <td><?= $phone['status'] ?></td>
        <td><?= $phone['sim_activated'] ? 'Yes' : 'No' ?></td>
        <td>
            <?php if (!$phone['sim_activated'] && $phone['status'] == 'sold'): ?>
                <!-- Activate SIM form -->
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="phone_id" value="<?= $phone['phone_id'] ?>">
                    <button type="submit" name="activate_sim">Activate SIM</button>
                </form>
            <?php endif; ?>

            <?php if ($phone['status'] != 'stolen'): ?>
                <!-- Report stolen form -->
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="phone_id" value="<?= $phone['phone_id'] ?>">
                    <button type="submit" name="report_stolen" style="background-color:red; color:white;">
                        Report Stolen
                    </button>
                </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include 'footer.php'; ?>
