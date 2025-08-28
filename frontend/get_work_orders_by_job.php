<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
include 'includes/db.php';
$job_id = $_GET['job_id'] ?? '';
header('Content-Type: application/json');
if (!$job_id) {
    echo json_encode([]);
    exit;
}
$stmt = $pdo->prepare("SELECT id, work_order_number FROM work_orders WHERE job_id = ? AND status != 'completed' ORDER BY work_order_number");
$stmt->execute([$job_id]);
echo json_encode($stmt->fetchAll());
?>
