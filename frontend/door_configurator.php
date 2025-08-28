<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO door_configurations (work_order_id, name, has_transom, opening_width, opening_height, frame_height, glazing_thickness, hinge_rail_id, lock_rail_id, top_rail_id, bottom_rail_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $_POST['work_order'],
        $_POST['name'],
        isset($_POST['has_transom']) ? 1 : 0,
        $_POST['opening_width'] !== '' ? $_POST['opening_width'] : null,
        $_POST['opening_height'] !== '' ? $_POST['opening_height'] : null,
        $_POST['frame_height'] !== '' ? $_POST['frame_height'] : null,
        $_POST['glazing'],
        $_POST['hinge_rail'] !== '' ? $_POST['hinge_rail'] : null,
        $_POST['lock_rail'] !== '' ? $_POST['lock_rail'] : null,
        $_POST['top_rail'] !== '' ? $_POST['top_rail'] : null,
        $_POST['bottom_rail'] !== '' ? $_POST['bottom_rail'] : null
    ]);
}

$work_orders = $pdo->query("SELECT wo.id, wo.work_order_number, j.job_name FROM work_orders wo JOIN jobs j ON wo.job_id = j.id ORDER BY j.job_name, wo.work_order_number")->fetchAll();
$hinge_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'hinge_rail' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$lock_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'lock_rail' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$top_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'top_rail' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$bottom_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'bottom_rail' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
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
                        <h6 class='mb-4'>Door Configurator</h6>
                        <ul class='nav nav-tabs' id='doorTabs' role='tablist'>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link active' id='info-tab' data-bs-toggle='tab' data-bs-target='#info' type='button' role='tab'>Info</button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='parts-tab' data-bs-toggle='tab' data-bs-target='#parts' type='button' role='tab'>Door Parts</button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='frame-tab' data-bs-toggle='tab' data-bs-target='#frame' type='button' role='tab'>Frame Parts</button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='hardware-tab' data-bs-toggle='tab' data-bs-target='#hardware' type='button' role='tab'>Hardware</button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='summary-tab' data-bs-toggle='tab' data-bs-target='#summary' type='button' role='tab'>Summary</button>
                            </li>
                        </ul>
                        <form method='post'>
                            <div class='tab-content pt-3' id='doorTabContent'>
                                <div class='tab-pane fade show active' id='info' role='tabpanel'>
                                    <div class='mb-3'>
                                        <label class='form-label'>Work Order</label>
                                        <select class='form-select' name='work_order' required>
                                            <option value=''>Select Work Order</option>
                                            <?php foreach ($work_orders as $wo): ?>
                                                <option value='<?php echo htmlspecialchars($wo['id']); ?>'><?php echo htmlspecialchars($wo['job_name'] . ' - WO #' . $wo['work_order_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Door Name</label>
                                        <input type='text' class='form-control' name='name' required>
                                    </div>
                                    <div class='mb-3 form-check'>
                                        <input class='form-check-input' type='checkbox' id='has_transom' name='has_transom'>
                                        <label class='form-check-label' for='has_transom'>Has Transom</label>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Opening Width</label>
                                        <input type='number' step='any' class='form-control' name='opening_width'>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Opening Height</label>
                                        <input type='number' step='any' class='form-control' name='opening_height'>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Total Frame Height</label>
                                        <input type='number' step='any' class='form-control' name='frame_height' id='frame_height' disabled>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Glazing</label>
                                        <select class='form-select' name='glazing'>
                                            <option value='1/4'>1/4&quot;</option>
                                            <option value='3/8'>3/8&quot;</option>
                                            <option value='1/2'>1/2&quot;</option>
                                            <option value='1'>1&quot;</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='tab-pane fade' id='parts' role='tabpanel'>
                                    <div class='mb-3'>
                                        <label class='form-label'>Hinge Rail</label>
                                        <select class='form-select' name='hinge_rail'>
                                            <option value=''>Select Hinge Rail</option>
                                            <?php foreach ($hinge_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Lock Rail</label>
                                        <select class='form-select' name='lock_rail'>
                                            <option value=''>Select Lock Rail</option>
                                            <?php foreach ($lock_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Top Rail</label>
                                        <select class='form-select' name='top_rail'>
                                            <option value=''>Select Top Rail</option>
                                            <?php foreach ($top_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Bottom Rail</label>
                                        <select class='form-select' name='bottom_rail'>
                                            <option value=''>Select Bottom Rail</option>
                                            <?php foreach ($bottom_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class='tab-pane fade' id='frame' role='tabpanel'>
                                    <p>Frame parts configuration coming soon.</p>
                                </div>
                                <div class='tab-pane fade' id='hardware' role='tabpanel'>
                                    <p>Hardware configuration coming soon.</p>
                                </div>
                                <div class='tab-pane fade' id='summary' role='tabpanel'>
                                    <div id='summary-content'>
                                        <p><strong>Work Order:</strong> <span id='summary-workorder'></span></p>
                                        <p><strong>Door Name:</strong> <span id='summary-name'></span></p>
                                        <p><strong>Opening Width:</strong> <span id='summary-width'></span></p>
                                        <p><strong>Opening Height:</strong> <span id='summary-height'></span></p>
                                        <p><strong>Total Frame Height:</strong> <span id='summary-frame'></span></p>
                                        <p><strong>Glazing:</strong> <span id='summary-glazing'></span></p>
                                        <p><strong>Hinge Rail:</strong> <span id='summary-hinge'></span></p>
                                        <p><strong>Lock Rail:</strong> <span id='summary-lock'></span></p>
                                        <p><strong>Top Rail:</strong> <span id='summary-top'></span></p>
                                        <p><strong>Bottom Rail:</strong> <span id='summary-bottom'></span></p>
                                    </div>
                                    <button type='submit' class='btn btn-primary'>Save Door</button>
                                </div>
                            </div>
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
<script>
document.getElementById('has_transom').addEventListener('change', function() {
    document.getElementById('frame_height').disabled = !this.checked;
});

var summaryTab = document.getElementById('summary-tab');
summaryTab.addEventListener('shown.bs.tab', function () {
    var wo = document.querySelector("select[name='work_order']");
    document.getElementById('summary-workorder').textContent = wo.value ? wo.options[wo.selectedIndex].text : '';
    document.getElementById('summary-name').textContent = document.querySelector("input[name='name']").value;
    document.getElementById('summary-width').textContent = document.querySelector("input[name='opening_width']").value;
    document.getElementById('summary-height').textContent = document.querySelector("input[name='opening_height']").value;
    document.getElementById('summary-frame').textContent = document.querySelector("input[name='frame_height']").value;
    document.getElementById('summary-glazing').textContent = document.querySelector("select[name='glazing']").value;
    function selText(name) {
        var s = document.querySelector("select[name='" + name + "']");
        return s && s.value ? s.options[s.selectedIndex].text : '';
    }
    document.getElementById('summary-hinge').textContent = selText('hinge_rail');
    document.getElementById('summary-lock').textContent = selText('lock_rail');
    document.getElementById('summary-top').textContent = selText('top_rail');
    document.getElementById('summary-bottom').textContent = selText('bottom_rail');
});
</script>
<?php include 'includes/footer.php'; ?>

