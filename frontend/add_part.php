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

$manufacturers = $pdo->query('SELECT name FROM manufacturers ORDER BY name')->fetchAll();
$parts_stmt = $pdo->query('SELECT id, manufacturer, system, part_number FROM door_parts ORDER BY manufacturer, system, part_number');
$existing_parts = $parts_stmt->fetchAll();

$edit_mode = false;
$part_data = null;
$existing_functions = [];
$existing_requirements = [];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM door_parts WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $part_data = $stmt->fetch();
    if ($part_data) {
        $edit_mode = true;
        $func_stmt = $pdo->prepare('SELECT function FROM door_part_functions WHERE part_id = ?');
        $func_stmt->execute([$part_data['id']]);
        $existing_functions = $func_stmt->fetchAll(PDO::FETCH_COLUMN);
        $req_stmt = $pdo->prepare('SELECT required_part_id, quantity FROM door_part_requirements WHERE part_id = ?');
        $req_stmt->execute([$part_data['id']]);
        $existing_requirements = $req_stmt->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $lx = $ly = $lz = null;
    $function = null;
    $functions_selected = [];

    switch ($category) {
        case 'frame':
            $lx = $_POST['lx'] !== '' ? $_POST['lx'] : null;
            $ly = $_POST['ly'] !== '' ? $_POST['ly'] : null;
            $lz = $_POST['lz'] !== '' ? $_POST['lz'] : null;
            $function = $_POST['frame_function'] ?? 'frame';
            $functions_selected = [$function];
            break;
        case 'fastener':
            $lx = $_POST['length'] !== '' ? $_POST['length'] : null;
            $ly = $_POST['diameter'] !== '' ? $_POST['diameter'] : null;
            $function = 'fastener';
            break;
        case 'door':
            $lx = $_POST['lx'] !== '' ? $_POST['lx'] : null;
            $ly = $_POST['ly'] !== '' ? $_POST['ly'] : null;
            $lz = $_POST['lz'] !== '' ? $_POST['lz'] : null;
            $functions_selected = isset($_POST['functions']) ? $_POST['functions'] : [];
            $function = $functions_selected[0] ?? 'door';
            break;
        case 'accessory':
            $function = 'accessory';
            break;
    }

    if (!empty($_POST['id'])) {
        $part_id = $_POST['id'];
        $stmt = $pdo->prepare('UPDATE door_parts SET manufacturer = ?, system = ?, part_number = ?, lx = ?, ly = ?, lz = ?, function = ?, category = ? WHERE id = ?');
        $stmt->execute([
            $_POST['manufacturer'],
            $_POST['system'],
            $_POST['part_number'],
            $lx,
            $ly,
            $lz,
            $function,
            $category,
            $part_id
        ]);
        $pdo->prepare('DELETE FROM door_part_functions WHERE part_id = ?')->execute([$part_id]);
        $pdo->prepare('DELETE FROM door_part_requirements WHERE part_id = ?')->execute([$part_id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO door_parts (manufacturer, system, part_number, lx, ly, lz, function, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $_POST['manufacturer'],
            $_POST['system'],
            $_POST['part_number'],
            $lx,
            $ly,
            $lz,
            $function,
            $category
        ]);
        $part_id = $pdo->lastInsertId();
    }

    if (!empty($functions_selected)) {
        $func_stmt = $pdo->prepare('INSERT INTO door_part_functions (part_id, function) VALUES (?, ?)');
        foreach ($functions_selected as $func) {
            $func_stmt->execute([$part_id, $func]);
        }
    }
    if (!empty($_POST['required_parts'])) {
        $req_stmt = $pdo->prepare('INSERT INTO door_part_requirements (part_id, required_part_id, quantity) VALUES (?, ?, ?)');
        foreach ($_POST['required_parts'] as $index => $req) {
            if ($req !== '') {
                $qty = isset($_POST['required_quantities'][$index]) && $_POST['required_quantities'][$index] !== '' ? $_POST['required_quantities'][$index] : 1;
                $req_stmt->execute([$part_id, $req, $qty]);
            }
        }
    }

    $stmt = $pdo->prepare('SELECT * FROM door_parts WHERE id = ?');
    $stmt->execute([$part_id]);
    $part_data = $stmt->fetch();
    $func_stmt = $pdo->prepare('SELECT function FROM door_part_functions WHERE part_id = ?');
    $func_stmt->execute([$part_id]);
    $existing_functions = $func_stmt->fetchAll(PDO::FETCH_COLUMN);
    $req_stmt = $pdo->prepare('SELECT required_part_id, quantity FROM door_part_requirements WHERE part_id = ?');
    $req_stmt->execute([$part_id]);
    $existing_requirements = $req_stmt->fetchAll();
    $edit_mode = true;
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
                        <h6 class='mb-4'><?php echo $edit_mode ? 'Edit Part' : 'Add Part'; ?></h6>
                        <form method='post'>
                            <?php if ($edit_mode && isset($part_data['id'])): ?>
                                <input type='hidden' name='id' value='<?php echo htmlspecialchars($part_data['id']); ?>'>
                            <?php endif; ?>
                            <div class='mb-3'>
                                <label class='form-label'>Manufacturer</label>
                                <select class='form-select' name='manufacturer' id='manufacturer' required>
                                    <option value=''>Select Manufacturer</option>
                                    <?php foreach ($manufacturers as $m): ?>
                                        <option value='<?php echo htmlspecialchars($m['name']); ?>' <?php if ($part_data && $part_data['manufacturer'] === $m['name']) echo 'selected'; ?>><?php echo htmlspecialchars($m['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label'>System</label>
                                <select class='form-select' name='system' id='system' required disabled data-current='<?php echo htmlspecialchars($part_data['system'] ?? ''); ?>'>
                                    <option value=''>Select Manufacturer First</option>
                                </select>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label'>Category</label>
                                <select class='form-select' name='category' id='category' required>
                                    <option value='frame' <?php if ($part_data && $part_data['category'] === 'frame') echo 'selected'; ?>>Frame Part</option>
                                    <option value='fastener' <?php if ($part_data && $part_data['category'] === 'fastener') echo 'selected'; ?>>Fastener</option>
                                    <option value='accessory' <?php if ($part_data && $part_data['category'] === 'accessory') echo 'selected'; ?>>Accessory</option>
                                    <option value='door' <?php if ($part_data && $part_data['category'] === 'door') echo 'selected'; ?>>Door Part</option>
                                </select>
                            </div>
                            <div id='categoryFields'></div>
                            <div class='mb-3'>
                                <label class='form-label'>Part Number</label>
                                <input type='text' class='form-control' name='part_number' value='<?php echo htmlspecialchars($part_data['part_number'] ?? ''); ?>' required>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label'>Requirements</label>
                                <div id='requirements'>
                                    <?php if ($edit_mode && !empty($existing_requirements)): ?>
                                        <?php foreach ($existing_requirements as $req): ?>
                                            <div class='requirement d-flex mb-2'>
                                                <select class='form-select me-2' name='required_parts[]'>
                                                    <option value=''>Select Required Part</option>
                                                    <?php foreach ($existing_parts as $part): ?>
                                                        <option value='<?php echo htmlspecialchars($part['id']); ?>' <?php if ($part['id'] == $req['required_part_id']) echo 'selected'; ?>><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type='number' class='form-control' name='required_quantities[]' min='1' value='<?php echo htmlspecialchars($req['quantity']); ?>'>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <div class='requirement d-flex mb-2'>
                                        <select class='form-select me-2' name='required_parts[]'>
                                            <option value=''>Select Required Part</option>
                                            <?php foreach ($existing_parts as $part): ?>
                                                <option value='<?php echo htmlspecialchars($part['id']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type='number' class='form-control' name='required_quantities[]' min='1' value='1'>
                                    </div>
                                </div>
                                <button type='button' class='btn btn-sm btn-secondary' id='addRequirement'>Add Requirement</button>
                            </div>
                            <button type='submit' class='btn btn-primary'><?php echo $edit_mode ? 'Update Part' : 'Add Part'; ?></button>
                        </form>
                        <script>
                            function loadCategoryFields() {
                                var category = document.getElementById('category').value;
                                fetch('includes/parts/' + category + '_fields.php')
                                    .then(response => response.text())
                                    .then(html => {
                                        document.getElementById('categoryFields').innerHTML = html;
                                        if (editMode) {
                                            if (category === 'frame' || category === 'door') {
                                                document.querySelector('input[name="lx"]').value = currentLx;
                                                document.querySelector('input[name="ly"]').value = currentLy;
                                                var lzField = document.querySelector('input[name="lz"]');
                                                if (lzField) lzField.value = currentLz;
                                            } else if (category === 'fastener') {
                                                document.querySelector('input[name="length"]').value = currentLx;
                                                document.querySelector('input[name="diameter"]').value = currentLy;
                                            }
                                            if (category === 'door') {
                                                var select = document.querySelector('select[name="functions[]"]');
                                                currentFunctions.forEach(function(f) {
                                                    var option = Array.from(select.options).find(o => o.value === f);
                                                    if (option) option.selected = true;
                                                });
                                            }
                                            if (category === 'frame') {
                                                var fSelect = document.querySelector('select[name="frame_function"]');
                                                if (fSelect && currentFunctions[0]) {
                                                    fSelect.value = currentFunctions[0];
                                                }
                                            }
                                        }
                                    });
                            }
                            function loadSystems() {
                                var manufacturer = document.getElementById('manufacturer').value;
                                var systemSelect = document.getElementById('system');
                                if (!manufacturer) {
                                    systemSelect.innerHTML = "<option value=''>Select Manufacturer First</option>";
                                    systemSelect.disabled = true;
                                    return;
                                }
                                fetch('get_systems.php?manufacturer=' + encodeURIComponent(manufacturer))
                                    .then(response => response.text())
                                    .then(html => {
                                        systemSelect.innerHTML = html;
                                        systemSelect.disabled = false;
                                        if (editMode && currentSystem) {
                                            systemSelect.value = currentSystem;
                                        }
                                    });
                            }
                            document.getElementById('addRequirement').addEventListener('click', function () {
                                var container = document.getElementById('requirements');
                                var template = container.querySelector('.requirement').cloneNode(true);
                                template.querySelector('select').selectedIndex = 0;
                                template.querySelector('input').value = 1;
                                container.appendChild(template);
                            });
                            document.getElementById('manufacturer').addEventListener('change', loadSystems);
                            document.getElementById('category').addEventListener('change', loadCategoryFields);
                            var editMode = <?php echo $edit_mode ? 'true' : 'false'; ?>;
                            var currentSystem = <?php echo json_encode($part_data['system'] ?? ''); ?>;
                            var currentLx = <?php echo json_encode($part_data['lx'] ?? ''); ?>;
                            var currentLy = <?php echo json_encode($part_data['ly'] ?? ''); ?>;
                            var currentLz = <?php echo json_encode($part_data['lz'] ?? ''); ?>;
                            var currentFunctions = <?php echo json_encode($existing_functions); ?>;
                            loadSystems();
                            loadCategoryFields();
                        </script>
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
