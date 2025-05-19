<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

// Get error message if set
$errorMessage = isset($_GET['message']) ? sanitizeInput($_GET['message']) : 'An unexpected error occurred';
$errorCode = isset($_GET['code']) ? (int)$_GET['code'] : 404;
?>

<div class="row justify-content-center my-5">
    <div class="col-md-6 text-center">
        <div class="card shadow">
            <div class="card-body p-5">
                <h1 class="display-1 text-danger mb-4"><?php echo $errorCode; ?></h1>
                <h2 class="mb-4">Oops! Something went wrong</h2>
                <p class="lead mb-5"><?php echo $errorMessage; ?></p>
                <a href="index.php" class="btn btn-primary">Go to Homepage</a>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="btn btn-outline-primary ms-2">Go to Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once $rootPath . '/includes/footer.php';
?>
