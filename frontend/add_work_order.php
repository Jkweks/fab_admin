<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
include 'includes/db.php';
$job_id = $_GET['job_id'] ?? $_POST['job_id'] ?? '';
if (!$job_id) {
    die('Job required');
}
$fabricators = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role = 'fabricator' ORDER BY first_name")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery = !empty($_POST['material_delivery_date']) ? $_POST['material_delivery_date'] : null;
    $pull_from_stock = isset($_POST['pull_from_stock']) ? 1 : 0;
    $delivered = isset($_POST['delivered']) ? 1 : 0;
    $status = ($_POST['action'] ?? 'draft') === 'submit' ? 'submitted' : 'draft';
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(work_order_number),0)+1 FROM work_orders WHERE job_id = ?");
    $stmt->execute([$job_id]);
    $next = $stmt->fetchColumn();
    $insert = $pdo->prepare("INSERT INTO work_orders (job_id, work_order_number, material_delivery_date, pull_from_stock, delivered, status) VALUES (?,?,?,?,?,?) RETURNING id");
    $insert->execute([$job_id, $next, $delivery, $pull_from_stock, $delivered, $status]);
    $wo_id = $insert->fetchColumn();
    if (!empty($_POST['items'])) {
        $item_sql = "INSERT INTO work_order_items (work_order_id, item_type, elevation, quantity, scope, comments, date_required, date_completed, completed_by) VALUES (?,?,?,?,?,?,?,?,?)";
        $item_stmt = $pdo->prepare($item_sql);
        foreach ($_POST['items'] as $item) {
            $quantity = isset($item['quantity']) && $item['quantity'] !== '' ? (int)$item['quantity'] : null;
            $date_required = !empty($item['date_required']) ? $item['date_required'] : null;
            $date_completed = !empty($item['date_completed']) ? $item['date_completed'] : null;
            $completed_by = isset($item['completed_by']) && $item['completed_by'] !== '' ? (int)$item['completed_by'] : null;

            $item_stmt->execute([
                $wo_id,
                $item['item_type'] ?? '',
                $item['elevation'] ?? '',
                $quantity,
                $item['scope'] ?? '',
                $item['comments'] ?? '',
                $date_required,
                $date_completed,
                $completed_by,
            ]);
        }
    }
    $pdo->commit();
    header('Location: jobs.php');
    exit;
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
                            <h6 class='mb-4'>Add Work Order</h6>
                            <form method='post'>
                                <input type='hidden' name='job_id' value='<?php echo htmlspecialchars($job_id); ?>'>
                                <div class='mb-3'>
                                    <label class='form-label'>Material Delivery Date</label>
                                    <input type='date' name='material_delivery_date' class='form-control'>
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' name='pull_from_stock' id='pull_from_stock'>
                                        <label class='form-check-label' for='pull_from_stock'>Pull from stock</label>
                                    </div>
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' name='delivered' id='delivered'>
                                        <label class='form-check-label' for='delivered'>Delivered</label>
                                    </div>
                                </div>
                                <div id='items-container'>
                                    <h6>Line Items</h6>
                                    <div class='row g-2 mb-3 item-row'>
                                        <div class='col-md-2'>
                                            <select name='items[0][item_type]' class='form-select'>
                                                <option value='Curtainwall'>Curtainwall</option>
                                                <option value='Storefront'>Storefront</option>
                                                <option value='Doors'>Doors</option>
                                                <option value='Window wall'>Window wall</option>
                                            </select>
                                        </div>
                                        <div class='col-md-2'><input type='text' name='items[0][elevation]' class='form-control' placeholder='Elevation'></div>
                                        <div class='col-md-1'><input type='number' name='items[0][quantity]' class='form-control' placeholder='Qty'></div>
                                        <div class='col-md-2'>
                                            <select name='items[0][scope]' class='form-select'>
                                                <option value='assemble'>Assemble</option>
                                                <option value='kit'>Kit</option>
                                                <option value='hardware'>Hardware</option>
                                            </select>
                                        </div>
                                        <div class='col-md-2'><input type='text' name='items[0][comments]' class='form-control' placeholder='Comments'></div>
                                        <div class='col-md-1'><input type='date' name='items[0][date_required]' class='form-control'></div>
                                        <div class='col-md-1'><input type='date' name='items[0][date_completed]' class='form-control'></div>
                                        <div class='col-md-1'>
                                            <select name='items[0][completed_by]' class='form-select'>
                                                <option value=''>--</option>
                                                <?php foreach ($fabricators as $fab): ?>
                                                    <option value='<?php echo htmlspecialchars($fab['id']); ?>'><?php echo htmlspecialchars($fab['first_name'] . ' ' . $fab['last_name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type='button' class='btn btn-secondary mb-3' id='add-item'>Add Line Item</button>
                
                                <div>
                                    <button type='submit' name='action' value='draft' class='btn btn-secondary'>Save Draft</button>
                                    <a href='jobs.php' class='btn btn-secondary'>Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href='#' class='btn btn-lg btn-primary btn-lg-square back-to-top'><i class='bi bi-arrow-up'></i></a>
    </div>
    <script>
    document.getElementById('add-item').addEventListener('click', function() {
        var container = document.getElementById('items-container');
        var index = container.querySelectorAll('.item-row').length;
        var row = document.createElement('div');
        row.className = 'row g-2 mb-3 item-row';
        row.innerHTML = `
            <div class="col-md-2">
                <select name="items[${index}][item_type]" class="form-select">
                    <option value="Curtainwall">Curtainwall</option>
                    <option value="Storefront">Storefront</option>
                    <option value="Doors">Doors</option>
                    <option value="Window wall">Window wall</option>
                </select>
            </div>
            <div class="col-md-2"><input type="text" name="items[${index}][elevation]" class="form-control" placeholder="Elevation"></div>
            <div class="col-md-1"><input type="number" name="items[${index}][quantity]" class="form-control" placeholder="Qty"></div>
            <div class="col-md-2">
                <select name="items[${index}][scope]" class="form-select">
                    <option value="assemble">Assemble</option>
                    <option value="kit">Kit</option>
                    <option value="hardware">Hardware</option>
                </select>
            </div>
            <div class="col-md-2"><input type="text" name="items[${index}][comments]" class="form-control" placeholder="Comments"></div>
            <div class="col-md-1"><input type="date" name="items[${index}][date_required]" class="form-control"></div>
            <div class="col-md-1"><input type="date" name="items[${index}][date_completed]" class="form-control"></div>
            <div class="col-md-1">
                <select name="items[${index}][completed_by]" class="form-select">
                    <option value="">--</option>
                    <?php foreach ($fabricators as $fab): ?>
                        <option value="<?php echo htmlspecialchars($fab['id']); ?>"><?php echo htmlspecialchars($fab['first_name'] . ' ' . $fab['last_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>`;
        container.appendChild(row);
    });
    </script>
<?php include 'includes/footer.php'; ?>

