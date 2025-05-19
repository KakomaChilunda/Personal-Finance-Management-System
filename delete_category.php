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

// Check if category id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid category ID";
    $_SESSION['message_type'] = "danger";
    redirect('categories.php');
}

$categoryId = (int)$_GET['id'];

// Get category details to verify ownership
$conn = connectDB();
$stmt = $conn->prepare("SELECT id FROM categories WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $categoryId, $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if category exists and belongs to the user
if ($result->num_rows == 0) {
    $_SESSION['message'] = "Category not found or you don't have permission to delete it";
    $_SESSION['message_type'] = "danger";
    redirect('categories.php');
}

$stmt->close();

// Check if category is used in any transactions
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM transactions WHERE category_id = ?");
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$transactionCount = $row['count'];

if ($transactionCount > 0) {
    $_SESSION['message'] = "Cannot delete category because it is used in $transactionCount transaction(s)";
    $_SESSION['message_type'] = "danger";
    redirect('categories.php');
}

$stmt->close();

// Delete the category
$stmt = $conn->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $categoryId, $userId);

if ($stmt->execute()) {
    $_SESSION['message'] = "Category deleted successfully";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting category";
    $_SESSION['message_type'] = "danger";
}

$stmt->close();
$conn->close();

redirect('categories.php');
?>
