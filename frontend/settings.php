<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
require_once 'includes/db.php';
$themes = ['light', 'dark', 'cupcake', 'night', 'forest'];
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['theme'] ?? 'light';
    if (!in_array($theme, $themes, true)) {
        $theme = 'light';
    }
    $stmt = $pdo->prepare('UPDATE users SET theme = ? WHERE id = ?');
    $stmt->execute([$theme, $_SESSION['user_id']]);
    $_SESSION['theme'] = $theme;
    $message = 'Settings updated';
}
$currentTheme = $_SESSION['theme'] ?? 'light';
?>
<?php include 'includes/header.php'; ?>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <?php include 'includes/spinner.php'; ?>
        <?php include 'includes/sidebar.php'; ?>
        <!-- Content Start -->
        <div class="content">
            <?php include 'includes/navbar.php'; ?>
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <h6 class="mb-4">Settings</h6>
                            <?php if ($message): ?>
                            <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
                            <?php endif; ?>
                              <form method="post">
                                  <div class="mb-3">
                                      <label for="theme-select" class="form-label">Select Theme</label>
                                      <select id="theme-select" name="theme" class="form-select">
                                          <?php foreach ($themes as $t): ?>
                                              <option value="<?php echo htmlspecialchars($t); ?>" <?php if ($currentTheme === $t) echo 'selected'; ?>><?php echo htmlspecialchars(ucfirst($t)); ?></option>
                                          <?php endforeach; ?>
                                      </select>
                                  </div>
                                  <button type="submit" class="btn btn-primary mt-3">Save</button>
                              </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Your Site Name</a>, All Right Reserved.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            Designed By <a href="https://htmlcodex.com">HTML Codex</a><br>
                            Distributed By <a class="border-bottom" href="https://themewagon.com" target="_blank">ThemeWagon</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->
        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>
<?php include 'includes/footer.php'; ?>
