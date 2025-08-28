<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
$role = $_POST['role'] ?? 'admin';
$_SESSION['role'] = $role;
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: ' . $redirect);
exit;
?>
