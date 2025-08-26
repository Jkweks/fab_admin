<?php
session_start();
require_once 'includes/db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = 'Email already registered';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->execute([$email, $hash]);
        header('Location: signin.php');
        exit;
    }
}
?>
<?php include 'includes/header.php'; ?>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <?php include 'includes/spinner.php'; ?>
        <!-- Sign Up Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="index.php" class="">
                                <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                            </a>
                            <h3>Sign Up</h3>
                        </div>
                        <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" required>
                                <label for="floatingInput">Email address</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" required>
                                <label for="floatingPassword">Password</label>
                            </div>
                            <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Sign Up</button>
                        </form>
                        <p class="text-center mb-0">Already have an Account? <a href="signin.php">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign Up End -->
    </div>
<?php include 'includes/footer.php'; ?>
