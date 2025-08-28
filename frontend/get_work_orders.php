<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
include 'includes/db.php';
$job_id = $_GET['job_id'] ?? '';
if (!$job_id) {
    echo 'No job selected.';
    exit;
}
$wo_stmt = $pdo->prepare("SELECT id, work_order_number, material_delivery_date, pull_from_stock, delivered, status FROM work_orders WHERE job_id = ? ORDER BY work_order_number");
$wo_stmt->execute([$job_id]);
$work_orders = $wo_stmt->fetchAll();
foreach ($work_orders as $wo) {
    echo "<div class='mb-3'>";
    echo "<h6>Work Order " . htmlspecialchars($wo['work_order_number'] ?? '') . "</h6>";
    echo '<p>Status: ' . htmlspecialchars($wo['status'] ?? '') . '</p>';
    if ($wo['pull_from_stock']) {
        echo '<p>Pull from stock</p>';
    } elseif ($wo['delivered']) {
        echo '<p>Delivered</p>';
    } elseif ($wo['material_delivery_date']) {
        echo '<p>Material Delivery: ' . htmlspecialchars($wo['material_delivery_date'] ?? '') . '</p>';
    }

    $item_stmt = $pdo->prepare("SELECT item_type, elevation, quantity, scope, comments, date_required, date_completed, CONCAT(u.first_name, ' ', u.last_name) AS completed_by_name FROM work_order_items LEFT JOIN users u ON work_order_items.completed_by = u.id WHERE work_order_id = ? ORDER BY work_order_items.id");
    $item_stmt->execute([$wo['id']]);
    $items = $item_stmt->fetchAll();
    if ($items) {
        echo "<table class='table'><thead><tr><th>Type</th><th>Elevation</th><th>Qty</th><th>Scope</th><th>Comments</th><th>Date Required</th><th>Date Completed</th><th>Completed By</th></tr></thead><tbody>";
        foreach ($items as $it) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($it['item_type'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($it['elevation'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($it['quantity'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($it['scope'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($it['comments'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($it['date_required'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($it['date_completed'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($it['completed_by_name'] ?? '') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No line items.</p>';
    }
    echo "<a href='edit_work_order.php?id=" . urlencode($wo['id']) . "' class='btn btn-sm btn-secondary me-2'>Edit</a>";
    echo "<a href='delete_work_order.php?id=" . urlencode($wo['id']) . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Delete work order?');\">Delete</a>";
    echo '</div>';
}
echo "<a href='add_work_order.php?job_id=" . urlencode($job_id) . "' class='btn btn-primary me-2'>Add Work Order</a>";
echo "<a href='door_configurations_by_job.php?job_id=" . urlencode($job_id) . "' class='btn btn-secondary'>Door Configurations</a>";
?>

