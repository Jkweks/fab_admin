<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
include 'includes/db.php';

$job_id = $_GET['job_id'] ?? '';
if (!$job_id) {
    echo 'No job selected.';
    exit;
}

$job_stmt = $pdo->prepare("SELECT job_name, job_number FROM jobs WHERE id = ?");
$job_stmt->execute([$job_id]);
$job = $job_stmt->fetch();
if (!$job) {
    echo 'Job not found.';
    exit;
}

$conf_stmt = $pdo->prepare("SELECT dc.name, dc.handing, dc.has_transom, wo.work_order_number FROM door_configurations dc JOIN work_orders wo ON dc.work_order_id = wo.id WHERE wo.job_id = ? ORDER BY wo.work_order_number, dc.name");
$conf_stmt->execute([$job_id]);
$configs = $conf_stmt->fetchAll();
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
                        <h6 class='mb-4'>Door Configurations for <?php echo htmlspecialchars($job['job_name']); ?> (<?php echo htmlspecialchars($job['job_number']); ?>)</h6>
                        <?php if ($configs): ?>
                        <div class='table-responsive'>
                            <table class='table'>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Work Order</th>
                                        <th>Handing</th>
                                        <th>Transom</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($configs as $conf): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($conf['name']); ?></td>
                                        <td><?php echo htmlspecialchars($conf['work_order_number']); ?></td>
                                        <td><?php echo htmlspecialchars($conf['handing']); ?></td>
                                        <td><?php echo $conf['has_transom'] ? 'Yes' : 'No'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p>No door configurations found for this job.</p>
                        <?php endif; ?>
                        <a href='door_configurator.php' class='btn btn-primary'>Add Door Configuration</a>
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
