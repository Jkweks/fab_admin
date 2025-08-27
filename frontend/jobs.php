<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
include 'includes/db.php';
$pm_stmt = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role = 'project_manager' ORDER BY first_name");
$project_managers = $pm_stmt->fetchAll();
$pm_filter = $_GET['pm'] ?? '';
$sort = $_GET['sort'] ?? 'job_name';
$order = $_GET['order'] ?? 'asc';
$allowed_sorts = ['job_name', 'job_number', 'project_manager'];
if (!in_array($sort, $allowed_sorts)) {
    $sort = 'job_name';
}
$order = strtolower($order) === 'desc' ? 'desc' : 'asc';
if ($pm_filter) {
    $stmt = $pdo->prepare("SELECT jobs.id, jobs.job_name, jobs.job_number, CONCAT(users.first_name, ' ', users.last_name) AS project_manager FROM jobs LEFT JOIN users ON jobs.project_manager = users.id WHERE jobs.project_manager = ? ORDER BY $sort $order");
    $stmt->execute([$pm_filter]);
} else {
    $stmt = $pdo->query("SELECT jobs.id, jobs.job_name, jobs.job_number, CONCAT(users.first_name, ' ', users.last_name) AS project_manager FROM jobs LEFT JOIN users ON jobs.project_manager = users.id ORDER BY $sort $order");
}
$jobs = $stmt->fetchAll();
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
                            <h6 class='mb-4'>Jobs</h6>
                            <form method='get' class='mb-3'>
                                <div class='row'>
                                    <div class='col-md-4'>
                                        <select name='pm' class='form-select' onchange='this.form.submit()'>
                                            <option value=''>All Project Managers</option>
                                            <?php foreach ($project_managers as $pm): ?>
                                                <option value='<?php echo htmlspecialchars($pm['id']); ?>' <?php if ($pm_filter == $pm['id']) echo 'selected'; ?>><?php echo htmlspecialchars($pm['first_name'] . ' ' . $pm['last_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <div class='table-responsive'>
                                <table class='table'>
                                    <thead>
                                        <tr>
                                            <?php
                                            $nameOrder = ($sort === 'job_name' && $order === 'asc') ? 'desc' : 'asc';
                                            $numberOrder = ($sort === 'job_number' && $order === 'asc') ? 'desc' : 'asc';
                                            $pmOrder = ($sort === 'project_manager' && $order === 'asc') ? 'desc' : 'asc';
                                            $pmParam = $pm_filter ? '&pm=' . urlencode($pm_filter) : '';
                                            $nameLink = "?sort=job_name&order={$nameOrder}{$pmParam}";
                                            $numberLink = "?sort=job_number&order={$numberOrder}{$pmParam}";
                                            $pmLink = "?sort=project_manager&order={$pmOrder}{$pmParam}";
                                            ?>
                                            <th scope='col'><a href='<?php echo $nameLink; ?>'>Job Name</a></th>
                                            <th scope='col'><a href='<?php echo $numberLink; ?>'>Job Number</a></th>
                                            <th scope='col'><a href='<?php echo $pmLink; ?>'>Project Manager</a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jobs as $job): ?>
                                        <tr data-bs-toggle='modal' data-bs-target='#jobModal' data-id='<?php echo htmlspecialchars($job['id']); ?>' data-job='<?php echo htmlspecialchars($job['job_name']); ?>' data-number='<?php echo htmlspecialchars($job['job_number']); ?>' data-pm='<?php echo htmlspecialchars($job['project_manager']); ?>' style='cursor:pointer;'>
                                            <td><?php echo htmlspecialchars($job['job_name']); ?></td>
                                            <td><?php echo htmlspecialchars($job['job_number']); ?></td>
                                            <td><?php echo htmlspecialchars($job['project_manager']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
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
    <div class='modal fade' id='jobModal' tabindex='-1' aria-hidden='true'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h5 class='modal-title'>Job Details</h5>
            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class='modal-body'>
            <p><strong>Project Manager:</strong> <span class='project-manager'></span></p>
            <div id='work-orders'></div>
          </div>
          <div class='modal-footer'>
            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
          </div>
        </div>
      </div>
    </div>
    <script>
    var jobModal = document.getElementById('jobModal');
    jobModal.addEventListener('show.bs.modal', function (event) {
        var tr = event.relatedTarget;
        var jobId = tr.getAttribute('data-id');
        var jobName = tr.getAttribute('data-job');
        var jobNumber = tr.getAttribute('data-number');
        var pm = tr.getAttribute('data-pm');
        jobModal.querySelector('.modal-title').textContent = jobName + ' (' + jobNumber + ')';
        jobModal.querySelector('.project-manager').textContent = pm;
        fetch('get_work_orders.php?job_id=' + encodeURIComponent(jobId))
            .then(function(response) { return response.text(); })
            .then(function(html) {
                jobModal.querySelector('#work-orders').innerHTML = html;
            });
    });
    </script>
<?php include 'includes/footer.php'; ?>
