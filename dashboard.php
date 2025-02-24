<?php
require_once 'config.php';
include 'header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_role = $_SESSION['user']['role'];

echo "<h2>Welcome, " . $_SESSION['user']['name'] . "!</h2>";
echo "<p>Your role is: <strong>$user_role</strong></p>";

switch ($user_role) {
    case 'vendor':
        echo "<a href='vendor_dashboard.php'>Go to Vendor Dashboard</a>";
        break;
    case 'customer':
        echo "<a href='customer_dashboard.php'>Go to Customer Dashboard</a>";
        break;
    case 'law_enforcement':
        echo "<a href='law_enforcement_dashboard.php'>Go to Law Enforcement Dashboard</a>";
        break;
    case 'telecom':
        echo "<a href='telecom_dashboard.php'>Go to Telecom Dashboard</a>";
        break;
    case 'regulatory':
        echo "<a href='regulatory_dashboard.php'>Go to Regulatory Dashboard</a>";
        break;
    default:
        echo "<p>Unknown role.</p>";
        break;
}

include 'footer.php';
