<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
if (!isset($_SESSION['original_role'])) {
    $_SESSION['original_role'] = $_SESSION['role'] ?? '';
}
if (($_SESSION['original_role'] ?? '') !== 'admin' && empty($_SESSION['is_dev'])) {
    header('Location: index.php');
    exit;
}
$role = $_POST['role'] ?? 'admin';
$_SESSION['role'] = $role;
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: ' . $redirect);
exit;
?>
