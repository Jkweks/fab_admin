<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
include 'includes/db.php';

$jobs = $pdo->query("SELECT id, job_name FROM jobs ORDER BY job_name")->fetchAll();
$config_id = $_GET['id'] ?? $_POST['id'] ?? null;
$config = null;
if ($config_id) {
    $cfg_stmt = $pdo->prepare("SELECT dc.*, wo.job_id, jobs.job_name, wo.work_order_number FROM door_configurations dc JOIN work_orders wo ON dc.work_order_id = wo.id JOIN jobs ON wo.job_id = jobs.id WHERE dc.id=?");
    $cfg_stmt->execute([$config_id]);
    $config = $cfg_stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config_id = $_POST['id'] ?? null;
    if ($config_id) {
        $stmt = $pdo->prepare('UPDATE door_configurations SET name=?, has_transom=?, opening_width=?, opening_height=?, frame_height=?, glazing_thickness=?, hinge_rail_id=?, lock_rail_id=?, top_rail_id=?, bottom_rail_id=?, top_gap=?, bottom_gap=?, hinge_gap=?, latch_gap=?, handing=?, hinge_rail_2_id=?, lock_rail_2_id=?, top_rail_2_id=?, bottom_rail_2_id=?, frame_system=?, frame_finish=?, hinge_jamb_id=?, lock_jamb_id=?, rh_hinge_jamb_id=?, lh_hinge_jamb_id=?, door_header_id=?, transom_header_id=?, hinge_door_stop_id=?, latch_door_stop_id=?, head_door_stop_id=?, horizontal_transom_gutter_id=?, horizontal_transom_stop_id=?, vertical_transom_gutter_id=?, vertical_transom_stop_id=?, head_transom_stop_id=?, transom_head_perimeter_filler_id=? WHERE id=?');
        $stmt->execute([
            $_POST['name'],
            isset($_POST['has_transom']) ? 1 : 0,
            $_POST['opening_width'] !== '' ? $_POST['opening_width'] : null,
            $_POST['opening_height'] !== '' ? $_POST['opening_height'] : null,
            (isset($_POST['frame_height']) && $_POST['frame_height'] !== '') ? $_POST['frame_height'] : null,
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
            $_POST['bottom_rail_2'] !== '' ? $_POST['bottom_rail_2'] : null,
            $_POST['frame_system'] !== '' ? $_POST['frame_system'] : null,
            $_POST['frame_finish'] !== '' ? $_POST['frame_finish'] : null,
            $_POST['hinge_jamb'] !== '' ? $_POST['hinge_jamb'] : null,
            $_POST['lock_jamb'] !== '' ? $_POST['lock_jamb'] : null,
            $_POST['rh_hinge_jamb'] !== '' ? $_POST['rh_hinge_jamb'] : null,
            $_POST['lh_hinge_jamb'] !== '' ? $_POST['lh_hinge_jamb'] : null,
            $_POST['door_header'] !== '' ? $_POST['door_header'] : null,
            $_POST['transom_header'] !== '' ? $_POST['transom_header'] : null,
            $_POST['hinge_door_stop'] !== '' ? $_POST['hinge_door_stop'] : null,
            $_POST['latch_door_stop'] !== '' ? $_POST['latch_door_stop'] : null,
            $_POST['head_door_stop'] !== '' ? $_POST['head_door_stop'] : null,
            $_POST['horizontal_transom_gutter'] !== '' ? $_POST['horizontal_transom_gutter'] : null,
            $_POST['horizontal_transom_stop'] !== '' ? $_POST['horizontal_transom_stop'] : null,
            $_POST['vertical_transom_gutter'] !== '' ? $_POST['vertical_transom_gutter'] : null,
            $_POST['vertical_transom_stop'] !== '' ? $_POST['vertical_transom_stop'] : null,
            $_POST['head_transom_stop'] !== '' ? $_POST['head_transom_stop'] : null,
            $_POST['transom_head_perimeter_filler'] !== '' ? $_POST['transom_head_perimeter_filler'] : null,
            $config_id
        ]);
        $cfg_stmt = $pdo->prepare("SELECT dc.*, wo.job_id, jobs.job_name, wo.work_order_number FROM door_configurations dc JOIN work_orders wo ON dc.work_order_id = wo.id JOIN jobs ON wo.job_id = jobs.id WHERE dc.id=?");
        $cfg_stmt->execute([$config_id]);
        $config = $cfg_stmt->fetch();
    } else {
        $stmt = $pdo->prepare('INSERT INTO door_configurations (work_order_id, name, has_transom, opening_width, opening_height, frame_height, glazing_thickness, hinge_rail_id, lock_rail_id, top_rail_id, bottom_rail_id, top_gap, bottom_gap, hinge_gap, latch_gap, handing, hinge_rail_2_id, lock_rail_2_id, top_rail_2_id, bottom_rail_2_id, frame_system, frame_finish, hinge_jamb_id, lock_jamb_id, rh_hinge_jamb_id, lh_hinge_jamb_id, door_header_id, transom_header_id, hinge_door_stop_id, latch_door_stop_id, head_door_stop_id, horizontal_transom_gutter_id, horizontal_transom_stop_id, vertical_transom_gutter_id, vertical_transom_stop_id, head_transom_stop_id, transom_head_perimeter_filler_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) RETURNING id');
        $stmt->execute([
            $_POST['work_order'],
            $_POST['name'],
            isset($_POST['has_transom']) ? 1 : 0,
            $_POST['opening_width'] !== '' ? $_POST['opening_width'] : null,
            $_POST['opening_height'] !== '' ? $_POST['opening_height'] : null,
            (isset($_POST['frame_height']) && $_POST['frame_height'] !== '') ? $_POST['frame_height'] : null,
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
            $_POST['bottom_rail_2'] !== '' ? $_POST['bottom_rail_2'] : null,
            $_POST['frame_system'] !== '' ? $_POST['frame_system'] : null,
            $_POST['frame_finish'] !== '' ? $_POST['frame_finish'] : null,
            $_POST['hinge_jamb'] !== '' ? $_POST['hinge_jamb'] : null,
            $_POST['lock_jamb'] !== '' ? $_POST['lock_jamb'] : null,
            $_POST['rh_hinge_jamb'] !== '' ? $_POST['rh_hinge_jamb'] : null,
            $_POST['lh_hinge_jamb'] !== '' ? $_POST['lh_hinge_jamb'] : null,
            $_POST['door_header'] !== '' ? $_POST['door_header'] : null,
            $_POST['transom_header'] !== '' ? $_POST['transom_header'] : null,
            $_POST['hinge_door_stop'] !== '' ? $_POST['hinge_door_stop'] : null,
            $_POST['latch_door_stop'] !== '' ? $_POST['latch_door_stop'] : null,
            $_POST['head_door_stop'] !== '' ? $_POST['head_door_stop'] : null,
            $_POST['horizontal_transom_gutter'] !== '' ? $_POST['horizontal_transom_gutter'] : null,
            $_POST['horizontal_transom_stop'] !== '' ? $_POST['horizontal_transom_stop'] : null,
            $_POST['vertical_transom_gutter'] !== '' ? $_POST['vertical_transom_gutter'] : null,
            $_POST['vertical_transom_stop'] !== '' ? $_POST['vertical_transom_stop'] : null,
            $_POST['head_transom_stop'] !== '' ? $_POST['head_transom_stop'] : null,
            $_POST['transom_head_perimeter_filler'] !== '' ? $_POST['transom_head_perimeter_filler'] : null
        ]);
        $config_id = $stmt->fetchColumn();
        $cfg_stmt = $pdo->prepare("SELECT dc.*, wo.job_id, jobs.job_name, wo.work_order_number FROM door_configurations dc JOIN work_orders wo ON dc.work_order_id = wo.id JOIN jobs ON wo.job_id = jobs.id WHERE dc.id=?");
        $cfg_stmt->execute([$config_id]);
        $config = $cfg_stmt->fetch();
    }
}
$hinge_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'hinge_rail' AND dp.category = 'door' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$lock_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'lock_rail' AND dp.category = 'door' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$top_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'top_rail' AND dp.category = 'door' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$bottom_parts = $pdo->query("SELECT DISTINCT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'bottom_rail' AND dp.category = 'door' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$part_presets = $pdo->query("SELECT id, name, hinge_rail_id, lock_rail_id, top_rail_id, bottom_rail_id FROM door_part_presets ORDER BY name")->fetchAll();

$systems = $pdo->query("SELECT m.name AS manufacturer, s.name AS system FROM systems s JOIN manufacturers m ON s.manufacturer_id = m.id ORDER BY m.name, s.name")->fetchAll();

$hinge_jamb_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'hinge_jamb' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$lock_jamb_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'lock_jamb' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$door_header_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.ly, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'door_header' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$transom_header_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.ly FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'transom_header' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$hinge_door_stop_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.ly, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'hinge_door_stop' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$latch_door_stop_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.ly, dp.lz FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'latch_door_stop' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$head_door_stop_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'head_door_stop' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$horizontal_transom_gutter_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.ly FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'horizontal_transom_gutter' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$horizontal_transom_stop_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.ly FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'horizontal_transom_stop' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$vertical_transom_gutter_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'vertical_transom_gutter' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$vertical_transom_stop_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'vertical_transom_stop' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$head_transom_stop_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number, dp.ly FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'head_transom_stop' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
$transom_head_perimeter_filler_parts = $pdo->query("SELECT dp.id, dp.manufacturer, dp.system, dp.part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id = dpf.part_id WHERE dpf.function = 'transom_head_perimeter_filler' AND dp.category = 'frame' ORDER BY dp.manufacturer, dp.system, dp.part_number")->fetchAll();
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
                            <input type='hidden' name='id' value='<?php echo htmlspecialchars($config_id ?? ""); ?>'>
                            <div class='tab-content pt-3' id='doorTabContent'>
                                <div class='tab-pane fade show active' id='info' role='tabpanel'>
                                    <div class='mb-3'>
                                        <label class='form-label'>Job</label>
                                        <?php if ($config): ?>
                                        <select class='form-select' id='job_select' required disabled>
                                            <option value='<?php echo htmlspecialchars($config['job_id']); ?>' selected><?php echo htmlspecialchars($config['job_name']); ?></option>
                                        </select>
                                        <?php else: ?>
                                        <select class='form-select' id='job_select' required>
                                            <option value=''>Select Job</option>
                                            <?php foreach ($jobs as $job): ?>
                                                <option value='<?php echo htmlspecialchars($job['id']); ?>'><?php echo htmlspecialchars($job['job_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php endif; ?>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Work Order</label>
                                        <?php if ($config): ?>
                                        <select class='form-select' name='work_order' id='work_order_select' required disabled>
                                            <option value='<?php echo htmlspecialchars($config['work_order_id']); ?>' selected>Work Order <?php echo htmlspecialchars($config['work_order_number']); ?></option>
                                        </select>
                                        <input type='hidden' name='work_order' value='<?php echo htmlspecialchars($config['work_order_id']); ?>'>
                                        <?php else: ?>
                                        <select class='form-select' name='work_order' id='work_order_select' required disabled>
                                            <option value=''>Select Work Order</option>
                                        </select>
                                        <?php endif; ?>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Door Name</label>
                                        <input type='text' class='form-control' name='name' required value='<?php echo htmlspecialchars($config['name'] ?? ""); ?>'>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Handing</label>
                                        <select class='form-select' name='handing' id='handing'>
                                            <option value='single_lh_rhr' <?php if (($config['handing'] ?? '')==='single_lh_rhr') echo 'selected'; ?>>Single - LH/RHR</option>
                                            <option value='single_rh_lhr' <?php if (($config['handing'] ?? '')==='single_rh_lhr') echo 'selected'; ?>>Single - RH/LHR</option>
                                            <option value='pair_rhra' <?php if (($config['handing'] ?? '')==='pair_rhra') echo 'selected'; ?>>Pair - RHRA</option>
                                            <option value='pair_lhra' <?php if (($config['handing'] ?? '')==='pair_lhra') echo 'selected'; ?>>Pair - LHRA</option>
                                        </select>
                                    </div>
                                    <div class='mb-3 form-check'>
                                        <input class='form-check-input' type='checkbox' id='has_transom' name='has_transom' <?php if (!empty($config['has_transom'])) echo 'checked'; ?>>
                                        <label class='form-check-label' for='has_transom'>Has Transom</label>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Opening Width</label>
                                        <input type='number' step='any' class='form-control' name='opening_width' value='<?php echo htmlspecialchars($config['opening_width'] ?? ""); ?>'>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Opening Height</label>
                                        <input type='number' step='any' class='form-control' name='opening_height' value='<?php echo htmlspecialchars($config['opening_height'] ?? ""); ?>'>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Total Frame Height</label>
                                        <input type='number' step='any' class='form-control' name='frame_height' id='frame_height' value='<?php echo htmlspecialchars($config['frame_height'] ?? ""); ?>' disabled>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Glazing</label>
                                        <select class='form-select' name='glazing'>
                                            <option value='1/4' <?php if (($config['glazing_thickness'] ?? '')==='1/4') echo 'selected'; ?>>1/4"</option>
                                            <option value='3/8' <?php if (($config['glazing_thickness'] ?? '')==='3/8') echo 'selected'; ?>>3/8"</option>
                                            <option value='1/2' <?php if (($config['glazing_thickness'] ?? '')==='1/2') echo 'selected'; ?>>1/2"</option>
                                            <option value='1' <?php if (($config['glazing_thickness'] ?? '')==='1') echo 'selected'; ?>>1"</option>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <a href='#' id='edit-settings'>Edit Global Settings</a>
                                    </div>
                                    <input type='hidden' name='top_gap' id='top_gap' value='<?php echo htmlspecialchars($config['top_gap'] ?? 0.125); ?>'>
                                    <input type='hidden' name='bottom_gap' id='bottom_gap' value='<?php echo htmlspecialchars($config['bottom_gap'] ?? 0.6975); ?>'>
                                    <input type='hidden' name='hinge_gap' id='hinge_gap' value='<?php echo htmlspecialchars($config['hinge_gap'] ?? 0.0625); ?>'>
                                    <input type='hidden' name='latch_gap' id='latch_gap' value='<?php echo htmlspecialchars($config['latch_gap'] ?? 0.125); ?>'>
                                </div>
                                <div class='tab-pane fade' id='parts' role='tabpanel'>
                                    <div class='mb-3'>
                                        <label class='form-label'>Finish</label>
                                        <select class='form-select' name='door_finish'>
                                            <option value='c2' <?php if (($config['door_finish'] ?? '')==='c2') echo 'selected'; ?>>C2</option>
                                            <option value='db' <?php if (($config['door_finish'] ?? '')==='db') echo 'selected'; ?>>DB</option>
                                            <option value='bl' <?php if (($config['door_finish'] ?? '')==='bl') echo 'selected'; ?>>BL</option>
                                        </select>
                                    </div>
                                    <ul class='nav nav-tabs' id='doorLeafTabs' role='tablist'>
                                        <li class='nav-item' role='presentation'>
                                            <button class='nav-link active' id='active-door-tab' data-bs-toggle='tab' data-bs-target='#active-door' type='button' role='tab'>Active Door</button>
                                        </li>
                                        <li class='nav-item' role='presentation' id='inactive-door-tab-item' style='display:none;'>
                                            <button class='nav-link' id='inactive-door-tab' data-bs-toggle='tab' data-bs-target='#inactive-door' type='button' role='tab'>Inactive Door</button>
                                        </li>
                                    </ul>
                                    <div class='tab-content pt-3'>
                                        <div class='tab-pane fade show active' id='active-door' role='tabpanel'>
                                            <div class='mb-3'>
                                                <label class='form-label'>Preset</label>
                                                <select class='form-select' id='preset_select'>
                                                    <option value=''>Select Preset</option>
                                                    <?php foreach ($part_presets as $preset): ?>
                                                        <option value='<?php echo htmlspecialchars($preset['id']); ?>' data-hinge='<?php echo htmlspecialchars($preset['hinge_rail_id']); ?>' data-lock='<?php echo htmlspecialchars($preset['lock_rail_id']); ?>' data-top='<?php echo htmlspecialchars($preset['top_rail_id']); ?>' data-bottom='<?php echo htmlspecialchars($preset['bottom_rail_id']); ?>'><?php echo htmlspecialchars($preset['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='mb-3'>
                                                <label class='form-label'>Hinge Rail</label>
                                                <select class='form-select' name='hinge_rail'>
                                                    <option value=''>Select Hinge Rail</option>
                                                    <?php foreach ($hinge_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='door' <?php if (($config['hinge_rail_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='mb-3'>
                                                <label class='form-label'>Lock Rail</label>
                                                <select class='form-select' name='lock_rail'>
                                                    <option value=''>Select Lock Rail</option>
                                                    <?php foreach ($lock_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='door' <?php if (($config['lock_rail_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='mb-3'>
                                                <label class='form-label'>Top Rail</label>
                                                <select class='form-select' name='top_rail'>
                                                    <option value=''>Select Top Rail</option>
                                                    <?php foreach ($top_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='door' <?php if (($config['top_rail_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='mb-3'>
                                                <label class='form-label'>Bottom Rail</label>
                                                <select class='form-select' name='bottom_rail'>
                                                    <option value=''>Select Bottom Rail</option>
                                                    <?php foreach ($bottom_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='door' <?php if (($config['bottom_rail_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class='tab-pane fade' id='inactive-door' role='tabpanel'>
                                            <div class='mb-3'>
                                                <label class='form-label'>Preset</label>
                                                <select class='form-select' id='preset_select_2'>
                                                    <option value=''>Select Preset</option>
                                                    <?php foreach ($part_presets as $preset): ?>
                                                        <option value='<?php echo htmlspecialchars($preset['id']); ?>' data-hinge='<?php echo htmlspecialchars($preset['hinge_rail_id']); ?>' data-lock='<?php echo htmlspecialchars($preset['lock_rail_id']); ?>' data-top='<?php echo htmlspecialchars($preset['top_rail_id']); ?>' data-bottom='<?php echo htmlspecialchars($preset['bottom_rail_id']); ?>'><?php echo htmlspecialchars($preset['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='mb-3'>
                                                <label class='form-label'>Hinge Rail</label>
                                                <select class='form-select' name='hinge_rail_2'>
                                                    <option value=''>Select Hinge Rail</option>
                                                    <?php foreach ($hinge_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='door' <?php if (($config['hinge_rail_2_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='mb-3'>
                                                <label class='form-label'>Lock Rail</label>
                                                <select class='form-select' name='lock_rail_2'>
                                                    <option value=''>Select Lock Rail</option>
                                                    <?php foreach ($lock_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='door' <?php if (($config['lock_rail_2_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='mb-3'>
                                                <label class='form-label'>Top Rail</label>
                                                <select class='form-select' name='top_rail_2'>
                                                    <option value=''>Select Top Rail</option>
                                                    <?php foreach ($top_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='door' <?php if (($config['top_rail_2_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class='mb-3'>
                                                <label class='form-label'>Bottom Rail</label>
                                                <select class='form-select' name='bottom_rail_2'>
                                                    <option value=''>Select Bottom Rail</option>
                                                    <?php foreach ($bottom_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='door' <?php if (($config['bottom_rail_2_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class='tab-pane fade' id='frame' role='tabpanel'>
                                    <div class='mb-3'>
                                        <label class='form-label'>System</label>
                                        <select class='form-select' name='frame_system'>
                                            <option value=''>Select System</option>
                                            <?php foreach ($systems as $sys): ?>
                                                <option value='<?php echo htmlspecialchars($sys['system']); ?>' <?php if (($config['frame_system'] ?? '') == $sys['system']) echo 'selected'; ?>><?php echo htmlspecialchars($sys['manufacturer'] . ' ' . $sys['system']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Finish</label>
                                        <select class='form-select' name='frame_finish'>
                                            <option value='c2' <?php if (($config['frame_finish'] ?? '') === 'c2') echo 'selected'; ?>>C2</option>
                                            <option value='db' <?php if (($config['frame_finish'] ?? '') === 'db') echo 'selected'; ?>>DB</option>
                                            <option value='bl' <?php if (($config['frame_finish'] ?? '') === 'bl') echo 'selected'; ?>>BL</option>
                                        </select>
                                    </div>
                                    <div class='mb-3 single-only'>
                                        <label class='form-label'>Hinge Jamb</label>
                                        <select class='form-select frame-part-select' name='hinge_jamb'>
                                            <option value=''>Select Hinge Jamb</option>
                                            <?php foreach ($hinge_jamb_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['hinge_jamb_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 single-only'>
                                        <label class='form-label'>Lock Jamb</label>
                                        <select class='form-select frame-part-select' name='lock_jamb'>
                                            <option value=''>Select Lock Jamb</option>
                                            <?php foreach ($lock_jamb_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['lock_jamb_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 pair-only'>
                                        <label class='form-label'>RH Hinge Jamb</label>
                                        <select class='form-select frame-part-select' name='rh_hinge_jamb'>
                                            <option value=''>Select RH Hinge Jamb</option>
                                            <?php foreach ($hinge_jamb_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['rh_hinge_jamb_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 pair-only'>
                                        <label class='form-label'>LH Hinge Jamb</label>
                                        <select class='form-select frame-part-select' name='lh_hinge_jamb'>
                                            <option value=''>Select LH Hinge Jamb</option>
                                            <?php foreach ($hinge_jamb_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['lh_hinge_jamb_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Door Header</label>
                                        <select class='form-select frame-part-select' name='door_header'>
                                            <option value=''>Select Door Header</option>
                                            <?php foreach ($door_header_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-ly='<?php echo htmlspecialchars($part['ly']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['door_header_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 transom-only'>
                                        <label class='form-label'>Transom Header</label>
                                        <select class='form-select frame-part-select' name='transom_header'>
                                            <option value=''>Select Transom Header</option>
        <?php foreach ($transom_header_parts as $part): ?>
            <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-ly='<?php echo htmlspecialchars($part['ly']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['transom_header_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
        <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label' id='hinge_stop_label'>Hinge Door Stop</label>
                                        <select class='form-select frame-part-select' name='hinge_door_stop'>
                                            <option value='' id='hinge_stop_option'>Select Hinge Door Stop</option>
                                            <?php foreach ($hinge_door_stop_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-ly='<?php echo htmlspecialchars($part['ly']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['hinge_door_stop_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label' id='latch_stop_label'>Latch Door Stop</label>
                                        <select class='form-select frame-part-select' name='latch_door_stop'>
                                            <option value='' id='latch_stop_option'>Select Latch Door Stop</option>
                                            <?php foreach ($latch_door_stop_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-ly='<?php echo htmlspecialchars($part['ly']); ?>' data-lz='<?php echo htmlspecialchars($part['lz']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['latch_door_stop_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <label class='form-label'>Head Door Stop</label>
                                        <select class='form-select frame-part-select' name='head_door_stop'>
                                            <option value=''>Select Head Door Stop</option>
                                            <?php foreach ($head_door_stop_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['head_door_stop_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 transom-only'>
                                        <label class='form-label'>Horizontal Transom Gutter</label>
                                        <select class='form-select frame-part-select' name='horizontal_transom_gutter'>
                                            <option value=''>Select Horizontal Transom Gutter</option>
                                            <?php foreach ($horizontal_transom_gutter_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-ly='<?php echo htmlspecialchars($part['ly']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['horizontal_transom_gutter_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 transom-only'>
                                        <label class='form-label'>Horizontal Transom Stop</label>
                                        <select class='form-select frame-part-select' name='horizontal_transom_stop'>
                                            <option value=''>Select Horizontal Transom Stop</option>
                                            <?php foreach ($horizontal_transom_stop_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-ly='<?php echo htmlspecialchars($part['ly']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['horizontal_transom_stop_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 transom-gutter-conditional'>
                                        <label class='form-label'>Vertical Transom Gutter</label>
                                        <select class='form-select frame-part-select' name='vertical_transom_gutter'>
                                            <option value=''>Select Vertical Transom Gutter</option>
                                            <?php foreach ($vertical_transom_gutter_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['vertical_transom_gutter_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 transom-gutter-conditional'>
                                        <label class='form-label'>Vertical Transom Stop</label>
                                        <select class='form-select frame-part-select' name='vertical_transom_stop'>
                                            <option value=''>Select Vertical Transom Stop</option>
                                            <?php foreach ($vertical_transom_stop_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['vertical_transom_stop_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 transom-only'>
                                        <label class='form-label'>Head Transom Stop</label>
                                        <select class='form-select frame-part-select' name='head_transom_stop'>
                                            <option value=''>Select Head Transom Stop</option>
                                            <?php foreach ($head_transom_stop_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-ly='<?php echo htmlspecialchars($part['ly']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['head_transom_stop_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class='mb-3 transom-only'>
                                        <label class='form-label'>Transom Head Perimeter Filler</label>
                                        <select class='form-select frame-part-select' name='transom_head_perimeter_filler'>
                                            <option value=''>Select Transom Head Perimeter Filler</option>
                                            <?php foreach ($transom_head_perimeter_filler_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>' data-system='<?php echo htmlspecialchars($part['system']); ?>' data-part-number='<?php echo htmlspecialchars($part['part_number']); ?>' data-category='frame' <?php if (($config['transom_head_perimeter_filler_id'] ?? '') == $part['id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class='tab-pane fade' id='hardware' role='tabpanel'>
                                    <p>Hardware configuration coming soon.</p>
                                </div>
                                <div class='tab-pane fade' id='cutlist' role='tabpanel'>
                                    <table class='table'>
                                        <thead><tr><th>Part</th><th>Part Number</th><th>Cut Length</th></tr></thead>
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
                                        <p class='pair-only'><strong>Inactive Hinge Rail:</strong> <span id='summary-hinge2'></span></p>
                                        <p class='pair-only'><strong>Inactive Lock Rail:</strong> <span id='summary-lock2'></span></p>
                                        <p class='pair-only'><strong>Inactive Top Rail:</strong> <span id='summary-top2'></span></p>
                                        <p class='pair-only'><strong>Inactive Bottom Rail:</strong> <span id='summary-bottom2'></span></p>
                                        <h6 class='mt-4'>Parts Summary</h6>
                                        <table class='table'>
                                            <thead><tr><th>Type</th><th>Part Number</th><th>Description</th><th>Qty</th><th>Stock Lengths</th></tr></thead>
                                            <tbody id='summary-parts-body'></tbody>
                                        </table>
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
function updateTransomFields() {
    var hasTransom = document.getElementById('has_transom').checked;
    document.getElementById('frame_height').disabled = !hasTransom;
    document.querySelectorAll('.transom-only').forEach(function(el){
        el.style.display = hasTransom ? '' : 'none';
    });
    updateVerticalTransomGutterVisibility();
}
document.getElementById('has_transom').addEventListener('change', updateTransomFields);

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
var hingeStopLabelEl = document.getElementById('hinge_stop_label');
var latchStopLabelEl = document.getElementById('latch_stop_label');
var hingeStopOptionEl = document.getElementById('hinge_stop_option');
var latchStopOptionEl = document.getElementById('latch_stop_option');
var inactiveDoorTabItem = document.getElementById('inactive-door-tab-item');
var activeDoorTabBtn = document.getElementById('active-door-tab');
function updateHanding() {
    var isPair = handingSelect.value.startsWith('pair');
    inactiveDoorTabItem.style.display = isPair ? '' : 'none';
    if (!isPair) {
        var tab = new bootstrap.Tab(activeDoorTabBtn);
        tab.show();
    }
    document.querySelectorAll('.single-only').forEach(function(el){
        el.style.display = isPair ? 'none' : '';
    });
    document.querySelectorAll('.pair-only').forEach(function(el){
        el.style.display = isPair ? '' : 'none';
    });
    if (isPair) {
        hingeStopLabelEl.textContent = 'RH Hinge Stop';
        hingeStopOptionEl.textContent = 'Select RH Hinge Stop';
        latchStopLabelEl.textContent = 'LH Hinge Stop';
        latchStopOptionEl.textContent = 'Select LH Hinge Stop';
    } else {
        hingeStopLabelEl.textContent = 'Hinge Door Stop';
        hingeStopOptionEl.textContent = 'Select Hinge Door Stop';
        latchStopLabelEl.textContent = 'Latch Door Stop';
        latchStopOptionEl.textContent = 'Select Latch Door Stop';
    }
}
handingSelect.addEventListener('change', updateHanding);

function updateVerticalTransomGutterVisibility() {
    var glazing = document.querySelector("select[name='glazing']").value;
    var hasTransom = document.getElementById('has_transom').checked;
    var show = hasTransom && glazing !== '1/4' && glazing !== '3/8';
    document.querySelectorAll('.transom-gutter-conditional').forEach(function(el){
        el.style.display = show ? '' : 'none';
    });
}
document.querySelector("select[name='glazing']").addEventListener('change', updateVerticalTransomGutterVisibility);
updateHanding();
updateTransomFields();

var frameSystemSelect = document.querySelector("select[name='frame_system']");
var framePartSelects = document.querySelectorAll('#frame .frame-part-select');
function filterFrameParts(){
    var sys = frameSystemSelect.value;
    framePartSelects.forEach(function(sel){
        Array.from(sel.options).forEach(function(opt, idx){
            if (idx === 0) return;
            opt.hidden = sys && opt.dataset.system !== sys;
        });
        if (sel.value && sel.querySelector("option[value='" + sel.value + "']").hidden) {
            sel.value = '';
        }
    });
}
frameSystemSelect.addEventListener('change', filterFrameParts);
filterFrameParts();

var presetSelect = document.getElementById('preset_select');
if (presetSelect) {
    presetSelect.addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (!opt) return;
        var hinge = opt.dataset.hinge || '';
        var lock = opt.dataset.lock || '';
        var top = opt.dataset.top || '';
        var bottom = opt.dataset.bottom || '';
        if (hinge) document.querySelector("select[name='hinge_rail']").value = hinge;
        if (lock) document.querySelector("select[name='lock_rail']").value = lock;
        if (top) document.querySelector("select[name='top_rail']").value = top;
        if (bottom) document.querySelector("select[name='bottom_rail']").value = bottom;
    });
}

var presetSelect2 = document.getElementById('preset_select_2');
if (presetSelect2) {
    presetSelect2.addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (!opt) return;
        var hinge = opt.dataset.hinge || '';
        var lock = opt.dataset.lock || '';
        var top = opt.dataset.top || '';
        var bottom = opt.dataset.bottom || '';
        if (hinge) document.querySelector("select[name='hinge_rail_2']").value = hinge;
        if (lock) document.querySelector("select[name='lock_rail_2']").value = lock;
        if (top) document.querySelector("select[name='top_rail_2']").value = top;
        if (bottom) document.querySelector("select[name='bottom_rail_2']").value = bottom;
    });
}

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
summaryTab.addEventListener('shown.bs.tab', async function () {
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

    var width = parseFloat(document.querySelector("input[name='opening_width']").value) || 0;
    var height = parseFloat(document.querySelector("input[name='opening_height']").value) || 0;
    var topGap = parseFloat(document.getElementById('top_gap').value) || 0;
    var bottomGap = parseFloat(document.getElementById('bottom_gap').value) || 0;
    var hingeGap = parseFloat(document.getElementById('hinge_gap').value) || 0;
    var latchGap = parseFloat(document.getElementById('latch_gap').value) || 0;
    var hingeSelect = document.querySelector("select[name='hinge_rail']");
    var lockSelect = document.querySelector("select[name='lock_rail']");
    var hingeLz = parseFloat(hingeSelect && hingeSelect.selectedOptions.length ? hingeSelect.selectedOptions[0].dataset.lz || 0 : 0) || 0;
    var lockLz = parseFloat(lockSelect && lockSelect.selectedOptions.length ? lockSelect.selectedOptions[0].dataset.lz || 0 : 0) || 0;
    var railCut = width - hingeGap - latchGap - hingeLz - lockLz;
    var stileCut = height - topGap - bottomGap;
    var frameHeight = parseFloat(document.querySelector("input[name='frame_height']").value) || 0;
    var hasTransom = document.getElementById('has_transom').checked;
    var doorHeadSelect = document.querySelector("select[name='door_header']");
    var doorHeadLy = parseFloat(doorHeadSelect && doorHeadSelect.selectedOptions.length ? doorHeadSelect.selectedOptions[0].dataset.ly || 0 : 0) || 0;
    var doorHeadLz = parseFloat(doorHeadSelect && doorHeadSelect.selectedOptions.length ? doorHeadSelect.selectedOptions[0].dataset.lz || 0 : 0) || 0;
    var jambLength = hasTransom ? frameHeight : (height + doorHeadLz);
    var jambStopLength = height;
    var hingeStopSel = document.querySelector("select[name='hinge_door_stop']");
    var latchStopSel = document.querySelector("select[name='latch_door_stop']");
    var headStopSel = document.querySelector("select[name='head_door_stop']");
    var hingeStopLz = parseFloat(hingeStopSel && hingeStopSel.selectedOptions.length ? hingeStopSel.selectedOptions[0].dataset.lz || 0 : 0) || 0;
    var latchStopLz = parseFloat(latchStopSel && latchStopSel.selectedOptions.length ? latchStopSel.selectedOptions[0].dataset.lz || 0 : 0) || 0;
    var headStopLen = width - hingeStopLz - latchStopLz;

    var parts = [];
    function addPartByName(name, length){
        var sel = document.querySelector("select[name='" + name + "']");
        if(sel && sel.value){
            var opt = sel.selectedOptions[0];
            parts.push({id: sel.value, part_number: opt.dataset.partNumber, type: opt.dataset.category, description: opt.text, quantity:1, length:length});
        }
    }
    addPartByName('top_rail', railCut);
    addPartByName('bottom_rail', railCut);
    addPartByName('hinge_rail', stileCut);
    addPartByName('lock_rail', stileCut);
    if(handingSelect.value.startsWith('pair')){
        var hingeSelect2 = document.querySelector("select[name='hinge_rail_2']");
        var lockSelect2 = document.querySelector("select[name='lock_rail_2']");
        var hingeLz2 = parseFloat(hingeSelect2 && hingeSelect2.selectedOptions.length ? hingeSelect2.selectedOptions[0].dataset.lz || 0 : 0) || 0;
        var lockLz2 = parseFloat(lockSelect2 && lockSelect2.selectedOptions.length ? lockSelect2.selectedOptions[0].dataset.lz || 0 : 0) || 0;
        var railCut2 = width - hingeGap - latchGap - hingeLz2 - lockLz2;
        addPartByName('hinge_rail_2', stileCut);
        addPartByName('lock_rail_2', stileCut);
        addPartByName('top_rail_2', railCut2);
        addPartByName('bottom_rail_2', railCut2);
    }
    addPartByName('hinge_jamb', jambLength);
    addPartByName('lock_jamb', jambLength);
    addPartByName('rh_hinge_jamb', jambLength);
    addPartByName('lh_hinge_jamb', jambLength);
    addPartByName('door_header', width);
    addPartByName('hinge_door_stop', jambStopLength);
    addPartByName('latch_door_stop', jambStopLength);
    addPartByName('head_door_stop', headStopLen);
    if(hasTransom){
        var horizStopSel = document.querySelector("select[name='horizontal_transom_stop']");
        var horizGutterSel = document.querySelector("select[name='horizontal_transom_gutter']");
        var vertStopSel = document.querySelector("select[name='vertical_transom_stop']");
        var vertGutterSel = document.querySelector("select[name='vertical_transom_gutter']");
        var headTransomStopSel = document.querySelector("select[name='head_transom_stop']");
        var transomHeadFillerSel = document.querySelector("select[name='transom_head_perimeter_filler']");
        var horizStopLy = parseFloat(horizStopSel && horizStopSel.selectedOptions.length ? horizStopSel.selectedOptions[0].dataset.ly || 0 : 0) || 0;
        var headTransomStopLy = parseFloat(headTransomStopSel && headTransomStopSel.selectedOptions.length ? headTransomStopSel.selectedOptions[0].dataset.ly || 0 : 0) || 0;
        var verticalLen = frameHeight - height - doorHeadLy - horizStopLy - headTransomStopLy;
        addPartByName('horizontal_transom_stop', width);
        addPartByName('horizontal_transom_gutter', width);
        addPartByName('head_transom_stop', width);
        addPartByName('transom_head_perimeter_filler', width);
        addPartByName('vertical_transom_stop', verticalLen);
        addPartByName('vertical_transom_gutter', verticalLen);
    }
    var base = parts.slice();
    await Promise.all(base.map(function(p){
        return fetch('get_required_parts.php?id=' + encodeURIComponent(p.id))
            .then(r => r.json())
            .then(function(reqs){
                reqs.forEach(function(r){
                    parts.push({id:r.id, part_number:r.part_number, type:r.category, description:r.manufacturer + ' ' + r.system + ' ' + r.part_number, quantity:r.quantity});
                });
            });
    }));
    var grouped = {};
    parts.forEach(function(p){
        if(!grouped[p.part_number]){
            grouped[p.part_number] = {part_number:p.part_number, description:p.description, type:p.type, quantity:0, totalLength:0};
        }
        grouped[p.part_number].quantity += p.quantity;
        if(p.length){ grouped[p.part_number].totalLength += p.length * p.quantity; }
    });
    var rows = Object.values(grouped).sort(function(a,b){
        return a.type.localeCompare(b.type) || a.part_number.localeCompare(b.part_number);
    }).map(function(p){
        var stockLen = p.type === 'frame' ? 288 : (p.type === 'door' ? 252 : null);
        var stockQty = stockLen ? Math.ceil(p.totalLength / stockLen) : '';
        return '<tr><td>' + (p.type.charAt(0).toUpperCase() + p.type.slice(1)) + '</td><td>' + p.part_number + '</td><td>' + p.description + '</td><td>' + p.quantity + '</td><td>' + stockQty + '</td></tr>';
    }).join('');
    document.getElementById('summary-parts-body').innerHTML = rows;
});

var cutlistTab = document.getElementById('cutlist-tab');
cutlistTab.addEventListener('shown.bs.tab', function(){
    var width = parseFloat(document.querySelector("input[name='opening_width']").value) || 0;
    var height = parseFloat(document.querySelector("input[name='opening_height']").value) || 0;
    var isPair = document.getElementById('handing').value.startsWith('pair');
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
    var frameHeight = parseFloat(document.querySelector("input[name='frame_height']").value) || 0;
    var hasTransom = document.getElementById('has_transom').checked;
    var body = document.getElementById('cutlist-body');
    body.innerHTML = '';
    function addRow(label, sel, len){
        if(sel && sel.value && !isNaN(len)){
            var pn = sel.selectedOptions[0].dataset.partNumber || '';
            body.innerHTML += '<tr><td>' + label + '</td><td>' + pn + '</td><td>' + len.toFixed(3) + '</td></tr>';
        }
    }
    addRow('Top Rail', document.querySelector("select[name='top_rail']"), railCut);
    addRow('Bottom Rail', document.querySelector("select[name='bottom_rail']"), railCut);
    addRow('Hinge Rail', hingeSelect, stileCut);
    addRow('Lock Rail', lockSelect, stileCut);
    if(handingSelect.value.startsWith('pair')){
        var hingeSelect2 = document.querySelector("select[name='hinge_rail_2']");
        var lockSelect2 = document.querySelector("select[name='lock_rail_2']");
        var hingeLz2 = parseFloat(hingeSelect2 && hingeSelect2.selectedOptions.length ? hingeSelect2.selectedOptions[0].dataset.lz || 0 : 0) || 0;
        var lockLz2 = parseFloat(lockSelect2 && lockSelect2.selectedOptions.length ? lockSelect2.selectedOptions[0].dataset.lz || 0 : 0) || 0;
        var railCut2 = width - hingeGap - latchGap - hingeLz2 - lockLz2;
        addRow('Inactive Hinge Rail', hingeSelect2, stileCut);
        addRow('Inactive Lock Rail', lockSelect2, stileCut);
        addRow('Inactive Top Rail', document.querySelector("select[name='top_rail_2']"), railCut2);
        addRow('Inactive Bottom Rail', document.querySelector("select[name='bottom_rail_2']"), railCut2);
    }

    var doorHeadSelect = document.querySelector("select[name='door_header']");
    var doorHeadLy = parseFloat(doorHeadSelect && doorHeadSelect.selectedOptions.length ? doorHeadSelect.selectedOptions[0].dataset.ly || 0 : 0) || 0;
    var doorHeadLz = parseFloat(doorHeadSelect && doorHeadSelect.selectedOptions.length ? doorHeadSelect.selectedOptions[0].dataset.lz || 0 : 0) || 0;
    var jambLength = hasTransom ? frameHeight : (height + doorHeadLz);
    addRow('Hinge Jamb', document.querySelector("select[name='hinge_jamb']"), jambLength);
    addRow('Lock Jamb', document.querySelector("select[name='lock_jamb']"), jambLength);
    addRow('RH Hinge Jamb', document.querySelector("select[name='rh_hinge_jamb']"), jambLength);
    addRow('LH Hinge Jamb', document.querySelector("select[name='lh_hinge_jamb']"), jambLength);
    addRow('Door Header', doorHeadSelect, width);
    var jambStopLength = height;
    var hingeStopSel = document.querySelector("select[name='hinge_door_stop']");
    var lockStopSel = document.querySelector("select[name='latch_door_stop']");
    var headStopSel = document.querySelector("select[name='head_door_stop']");
    var hingeStopLabel = isPair ? 'RH Hinge Stop' : 'Hinge Door Stop';
    var lockStopLabel = isPair ? 'LH Hinge Stop' : 'Latch Door Stop';
    addRow(hingeStopLabel, hingeStopSel, jambStopLength);
    addRow(lockStopLabel, lockStopSel, jambStopLength);
    var hingeStopLz = parseFloat(hingeStopSel && hingeStopSel.selectedOptions.length ? hingeStopSel.selectedOptions[0].dataset.lz || 0 : 0) || 0;
    var lockStopLz = parseFloat(lockStopSel && lockStopSel.selectedOptions.length ? lockStopSel.selectedOptions[0].dataset.lz || 0 : 0) || 0;
    var headStopLen = width - hingeStopLz - lockStopLz;
    addRow('Head Door Stop', headStopSel, headStopLen);
    if(hasTransom){
        var horizStopSel = document.querySelector("select[name='horizontal_transom_stop']");
        var horizGutterSel = document.querySelector("select[name='horizontal_transom_gutter']");
        var vertStopSel = document.querySelector("select[name='vertical_transom_stop']");
        var vertGutterSel = document.querySelector("select[name='vertical_transom_gutter']");
        var headTransomStopSel = document.querySelector("select[name='head_transom_stop']");
        var transomHeadFillerSel = document.querySelector("select[name='transom_head_perimeter_filler']");
        var horizStopLy = parseFloat(horizStopSel && horizStopSel.selectedOptions.length ? horizStopSel.selectedOptions[0].dataset.ly || 0 : 0) || 0;
        var headTransomStopLy = parseFloat(headTransomStopSel && headTransomStopSel.selectedOptions.length ? headTransomStopSel.selectedOptions[0].dataset.ly || 0 : 0) || 0;
        var verticalLen = frameHeight - height - doorHeadLy - horizStopLy - headTransomStopLy;
        addRow('Horizontal Transom Stop', horizStopSel, width);
        addRow('Horizontal Transom Gutter', horizGutterSel, width);
        addRow('Head Transom Stop', headTransomStopSel, width);
        addRow('Transom Head Perimeter Filler', transomHeadFillerSel, width);
        addRow('Vertical Transom Stop', vertStopSel, verticalLen);
        addRow('Vertical Transom Gutter', vertGutterSel, verticalLen);
    }
});
</script>
<?php include 'includes/footer.php'; ?>

