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
    $category = $_POST['category'];
    $lx = $ly = $lz = null;
    $function = null;

    switch ($category) {
        case 'frame':
            $lx = $_POST['lx'] !== '' ? $_POST['lx'] : null;
            $ly = $_POST['ly'] !== '' ? $_POST['ly'] : null;
            $lz = $_POST['lz'] !== '' ? $_POST['lz'] : null;
            $function = 'frame';
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
            $function = $_POST['function'] ?? 'door';
            break;
        case 'accessory':
            $function = 'accessory';
            break;
    }

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
                        <h6 class='mb-4'>Add Part</h6>
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
                                <label class='form-label'>Category</label>
                                <select class='form-select' name='category' id='category' required>
                                    <option value='frame'>Frame Part</option>
                                    <option value='fastener'>Fastener</option>
                                    <option value='accessory'>Accessory</option>
                                    <option value='door'>Door Part</option>
                                </select>
                            </div>
                            <div id='categoryFields'></div>
                            <button type='submit' class='btn btn-primary'>Add Part</button>
                        </form>
                        <script>
                            function loadCategoryFields() {
                                var category = document.getElementById('category').value;
                                fetch('includes/parts/' + category + '_fields.php')
                                    .then(response => response.text())
                                    .then(html => {
                                        document.getElementById('categoryFields').innerHTML = html;
                                    });
                            }
                            document.getElementById('category').addEventListener('change', loadCategoryFields);
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
