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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $functions = isset($_POST['functions']) ? $_POST['functions'] : [];
    $primary_function = $functions[0] ?? null;
    $stmt = $pdo->prepare('INSERT INTO door_parts (manufacturer, system, part_number, lx, ly, lz, function, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $_POST['manufacturer'],
        $_POST['system'],
        $_POST['part_number'],
        $_POST['lx'] !== '' ? $_POST['lx'] : null,
        $_POST['ly'] !== '' ? $_POST['ly'] : null,
        $_POST['lz'] !== '' ? $_POST['lz'] : null,
        $primary_function,
        $_POST['category']
    ]);
    $part_id = $pdo->lastInsertId();
    if (!empty($functions)) {
        $func_stmt = $pdo->prepare('INSERT INTO door_part_functions (part_id, function) VALUES (?, ?)');
        foreach ($functions as $func) {
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
}

$parts_stmt = $pdo->query('SELECT id, manufacturer, system, part_number FROM door_parts ORDER BY manufacturer, system, part_number');
$existing_parts = $parts_stmt->fetchAll();
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
                            <h6 class='mb-4'>Add Door Part</h6>
                            <form method='post'>
                                <div class='mb-3'>
                                    <label class='form-label'>Manufacturer</label>
                                    <select class='form-select' name='manufacturer' id='manufacturer' required>
                                        <option value=''>Select Manufacturer</option>
                                        <?php foreach ($manufacturers as $m): ?>
                                            <option value='<?php echo htmlspecialchars($m['name']); ?>'><?php echo htmlspecialchars($m['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>System</label>
                                    <select class='form-select' name='system' id='system' required disabled>
                                        <option value=''>Select Manufacturer First</option>
                                    </select>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Part Number</label>
                                    <input type='text' class='form-control' name='part_number' required>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>LX</label>
                                    <input type='number' step='any' class='form-control' name='lx'>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>LY</label>
                                    <input type='number' step='any' class='form-control' name='ly'>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>LZ</label>
                                    <input type='number' step='any' class='form-control' name='lz'>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Category</label>
                                    <select class='form-select' name='category' required>
                                        <option value='frame'>Frame Part</option>
                                        <option value='fastener'>Fastener</option>
                                        <option value='accessory'>Accessory</option>
                                        <option value='door'>Door Part</option>
                                    </select>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Function</label>
                                    <select class='form-select' name='functions[]' multiple required>
                                        <option value='hinge_rail'>Hinge Rail</option>
                                        <option value='lock_rail'>Lock Rail</option>
                                        <option value='top_rail'>Top Rail</option>
                                        <option value='bottom_rail'>Bottom Rail</option>
                                        <option value='tie_rod'>Tie Rod</option>
                                        <option value='door_lug'>Door Lug</option>
                                        <option value='midrail'>Midrail</option>
                                        <option value='glass_stop'>Glass Stop</option>
                                        <option value='glazing_vinyl'>Glazing Vinyl</option>
                                        <option value='glazing_accessory'>Glazing Accessory</option>
                                    </select>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Requirements</label>
                                    <div id='requirements'>
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
                                <button type='submit' class='btn btn-primary'>Add Part</button>
                            </form>
                            <script>
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
                                        });
                                }
                                document.getElementById('manufacturer').addEventListener('change', loadSystems);
                                loadSystems();

                                document.getElementById('addRequirement').addEventListener('click', function () {
                                    var container = document.getElementById('requirements');
                                    var template = container.querySelector('.requirement').cloneNode(true);
                                    template.querySelector('select').selectedIndex = 0;
                                    template.querySelector('input').value = 1;
                                    container.appendChild(template);
                                });
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

