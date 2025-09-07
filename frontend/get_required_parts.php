<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
include 'includes/db.php';
$part_id = $_GET['id'] ?? '';
if ($part_id === '') {
    http_response_code(400);
    echo json_encode(['error' => 'id required']);
    exit;
}
header('Content-Type: application/json');
$stmt = $pdo->prepare("SELECT r.required_part_id AS id, r.quantity, dp.part_number, dp.manufacturer, dp.system, dp.category FROM door_part_requirements r JOIN door_parts dp ON r.required_part_id = dp.id WHERE r.part_id = ?");
$stmt->execute([$part_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
?>
