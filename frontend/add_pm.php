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
    $stmt = $pdo->prepare("INSERT INTO users (email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, 'project_manager')");
    $stmt->execute([
        $_POST['email'],
        password_hash($_POST['password'] ?? 'password', PASSWORD_DEFAULT),
        $_POST['first_name'],
        $_POST['last_name']
    ]);
}
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
                            <h6 class='mb-4'>Add Project Manager</h6>
                            <form method='post'>
                                <div class='mb-3'>
                                    <label class='form-label'>First Name</label>
                                    <input type='text' class='form-control' name='first_name' required>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Last Name</label>
                                    <input type='text' class='form-control' name='last_name' required>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Email</label>
                                    <input type='email' class='form-control' name='email' required>
                                </div>
                                <button type='submit' name='add_pm' class='btn btn-primary'>Add Project Manager</button>
                            </form>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href='#' class='btn btn-lg btn-primary btn-lg-square back-to-top'><i class='bi bi-arrow-up'></i></a>
    </div>
<?php include 'includes/footer.php'; ?>
