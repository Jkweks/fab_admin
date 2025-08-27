<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
include 'includes/db.php';

$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM work_orders WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: jobs.php');
exit;
?>

