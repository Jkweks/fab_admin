<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
include 'includes/db.php';

$jobs = $pdo->query("SELECT id, job_name FROM jobs ORDER BY job_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO door_configurations (work_order_id, name, has_transom, opening_width, opening_height, frame_height, glazing_thickness, hinge_rail_id, lock_rail_id, top_rail_id, bottom_rail_id, top_gap, bottom_gap, hinge_gap, latch_gap, handing, hinge_rail_2_id, lock_rail_2_id, top_rail_2_id, bottom_rail_2_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
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
        $_POST['bottom_rail'] !== '' ? $_POST['bottom_rail'] : null,
        $_POST['top_gap'] !== '' ? $_POST['top_gap'] : null,
        $_POST['bottom_gap'] !== '' ? $_POST['bottom_gap'] : null,
        $_POST['hinge_gap'] !== '' ? $_POST['hinge_gap'] : null,
        $_POST['latch_gap'] !== '' ? $_POST['latch_gap'] : null,
        $_POST['handing'],
        $_POST['hinge_rail_2'] !== '' ? $_POST['hinge_rail_2'] : null,
        $_POST['lock_rail_2'] !== '' ? $_POST['lock_rail_2'] : null,
        $_POST['top_rail_2'] !== '' ? $_POST['top_rail_2'] : null,
        $_POST['bottom_rail_2'] !== '' ? $_POST['bottom_rail_2'] : null
    ]);
}
$hinge_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'hinge_rail' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$lock_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'lock_rail' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$top_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'top_rail' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$bottom_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'bottom_rail' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
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
                                <button class='nav-link' id='cutlist-tab' data-bs-toggle='tab' data-bs-target='#cutlist' type='button' role='tab'>Cut List</button>
                            </li>
                            <li class='nav-item' role='presentation'>
                                <button class='nav-link' id='summary-tab' data-bs-toggle='tab' data-bs-target='#summary' type='button' role='tab'>Summary</button>
                            </li>
                        </ul>
                        <form method='post'>
                            <div class='tab-content pt-3' id='doorTabContent'>
                                <div class='tab-pane fade show active' id='info' role='tabpanel'>
                                    <div class='mb-3'>
                                        <label class='form-label'>Job</label>
                                        <select class='form-select' id='job_select' required>
                                            <option value=''>Select Job</option>
                                            <?php foreach ($jobs as $job): ?>
                                                <option value='<?php echo htmlspecialchars($job['id']); ?>'><?php echo htmlspecialchars($job['job_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Work Order</label>
                                        <select class='form-select' name='work_order' id='work_order_select' required disabled>
                                            <option value=''>Select Work Order</option>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Door Name</label>
                                        <input type='text' class='form-control' name='name' required>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Handing</label>
                                        <select class='form-select' name='handing' id='handing'>
                                            <option value='single_lh_rhr'>Single - LH/RHR</option>
                                            <option value='single_rh_lhr'>Single - RH/LHR</option>
                                            <option value='pair_rhra'>Pair - RHRA</option>
                                            <option value='pair_lhra'>Pair - LHRA</option>
                                        </select>
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
                                            <option value='1/4'>1/4"</option>
                                            <option value='3/8'>3/8"</option>
                                            <option value='1/2'>1/2"</option>
                                            <option value='1'>1"</option>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <a href='#' id='edit-settings'>Edit Global Settings</a>
                                    </div>
                                    <input type='hidden' name='top_gap' id='top_gap' value='0.125'>
                                    <input type='hidden' name='bottom_gap' id='bottom_gap' value='0.6975'>
                                    <input type='hidden' name='hinge_gap' id='hinge_gap' value='0.0625'>
                                    <input type='hidden' name='latch_gap' id='latch_gap' value='0.125'>
                                </div>
                                <div class='tab-pane fade' id='parts' role='tabpanel'>
                                    <div class='mb-3'>
                                        <label class='form-label'>Hinge Rail</label>
                                        <select class='form-select' name='hinge_rail'>
                                            <option value=''>Select Hinge Rail</option>
                                            <?php foreach ($hinge_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Lock Rail</label>
                                        <select class='form-select' name='lock_rail'>
                                            <option value=''>Select Lock Rail</option>
                                            <?php foreach ($lock_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Top Rail</label>
                                        <select class='form-select' name='top_rail'>
                                            <option value=''>Select Top Rail</option>
                                            <?php foreach ($top_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Bottom Rail</label>
                                        <select class='form-select' name='bottom_rail'>
                                            <option value=''>Select Bottom Rail</option>
                                            <?php foreach ($bottom_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div id='second_leaf' style='display:none'>
                                        <h6>Second Leaf</h6>
                                        <div class='mb-3'>
                                            <label class='form-label'>Hinge Rail (2)</label>
                                            <select class='form-select' name='hinge_rail_2'>
                                                <option value=''>Select Hinge Rail</option>
                                                <?php foreach ($hinge_parts as $part): ?>
                                                    <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class='mb-3'>
                                            <label class='form-label'>Lock Rail (2)</label>
                                            <select class='form-select' name='lock_rail_2'>
                                                <option value=''>Select Lock Rail</option>
                                                <?php foreach ($lock_parts as $part): ?>
                                                    <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class='mb-3'>
                                            <label class='form-label'>Top Rail (2)</label>
                                            <select class='form-select' name='top_rail_2'>
                                                <option value=''>Select Top Rail</option>
                                                <?php foreach ($top_parts as $part): ?>
                                                    <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class='mb-3'>
                                            <label class='form-label'>Bottom Rail (2)</label>
                                            <select class='form-select' name='bottom_rail_2'>
                                                <option value=''>Select Bottom Rail</option>
                                                <?php foreach ($bottom_parts as $part): ?>
                                                    <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class='tab-pane fade' id='frame' role='tabpanel'>
                                    <p>Frame parts configuration coming soon.</p>
                                </div>
                                <div class='tab-pane fade' id='hardware' role='tabpanel'>
                                    <p>Hardware configuration coming soon.</p>
                                </div>
                                <div class='tab-pane fade' id='cutlist' role='tabpanel'>
                                    <table class='table'>
                                        <thead><tr><th>Part</th><th>Cut Length</th></tr></thead>
                                        <tbody id='cutlist-body'></tbody>
                                    </table>
                                </div>
                                <div class='tab-pane fade' id='summary' role='tabpanel'>
                                    <div id='summary-content'>
                                        <p><strong>Work Order:</strong> <span id='summary-workorder'></span></p>
                                        <p><strong>Door Name:</strong> <span id='summary-name'></span></p>
                                        <p><strong>Handing:</strong> <span id='summary-handing'></span></p>
                                        <p><strong>Opening Width:</strong> <span id='summary-width'></span></p>
                                        <p><strong>Opening Height:</strong> <span id='summary-height'></span></p>
                                        <p><strong>Total Frame Height:</strong> <span id='summary-frame'></span></p>
                                        <p><strong>Glazing:</strong> <span id='summary-glazing'></span></p>
                                        <p><strong>Top Gap:</strong> <span id='summary-topgap'></span></p>
                                        <p><strong>Bottom Gap:</strong> <span id='summary-bottomgap'></span></p>
                                        <p><strong>Hinge Gap:</strong> <span id='summary-hingegap'></span></p>
                                        <p><strong>Latch Gap:</strong> <span id='summary-latchgap'></span></p>
                                        <p><strong>Hinge Rail:</strong> <span id='summary-hinge'></span></p>
                                        <p><strong>Lock Rail:</strong> <span id='summary-lock'></span></p>
                                        <p><strong>Top Rail:</strong> <span id='summary-top'></span></p>
                                        <p><strong>Bottom Rail:</strong> <span id='summary-bottom'></span></p>
                                        <p><strong>Second Hinge Rail:</strong> <span id='summary-hinge2'></span></p>
                                        <p><strong>Second Lock Rail:</strong> <span id='summary-lock2'></span></p>
                                        <p><strong>Second Top Rail:</strong> <span id='summary-top2'></span></p>
                                        <p><strong>Second Bottom Rail:</strong> <span id='summary-bottom2'></span></p>
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
<div class='modal fade' id='settingsModal' tabindex='-1'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title'>Global Settings</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
            </div>
            <div class='modal-body'>
                <div class='mb-3'><label class='form-label'>Top Gap</label><input type='number' step='any' class='form-control' id='input_top_gap'></div>
                <div class='mb-3'><label class='form-label'>Bottom Gap</label><input type='number' step='any' class='form-control' id='input_bottom_gap'></div>
                <div class='mb-3'><label class='form-label'>Hinge Gap</label><input type='number' step='any' class='form-control' id='input_hinge_gap'></div>
                <div class='mb-3'><label class='form-label'>Latch Gap</label><input type='number' step='any' class='form-control' id='input_latch_gap'></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                <button type='button' class='btn btn-primary' id='save_settings'>Save</button>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('has_transom').addEventListener('change', function() {
    document.getElementById('frame_height').disabled = !this.checked;
});

document.getElementById('job_select').addEventListener('change', function() {
    var jobId = this.value;
    var woSelect = document.getElementById('work_order_select');
    woSelect.innerHTML = '<option value="">Select Work Order</option>';
    woSelect.disabled = true;
    if (jobId) {
        fetch('get_work_orders_by_job.php?job_id=' + jobId)
            .then(r => r.json())
            .then(data => {
                data.forEach(function(wo) {
                    var opt = document.createElement('option');
                    opt.value = wo.id;
                    opt.textContent = 'WO #' + wo.work_order_number;
                    woSelect.appendChild(opt);
                });
                woSelect.disabled = false;
            });
    }
});

var handingSelect = document.getElementById('handing');
handingSelect.addEventListener('change', function() {
    document.getElementById('second_leaf').style.display = this.value.startsWith('pair') ? 'block' : 'none';
});

document.getElementById('edit-settings').addEventListener('click', function(e){
    e.preventDefault();
    document.getElementById('input_top_gap').value = document.getElementById('top_gap').value;
    document.getElementById('input_bottom_gap').value = document.getElementById('bottom_gap').value;
    document.getElementById('input_hinge_gap').value = document.getElementById('hinge_gap').value;
    document.getElementById('input_latch_gap').value = document.getElementById('latch_gap').value;
    new bootstrap.Modal(document.getElementById('settingsModal')).show();
});

document.getElementById('save_settings').addEventListener('click', function(){
    document.getElementById('top_gap').value = document.getElementById('input_top_gap').value;
    document.getElementById('bottom_gap').value = document.getElementById('input_bottom_gap').value;
    document.getElementById('hinge_gap').value = document.getElementById('input_hinge_gap').value;
    document.getElementById('latch_gap').value = document.getElementById('input_latch_gap').value;
    bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();
});

var summaryTab = document.getElementById('summary-tab');
summaryTab.addEventListener('shown.bs.tab', function () {
    var wo = document.querySelector("select[name='work_order']");
    document.getElementById('summary-workorder').textContent = wo.value ? wo.options[wo.selectedIndex].text : '';
    document.getElementById('summary-name').textContent = document.querySelector("input[name='name']").value;
    document.getElementById('summary-handing').textContent = handingSelect.options[handingSelect.selectedIndex].text;
    document.getElementById('summary-width').textContent = document.querySelector("input[name='opening_width']").value;
    document.getElementById('summary-height').textContent = document.querySelector("input[name='opening_height']").value;
    document.getElementById('summary-frame').textContent = document.querySelector("input[name='frame_height']").value;
    document.getElementById('summary-glazing').textContent = document.querySelector("select[name='glazing']").value;
    document.getElementById('summary-topgap').textContent = document.getElementById('top_gap').value;
    document.getElementById('summary-bottomgap').textContent = document.getElementById('bottom_gap').value;
    document.getElementById('summary-hingegap').textContent = document.getElementById('hinge_gap').value;
    document.getElementById('summary-latchgap').textContent = document.getElementById('latch_gap').value;
    function selText(name) {
        var s = document.querySelector("select[name='" + name + "']");
        return s && s.value ? s.options[s.selectedIndex].text : '';
    }
    document.getElementById('summary-hinge').textContent = selText('hinge_rail');
    document.getElementById('summary-lock').textContent = selText('lock_rail');
    document.getElementById('summary-top').textContent = selText('top_rail');
    document.getElementById('summary-bottom').textContent = selText('bottom_rail');
    document.getElementById('summary-hinge2').textContent = selText('hinge_rail_2');
    document.getElementById('summary-lock2').textContent = selText('lock_rail_2');
    document.getElementById('summary-top2').textContent = selText('top_rail_2');
    document.getElementById('summary-bottom2').textContent = selText('bottom_rail_2');
});

var cutlistTab = document.getElementById('cutlist-tab');
cutlistTab.addEventListener('shown.bs.tab', function(){
    var width = parseFloat(document.querySelector("input[name='opening_width']").value) || 0;
    var height = parseFloat(document.querySelector("input[name='opening_height']").value) || 0;
    var topGap = parseFloat(document.getElementById('top_gap').value) || 0;
    var bottomGap = parseFloat(document.getElementById('bottom_gap').value) || 0;
    var hingeGap = parseFloat(document.getElementById('hinge_gap').value) || 0;
    var latchGap = parseFloat(document.getElementById('latch_gap').value) || 0;
    var hingeSelect = document.querySelector("select[name='hinge_rail']");
    var lockSelect = document.querySelector("select[name='lock_rail']");
    var hingeLz = parseFloat(hingeSelect.options[hingeSelect.selectedIndex].dataset.lz || 0) || 0;
    var lockLz = parseFloat(lockSelect.options[lockSelect.selectedIndex].dataset.lz || 0) || 0;
    var railCut = width - hingeGap - latchGap - hingeLz - lockLz;
    var stileCut = height - topGap - bottomGap;
    var body = document.getElementById('cutlist-body');
    body.innerHTML = '';
    if (!isNaN(railCut)) {
        body.innerHTML += '<tr><td>Top Rail</td><td>' + railCut.toFixed(3) + '</td></tr>';
        body.innerHTML += '<tr><td>Bottom Rail</td><td>' + railCut.toFixed(3) + '</td></tr>';
    }
    if (!isNaN(stileCut)) {
        body.innerHTML += '<tr><td>Hinge Rail</td><td>' + stileCut.toFixed(3) + '</td></tr>';
        body.innerHTML += '<tr><td>Lock Rail</td><td>' + stileCut.toFixed(3) + '</td></tr>';
        if (document.getElementById('second_leaf').style.display === 'block') {
            var hingeSelect2 = document.querySelector("select[name='hinge_rail_2']");
            var lockSelect2 = document.querySelector("select[name='lock_rail_2']");
            var hingeLz2 = parseFloat(hingeSelect2.options[hingeSelect2.selectedIndex].dataset.lz || 0) || 0;
            var lockLz2 = parseFloat(lockSelect2.options[lockSelect2.selectedIndex].dataset.lz || 0) || 0;
            var railCut2 = width - hingeGap - latchGap - hingeLz2 - lockLz2;
            body.innerHTML += '<tr><td>Second Hinge Rail</td><td>' + stileCut.toFixed(3) + '</td></tr>';
            body.innerHTML += '<tr><td>Second Lock Rail</td><td>' + stileCut.toFixed(3) + '</td></tr>';
            if (!isNaN(railCut2)) {
                body.innerHTML += '<tr><td>Second Top Rail</td><td>' + railCut2.toFixed(3) + '</td></tr>';
                body.innerHTML += '<tr><td>Second Bottom Rail</td><td>' + railCut2.toFixed(3) + '</td></tr>';
            }
        }
    }
});
</script>
<?php include 'includes/footer.php'; ?>

