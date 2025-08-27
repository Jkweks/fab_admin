<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['new_name'])) {
        $stmt = $pdo->prepare('INSERT INTO manufacturers (name) VALUES (?) ON CONFLICT (name) DO NOTHING');
        $stmt->execute([$_POST['new_name']]);
    }
    if (!empty($_POST['id']) && isset($_POST['name'])) {
        $stmt = $pdo->prepare('UPDATE manufacturers SET name = ? WHERE id = ?');
        $stmt->execute([$_POST['name'], $_POST['id']]);
    }
}

$manufacturers = $pdo->query('SELECT id, name FROM manufacturers ORDER BY name')->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<div class='container-xxl position-relative bg-white d-flex p-0'>
    <?php include 'includes/spinner.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class='content'>
        <?php include 'includes/navbar.php'; ?>
        <div class='container-fluid pt-4 px-4'>
            <div class='row g-4'>
                <div class='col-12'>
                    <div class='bg-light rounded h-100 p-4'>
                        <h6 class='mb-4'>Edit Manufacturers</h6>
                        <form method='post' class='mb-3'>
                            <div class='input-group'>
                                <input type='text' class='form-control' name='new_name' placeholder='New Manufacturer'>
                                <button class='btn btn-primary' type='submit'>Add</button>
                            </div>
                        </form>
                        <?php foreach ($manufacturers as $m): ?>
                            <form method='post' class='d-flex mb-2'>
                                <input type='hidden' name='id' value='<?php echo htmlspecialchars($m['id']); ?>'>
                                <input type='text' class='form-control me-2' name='name' value='<?php echo htmlspecialchars($m['name']); ?>'>
                                <button class='btn btn-secondary' type='submit'>Update</button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class='container-fluid pt-4 px-4'>
            <div class='bg-light rounded-top p-4'>
                <div class='row'>
                    <div class='col-12 col-sm-6 text-center text-sm-start'>
                        &copy; <a href='#'>Your Site Name</a>, All Right Reserved.
                    </div>
                    <div class='col-12 col-sm-6 text-center text-sm-end'>
                        Designed By <a href='https://htmlcodex.com'>HTML Codex</a><br>
                        Distributed By <a class='border-bottom' href='https://themewagon.com' target='_blank'>ThemeWagon</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href='#' class='btn btn-lg btn-primary btn-lg-square back-to-top'><i class='bi bi-arrow-up'></i></a>
</div>
<?php include 'includes/footer.php'; ?>
