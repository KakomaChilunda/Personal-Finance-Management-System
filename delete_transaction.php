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

// Check if transaction id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid transaction ID";
    $_SESSION['message_type'] = "danger";
    redirect('transactions.php');
}

$transactionId = (int)$_GET['id'];

// Get transaction details to verify ownership
$conn = connectDB();
$stmt = $conn->prepare("SELECT id FROM transactions WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $transactionId, $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if transaction exists and belongs to the user
if ($result->num_rows == 0) {
    $_SESSION['message'] = "Transaction not found";
    $_SESSION['message_type'] = "danger";
    redirect('transactions.php');
}

$stmt->close();

// Delete the transaction
$stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $transactionId, $userId);

if ($stmt->execute()) {
    $_SESSION['message'] = "Transaction deleted successfully";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting transaction";
    $_SESSION['message_type'] = "danger";
}

$stmt->close();
$conn->close();

redirect('transactions.php');
?>
