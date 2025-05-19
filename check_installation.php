<?php
// Turn on error reporting for this file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define requirements
$requirements = [
    'PHP Version' => [
        'required' => '7.4.0',
        'current' => phpversion(),
        'status' => version_compare(phpversion(), '7.4.0', '>=')
    ],
    'MySQL Extension' => [
        'required' => 'Enabled',
        'current' => extension_loaded('mysqli') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('mysqli')
    ],
    'PDO MySQL Extension' => [
        'required' => 'Enabled',
        'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('pdo_mysql')
    ],
    'File Permissions' => [
        'required' => 'Writable',
        'current' => is_writable(__DIR__ . '/exports') ? 'Writable' : 'Not Writable',
        'status' => is_writable(__DIR__ . '/exports')
    ]
];

// Check for database connection
$dbStatus = [
    'required' => 'Connected',
    'current' => 'Not Connected',
    'status' => false,
    'message' => ''
];

// Try to connect to database
if (file_exists(__DIR__ . '/config/config.php')) {
    include_once __DIR__ . '/config/config.php';
    
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if (!$conn->connect_error) {
                $dbStatus['current'] = 'Connected';
                $dbStatus['status'] = true;
                
                // Check if tables exist
                $tables = ['users', 'categories', 'transactions'];
                $missingTables = [];
                
                foreach ($tables as $table) {
                    $result = $conn->query("SHOW TABLES LIKE '$table'");
                    if ($result->num_rows == 0) {
                        $missingTables[] = $table;
                    }
                }
                
                if (!empty($missingTables)) {
                    $dbStatus['current'] = 'Connected, but missing tables: ' . implode(', ', $missingTables);
                    $dbStatus['status'] = false;
                    $dbStatus['message'] = 'Database tables are missing. Please import the setup.sql file.';
                }
                
                $conn->close();
            } else {
                $dbStatus['message'] = 'Connection error: ' . $conn->connect_error;
            }
        } catch (Exception $e) {
            $dbStatus['message'] = 'Connection exception: ' . $e->getMessage();
        }
    } else {
        $dbStatus['message'] = 'Database configuration constants are not defined properly';
    }
} else {
    $dbStatus['message'] = 'Config file not found';
}

$requirements['Database Connection'] = $dbStatus;

// Overall status
$overallStatus = true;
foreach ($requirements as $requirement) {
    if (!$requirement['status']) {
        $overallStatus = false;
        break;
    }
}

// Get application URL
$appUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['REQUEST_URI']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Check - Personal Finance Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 2rem;
        }
        .check-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .status-icon {
            font-size: 1.5rem;
        }
        .setup-step {
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: #fff;
            border-radius: 0.25rem;
            border-left: 5px solid #6c757d;
        }
        .setup-step.active {
            border-left-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container check-container">
        <h1 class="text-center mb-4">Personal Finance Management System</h1>
        <h2 class="text-center mb-5">Installation Check</h2>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">System Requirements</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Requirement</th>
                                <th>Required</th>
                                <th>Current</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requirements as $name => $requirement): ?>
                                <tr>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $requirement['required']; ?></td>
                                    <td><?php echo $requirement['current']; ?></td>
                                    <td>
                                        <?php if ($requirement['status']): ?>
                                            <span class="text-success status-icon">✓</span>
                                        <?php else: ?>
                                            <span class="text-danger status-icon">✗</span>
                                            <?php if (isset($requirement['message']) && !empty($requirement['message'])): ?>
                                                <p class="text-danger mb-0 small"><?php echo $requirement['message']; ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert <?php echo $overallStatus ? 'alert-success' : 'alert-danger'; ?> mt-3">
                    <strong>Overall Status:</strong> 
                    <?php echo $overallStatus ? 'Your system meets all requirements!' : 'Your system does not meet all requirements. Please fix the issues above.'; ?>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Setup Instructions</h3>
            </div>
            <div class="card-body">
                <div class="setup-step <?php echo !file_exists(__DIR__ . '/config/config.php') ? 'active' : ''; ?>">
                    <h4>1. Database Configuration</h4>
                    <p>Edit the <code>config/config.php</code> file with your database credentials:</p>
                    <pre class="bg-light p-3 mb-0"><code>define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'finance_management');</code></pre>
                </div>
                
                <div class="setup-step <?php echo (isset($dbStatus['status']) && !$dbStatus['status'] && strpos($dbStatus['current'], 'missing tables') !== false) ? 'active' : ''; ?>">
                    <h4>2. Database Setup</h4>
                    <p>Import the <code>database/setup.sql</code> file into your MySQL database:</p>
                    <ol>
                        <li>Log in to phpMyAdmin or your preferred MySQL client</li>
                        <li>Create a new database named <code>finance_management</code> (or your chosen name)</li>
                        <li>Import the <code>database/setup.sql</code> file</li>
                    </ol>
                </div>
                
                <div class="setup-step <?php echo !$overallStatus ? 'active' : ''; ?>">
                    <h4>3. Folder Permissions</h4>
                    <p>Ensure the following folders have write permissions:</p>
                    <ul>
                        <li><code>exports/</code> - For storing exported CSV files</li>
                    </ul>
                </div>
                
                <div class="setup-step <?php echo $overallStatus ? 'active' : ''; ?>">
                    <h4>4. Access the Application</h4>
                    <p>Once all requirements are met, you can access the application at:</p>
                    <p><a href="<?php echo $appUrl; ?>" class="btn btn-primary"><?php echo $appUrl; ?></a></p>
                </div>
            </div>
        </div>
        
        <div class="text-center mb-5">
            <a href="<?php echo $appUrl; ?>" class="btn btn-lg btn-primary">Go to Application</a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-lg btn-outline-secondary ms-2">Refresh Check</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
