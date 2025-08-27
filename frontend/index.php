<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
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
                            <h6 class="mb-4">Welcome to the dashboard</h6>
                            <p>This is a placeholder page.</p>
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
