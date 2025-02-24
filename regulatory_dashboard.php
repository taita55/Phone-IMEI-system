<?php
require_once 'config.php';
include 'header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'regulatory') {
    header("Location: login.php");
    exit;
}

// This is just a placeholder. In a real system, you'd have various audit queries.

echo "<h2>Regulatory Dashboard</h2>";

echo "<p>Here you can generate or view detailed reports:</p>";

// Example: Count phones by status
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM phones GROUP BY status");
$phoneCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Phones by Status</h3>";
echo "<table border='1' cellpadding='5'><tr><th>Status</th><th>Count</th></tr>";
foreach ($phoneCounts as $pc) {
    echo "<tr><td>{$pc['status']}</td><td>{$pc['count']}</td></tr>";
}
echo "</table>";

// Example: Stolen phones
$stolenStmt = $pdo->query("SELECT COUNT(*) as stolen_count FROM phones WHERE status = 'stolen'");
$stolenCount = $stolenStmt->fetch(PDO::FETCH_ASSOC)['stolen_count'];
echo "<p>Number of stolen phones: $stolenCount</p>";

// Example: Approved theft reports
$approvedTheftStmt = $pdo->query("SELECT COUNT(*) as approved_count FROM theft_reports WHERE approved_by_law = 1");
$approvedCount = $approvedTheftStmt->fetch(PDO::FETCH_ASSOC)['approved_count'];
echo "<p>Approved theft reports: $approvedCount</p>";

include 'footer.php';
?>
