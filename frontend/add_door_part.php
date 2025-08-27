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
    $stmt = $pdo->prepare('INSERT INTO door_parts (manufacturer, system, part_number, lx, ly, lz, function) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $_POST['manufacturer'],
        $_POST['system'],
        $_POST['part_number'],
        $_POST['lx'] !== '' ? $_POST['lx'] : null,
        $_POST['ly'] !== '' ? $_POST['ly'] : null,
        $_POST['lz'] !== '' ? $_POST['lz'] : null,
        $_POST['function']
    ]);
    $part_id = $pdo->lastInsertId();
    if (!empty($_POST['requires'])) {
        $req_stmt = $pdo->prepare('INSERT INTO door_part_requirements (part_id, required_part_id) VALUES (?, ?)');
        foreach ($_POST['requires'] as $req) {
            $req_stmt->execute([$part_id, $req]);
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
                                    <input type='text' class='form-control' name='manufacturer' required>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>System</label>
                                    <input type='text' class='form-control' name='system' required>
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
                                    <label class='form-label'>Function</label>
                                    <select class='form-select' name='function' required>
                                        <option value='hinge_rail'>Hinge Rail</option>
                                        <option value='lock_rail'>Lock Rail</option>
                                        <option value='top_rail'>Top Rail</option>
                                        <option value='bottom_rail'>Bottom Rail</option>
                                    </select>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label'>Requires</label>
                                    <select class='form-select' name='requires[]' multiple>
                                        <?php foreach ($existing_parts as $part): ?>
                                            <option value='<?php echo htmlspecialchars($part['id']); ?>'><?php echo htmlspecialchars($part['manufacturer'] . ' ' . $part['system'] . ' ' . $part['part_number']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class='form-text text-muted'>Hold Ctrl (Cmd on Mac) to select multiple parts.</small>
                                </div>
                                <button type='submit' class='btn btn-primary'>Add Part</button>
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

