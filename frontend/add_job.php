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
    $stmt = $pdo->prepare("INSERT INTO jobs (job_name, job_number, project_manager) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['job_name'],
        $_POST['job_number'],
        $_POST['project_manager']
    ]);
}

$pm_stmt = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role = 'project_manager' ORDER BY first_name");
$project_managers = $pm_stmt->fetchAll();
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
                            <h6 class='mb-4'>Add Job</h6>
                            <form method='post'>
                                <div class='mb-3'>
                                    <label class='form-label'>Job Name</label>
                                    <input type='text' class='form-control' name='job_name' required>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Job Number</label>
                                    <input type='text' class='form-control' name='job_number' required>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Project Manager</label>
                                    <select class='form-select' name='project_manager' required>
                                        <?php foreach ($project_managers as $pm): ?>
                                            <option value='<?php echo htmlspecialchars($pm['id']); ?>'><?php echo htmlspecialchars($pm['first_name'] . ' ' . $pm['last_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type='submit' name='add_job' class='btn btn-primary'>Add Job</button>
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
