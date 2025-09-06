<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
include 'includes/db.php';

$manufacturer = $_GET['manufacturer'] ?? '';
$system = $_GET['system'] ?? '';
if ($manufacturer === '' || $system === '') {
    http_response_code(400);
    echo json_encode(['error' => 'manufacturer and system required']);
    exit;
}

header('Content-Type: application/json');

$stmt = $pdo->prepare(
    "SELECT sdp.glazing_thickness, sdp.function, dp.part_number, dp.manufacturer, dp.system
     FROM system_default_parts sdp
     JOIN systems s ON sdp.system_id = s.id
     JOIN manufacturers m ON s.manufacturer_id = m.id
     LEFT JOIN door_parts dp ON sdp.part_id = dp.id
     WHERE s.name = ? AND m.name = ?
     ORDER BY sdp.glazing_thickness, sdp.function"
);
$stmt->execute([$system, $manufacturer]);

$results = [];
foreach ($stmt as $row) {
    $thickness = $row['glazing_thickness'];
    if (!isset($results[$thickness])) {
        $results[$thickness] = [];
    }
    $results[$thickness][] = [
        'function' => $row['function'],
        'part_number' => $row['part_number'],
        'manufacturer' => $row['manufacturer'],
        'system' => $row['system'],
    ];
}

echo json_encode($results);
?>

