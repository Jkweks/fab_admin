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

// Fetch parts for each function
$hinge_parts = $pdo->query("SELECT id, manufacturer, system, part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id=dpf.part_id WHERE dpf.function='hinge_rail' ORDER BY manufacturer, system, part_number")->fetchAll();
$lock_parts = $pdo->query("SELECT id, manufacturer, system, part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id=dpf.part_id WHERE dpf.function='lock_rail' ORDER BY manufacturer, system, part_number")->fetchAll();
$top_parts = $pdo->query("SELECT id, manufacturer, system, part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id=dpf.part_id WHERE dpf.function='top_rail' ORDER BY manufacturer, system, part_number")->fetchAll();
$bottom_parts = $pdo->query("SELECT id, manufacturer, system, part_number FROM door_parts dp JOIN door_part_functions dpf ON dp.id=dpf.part_id WHERE dpf.function='bottom_rail' ORDER BY manufacturer, system, part_number")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO door_part_presets (name, hinge_rail_id, lock_rail_id, top_rail_id, bottom_rail_id) VALUES (?,?,?,?,?)');
    $stmt->execute([
        $_POST['name'],
        $_POST['hinge_rail'] !== '' ? $_POST['hinge_rail'] : null,
        $_POST['lock_rail'] !== '' ? $_POST['lock_rail'] : null,
        $_POST['top_rail'] !== '' ? $_POST['top_rail'] : null,
        $_POST['bottom_rail'] !== '' ? $_POST['bottom_rail'] : null,
    ]);
}

// Fetch existing presets
$presets = $pdo->query("SELECT dpp.*, 
        hr.manufacturer || ' ' || hr.system || ' ' || hr.part_number AS hinge,
        lr.manufacturer || ' ' || lr.system || ' ' || lr.part_number AS lock,
        tr.manufacturer || ' ' || tr.system || ' ' || tr.part_number AS top,
        br.manufacturer || ' ' || br.system || ' ' || br.part_number AS bottom
    FROM door_part_presets dpp
    LEFT JOIN door_parts hr ON dpp.hinge_rail_id = hr.id
    LEFT JOIN door_parts lr ON dpp.lock_rail_id = lr.id
    LEFT JOIN door_parts tr ON dpp.top_rail_id = tr.id
    LEFT JOIN door_parts br ON dpp.bottom_rail_id = br.id
    ORDER BY dpp.name")->fetchAll();
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
                        <h6 class='mb-4'>Door Part Presets</h6>
                        <form method='post' class='mb-4'>
                            <div class='mb-3'>
                                <label class='form-label'>Preset Name</label>
                                <input type='text' class='form-control' name='name' required>
                            </div>
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
                            <button type='submit' class='btn btn-primary'>Add Preset</button>
                        </form>
                        <h6 class='mb-3'>Existing Presets</h6>
                        <table class='table'>
                            <thead>
                                <tr><th>Name</th><th>Hinge Rail</th><th>Lock Rail</th><th>Top Rail</th><th>Bottom Rail</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($presets as $preset): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($preset['name']); ?></td>
                                        <td><?php echo htmlspecialchars($preset['hinge']); ?></td>
                                        <td><?php echo htmlspecialchars($preset['lock']); ?></td>
                                        <td><?php echo htmlspecialchars($preset['top']); ?></td>
                                        <td><?php echo htmlspecialchars($preset['bottom']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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

