        <!-- Sidebar Start -->
        <div class='sidebar pe-4 pb-3'>
            <nav class='navbar bg-light navbar-light'>
                <a href='index.php' class='navbar-brand mx-4 mb-3'>
                    <h3 class='text-primary'><i class='fa fa-hashtag me-2'></i>DASHMIN</h3>
                </a>
                <div class='d-flex align-items-center ms-4 mb-4'>
                    <div class='position-relative'>
                        <img class='rounded-circle' src='img/user.jpg' alt='' style='width: 40px; height: 40px;'>
                        <div class='bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1'></div>
                    </div>
                    <div class='ms-3'>
                        <h6 class='mb-0'><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h6>
                        <span><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                    </div>
                </div>
                <div class='navbar-nav w-100'>
                    <a href='index.php' class='nav-item nav-link'><i class='fa fa-tachometer-alt me-2'></i>Dashboard</a>
                    <a href='jobs.php' class='nav-item nav-link'><i class='fa fa-briefcase me-2'></i>Jobs</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href='data.php' class='nav-item nav-link'><i class='fa fa-database me-2'></i>Data Management</a>
                    <a href='add_pm.php' class='nav-item nav-link ms-4'>Add Project Manager</a>
                    <a href='add_job.php' class='nav-item nav-link ms-4'>Add Job</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->
