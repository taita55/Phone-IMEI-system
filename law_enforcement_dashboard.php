<?php
require_once 'config.php';
include 'header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'law_enforcement') {
    header("Location: login.php");
    exit;
}

// Approve theft
if (isset($_POST['approve_theft'])) {
    $report_id = $_POST['report_id'];
    $stmt = $pdo->prepare("UPDATE theft_reports SET approved_by_law = 1, law_approval_date = NOW() 
                           WHERE report_id = :report_id");
    $stmt->execute([':report_id' => $report_id]);
    echo "<p>Theft report approved.</p>";
}

// Get all theft reports
$query = "SELECT tr.*, p.imei, p.vendor_id, p.customer_id, u.name as reporter_name, u.phone as reporter_phone
          FROM theft_reports tr
          JOIN phones p ON tr.phone_id = p.phone_id
          JOIN users u ON tr.reported_by = u.user_id
          ORDER BY tr.report_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to get user info
function getUserInfo($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Law Enforcement Dashboard</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>IMEI</th>
        <th>Reported By</th>
        <th>Vendor Info</th>
        <th>Report Date</th>
        <th>Approved By Law</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($reports as $r): 
        $vendorInfo = $r['vendor_id'] ? getUserInfo($pdo, $r['vendor_id']) : null;
        $customerInfo = $r['customer_id'] ? getUserInfo($pdo, $r['customer_id']) : null;
    ?>
    <tr>
        <td><?= $r['imei'] ?></td>
        <td>
            <?= $r['reporter_name'] ?> (<?= $r['reporter_phone'] ?>)
        </td>
        <td>
            <?php if ($vendorInfo): ?>
                <?= $vendorInfo['name'] ?> (<?= $vendorInfo['phone'] ?>)
            <?php else: ?>
                Unknown Vendor
            <?php endif; ?>
        </td>
        <td><?= $r['report_date'] ?></td>
        <td><?= $r['approved_by_law'] ? 'Yes' : 'No' ?></td>
        <td>
            <?php if (!$r['approved_by_law']): ?>
                <form method="POST" action="">
                    <input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
                    <button type="submit" name="approve_theft">Approve Theft</button>
                </form>
            <?php else: ?>
                Approved on <?= $r['law_approval_date'] ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include 'footer.php'; ?>
