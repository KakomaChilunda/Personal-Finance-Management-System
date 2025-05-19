<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

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

// Get transaction details
$conn = connectDB();
$stmt = $conn->prepare("SELECT t.*, c.type as transaction_type 
                        FROM transactions t 
                        JOIN categories c ON t.category_id = c.id 
                        WHERE t.id = ? AND t.user_id = ?");
$stmt->bind_param("ii", $transactionId, $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if transaction exists and belongs to the user
if ($result->num_rows == 0) {
    $_SESSION['message'] = "Transaction not found";
    $_SESSION['message_type'] = "danger";
    redirect('transactions.php');
}

$transaction = $result->fetch_assoc();
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $amount = sanitizeInput($_POST['amount']);
    $description = sanitizeInput($_POST['description']);
    $date = sanitizeInput($_POST['date']);
    $categoryId = sanitizeInput($_POST['category_id']);
    
    $errors = [];
    
    // Validate amount
    if (empty($amount)) {
        $errors[] = "Amount is required";
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $errors[] = "Amount must be a positive number";
    }
    
    // Validate date
    if (empty($date)) {
        $errors[] = "Date is required";
    }
    
    // Validate category
    if (empty($categoryId)) {
        $errors[] = "Category is required";
    }
    
    // If no errors, update transaction
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE transactions SET category_id = ?, amount = ?, description = ?, date = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("idssii", $categoryId, $amount, $description, $date, $transactionId, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Transaction updated successfully";
            $_SESSION['message_type'] = "success";
            redirect('transactions.php');
        } else {
            $errors[] = "Error updating transaction: " . $conn->error;
        }
        
        $stmt->close();
    }
}

// Get categories for the drop-down
$incomeCategories = getCategories($userId, 'income');
$expenseCategories = getCategories($userId, 'expense');

$conn->close();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page-header">Edit Transaction</h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="edit_transaction.php?id=<?php echo $transactionId; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Transaction Type</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="transaction-type" id="income-type" value="income" <?php echo ($transaction['transaction_type'] == 'income') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="income-type">Income</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="transaction-type" id="expense-type" value="expense" <?php echo ($transaction['transaction_type'] == 'expense') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="expense-type">Expense</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php if ($transaction['transaction_type'] == 'income'): ?>
                                <?php foreach ($incomeCategories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($transaction['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($expenseCategories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($transaction['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo CURRENCY; ?></span>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" value="<?php echo $transaction['amount']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?php echo $transaction['date']; ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"><?php echo $transaction['description']; ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Transaction</button>
                        <a href="transactions.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Handle transaction type selection
document.addEventListener('DOMContentLoaded', function() {
    const incomeRadio = document.getElementById('income-type');
    const expenseRadio = document.getElementById('expense-type');
    const categorySelect = document.getElementById('category_id');
    
    // Helper function to update category options
    function updateCategoryOptions(type) {
        // Clear current options
        categorySelect.innerHTML = '<option value="">Select Category</option>';
        
        // Add new options based on type
        const categories = type === 'income' ? <?php echo json_encode($incomeCategories); ?> : <?php echo json_encode($expenseCategories); ?>;
        
        categories.forEach(function(category) {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            
            // Select the current category if it matches
            if (category.id == <?php echo $transaction['category_id']; ?>) {
                option.selected = true;
            }
            
            categorySelect.appendChild(option);
        });
    }
    
    // Add event listeners
    incomeRadio.addEventListener('change', function() {
        if (this.checked) {
            updateCategoryOptions('income');
        }
    });
    
    expenseRadio.addEventListener('change', function() {
        if (this.checked) {
            updateCategoryOptions('expense');
        }
    });
});
</script>

<?php
require_once $rootPath . '/includes/footer.php';
?>
