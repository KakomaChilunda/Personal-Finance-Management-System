<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/config/config.php';
require_once $rootPath . '/includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Get filters
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$startDate = isset($_GET['start_date']) ? sanitizeInput($_GET['start_date']) : null;
$endDate = isset($_GET['end_date']) ? sanitizeInput($_GET['end_date']) : null;

// Fetch all transactions based on filters
$transactions = getTransactions($userId, null, 0, $categoryId, $startDate, $endDate);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="transactions_' . date('Y-m-d') . '.csv"');

// Create file pointer to output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM to fix Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, ['Date', 'Category', 'Description', 'Amount (' . CURRENCY . ')', 'Type']);

// Add transaction data
foreach ($transactions as $transaction) {
    fputcsv($output, [
        $transaction['date'],
        $transaction['category_name'],
        $transaction['description'],
        $transaction['amount'],
        $transaction['transaction_type']
    ]);
}

// Close the file pointer
fclose($output);
exit;
?>
