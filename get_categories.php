<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/config/config.php';
require_once $rootPath . '/includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    // Return empty data if not logged in
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

// Get transaction type from query parameter
$type = isset($_GET['type']) ? sanitizeInput($_GET['type']) : null;

// Validate type
if (!in_array($type, ['income', 'expense'])) {
    // Return empty data if type is invalid
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// Get categories by type
$categories = getCategories($userId, $type);

// Return categories as JSON
header('Content-Type: application/json');
echo json_encode($categories);
exit;
?>
