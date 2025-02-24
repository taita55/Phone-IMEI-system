<?php
require_once 'config.php';
include 'header.php';

// Only allow telecom role access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'telecom') {
    header("Location: login.php");
    exit;
}

// --- BLOCK/UNBLOCK FUNCTIONS ---

// Block phone
if (isset($_POST['block_phone'])) {
    $report_id = $_POST['report_id'];
    $phone_id = $_POST['phone_id'];
    
    // Update theft_reports to mark as blocked
    $stmt = $pdo->prepare("UPDATE theft_reports SET blocked_by_telecom = 1, block_date = NOW() 
                           WHERE report_id = :report_id");
    $stmt->execute([':report_id' => $report_id]);
    
    // Update phones table to change status to 'blocked'
    $stmt2 = $pdo->prepare("UPDATE phones SET status = 'blocked' WHERE phone_id = :phone_id");
    $stmt2->execute([':phone_id' => $phone_id]);
    
    echo "<p>Phone blocked successfully.</p>";
}

// Unblock phone
if (isset($_POST['unblock_phone'])) {
    $report_id = $_POST['report_id'];
    $phone_id = $_POST['phone_id'];
    
    // Update theft_reports to mark as unblocked
    $stmt = $pdo->prepare("UPDATE theft_reports SET unblocked_by_telecom = 1, unblock_date = NOW() 
                           WHERE report_id = :report_id");
    $stmt->execute([':report_id' => $report_id]);
    
    // Update phones table to revert status back to 'sold'
    $stmt2 = $pdo->prepare("UPDATE phones SET status = 'sold' WHERE phone_id = :phone_id");
    $stmt2->execute([':phone_id' => $phone_id]);
    
    echo "<p>Phone unblocked successfully.</p>";
}

// --- FETCH DATA FOR DISPLAY ---

// Retrieve approved theft reports (approved by law enforcement)
$stmt = $pdo->prepare("SELECT tr.*, p.imei, u.name AS customer_name, u.phone AS customer_phone
                       FROM theft_reports tr
                       JOIN phones p ON tr.phone_id = p.phone_id
                       JOIN users u ON tr.reported_by = u.user_id
                       WHERE tr.approved_by_law = 1
                       ORDER BY tr.report_date DESC");
$stmt->execute();
$theftReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve SIM activations (phones with sim_activated = 1)
$simStmt = $pdo->prepare("SELECT p.*, u.name AS customer_name, u.phone AS customer_phone
                          FROM phones p
                          JOIN users u ON p.customer_id = u.user_id
                          WHERE p.sim_activated = 1
                          ORDER BY p.date_sold DESC");
$simStmt->execute();
$activatedPhones = $simStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Telecom Dashboard</h2>

<!-- Approved Theft Reports -->
<h3>Approved Theft Reports (Ready to Block)</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>IMEI</th>
        <th>Reported By (Customer)</th>
        <th>Blocked By Telecom</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($theftReports as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['imei']) ?></td>
        <td><?= htmlspecialchars($r['customer_name']) ?> (<?= htmlspecialchars($r['customer_phone']) ?>)</td>
        <td><?= $r['blocked_by_telecom'] ? 'Yes' : 'No' ?></td>
        <td>
            <?php if (!$r['blocked_by_telecom']): ?>
                <form method="POST" action="">
                    <input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
                    <input type="hidden" name="phone_id" value="<?= $r['phone_id'] ?>">
                    <button type="submit" name="block_phone">Block IMEI</button>
                </form>
            <?php else: ?>
                <?php if (!$r['unblocked_by_telecom']): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
                        <input type="hidden" name="phone_id" value="<?= $r['phone_id'] ?>">
                        <button type="submit" name="unblock_phone">Unblock IMEI</button>
                    </form>
                <?php else: ?>
                    Unblocked on <?= htmlspecialchars($r['unblock_date']) ?>
                <?php endif; ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- SIM Activations -->
<h3>SIM Activations</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>IMEI</th>
        <th>Customer</th>
        <th>Date Sold</th>
    </tr>
    <?php foreach ($activatedPhones as $ap): ?>
    <tr>
        <td><?= htmlspecialchars($ap['imei']) ?></td>
        <td><?= htmlspecialchars($ap['customer_name']) ?> (<?= htmlspecialchars($ap['customer_phone']) ?>)</td>
        <td><?= htmlspecialchars($ap['date_sold']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Simulated Triangulation Section -->
<h3>Simulated Triangulation (Cell Tower Tracking)</h3>

<?php
// Call the Python simulation script
// Make sure the path to the script is correct on your server
$pythonScriptPath = ''; // Update this path if needed
$triangulationOutput = shell_exec("python3 " . escapeshellarg($pythonScriptPath));

// Display the output from the Python script
echo "<pre>" . htmlspecialchars($triangulationOutput) . "</pre>";
?>

<!-- New Button to Open the Locate Page -->
<button type="button" onclick="window.open('locat.php', '_blank')">Locate Phone IMEI</button>

<?php include 'footer.php'; ?>
