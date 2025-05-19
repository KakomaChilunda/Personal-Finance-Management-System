<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

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
    
    // If no errors, save transaction
    if (empty($errors)) {
        $conn = connectDB();
        
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, category_id, amount, description, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidss", $userId, $categoryId, $amount, $description, $date);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Transaction added successfully";
            $_SESSION['message_type'] = "success";
            redirect('transactions.php');
        } else {
            $errors[] = "Error adding transaction: " . $conn->error;
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Get categories
$incomeCategories = getCategories($userId, 'income');
$expenseCategories = getCategories($userId, 'expense');
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page-header">Add Transaction</h1>
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
                
                <form action="add_transaction.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Transaction Type</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="transaction-type" id="income-type" value="income" checked>
                            <label class="form-check-label" for="income-type">Income</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="transaction-type" id="expense-type" value="expense">
                            <label class="form-check-label" for="expense-type">Expense</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <!-- Income categories (shown by default) -->
                            <div id="income-categories">
                                <?php foreach ($incomeCategories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </div>
                            <!-- Expense categories (initially hidden via JavaScript) -->
                            <div id="expense-categories" style="display: none;">
                                <?php foreach ($expenseCategories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </div>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo CURRENCY; ?></span>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
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
            categorySelect.appendChild(option);
        });
    }
    
    // Set initial categories
    updateCategoryOptions('income');
    
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
