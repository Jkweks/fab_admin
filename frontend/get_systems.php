<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
include 'includes/db.php';
$manufacturer = $_GET['manufacturer'] ?? '';
if ($manufacturer === '') {
    echo "<option value=''>Select Manufacturer First</option>";
    exit;
}
$stmt = $pdo->prepare('SELECT s.name FROM systems s JOIN manufacturers m ON s.manufacturer_id = m.id WHERE m.name = ? ORDER BY s.name');
$stmt->execute([$manufacturer]);
echo "<option value=''>Select System</option>";
foreach ($stmt as $row) {
    echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
}
?>
