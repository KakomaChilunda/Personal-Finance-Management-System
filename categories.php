<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Get all categories (both default and user-created)
$categories = getCategories($userId);

// Process form submission for adding a new category
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = sanitizeInput($_POST['name']);
    $type = sanitizeInput($_POST['type']);
    
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Category name is required";
    }
    
    // Validate type
    if (empty($type) || !in_array($type, ['income', 'expense'])) {
        $errors[] = "Invalid category type";
    }
    
    // If no errors, save category
    if (empty($errors)) {
        $conn = connectDB();
        
        // Check if category already exists for this user
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? AND type = ? AND (user_id = ? OR user_id IS NULL)");
        $stmt->bind_param("ssi", $name, $type, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "A category with this name and type already exists";
        } else {
            // Insert new category
            $stmt = $conn->prepare("INSERT INTO categories (user_id, name, type) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $name, $type);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Category added successfully";
                $_SESSION['message_type'] = "success";
                redirect('categories.php');
            } else {
                $errors[] = "Error adding category: " . $conn->error;
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page-header">
            Categories
            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-arrow-down text-success me-2"></i> Income Categories</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php 
                    $hasIncomeCategories = false;
                    foreach ($categories as $category): 
                        if ($category['type'] == 'income'):
                            $hasIncomeCategories = true;
                    ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo $category['name']; ?>
                            <?php if (!is_null($category['user_id'])): ?>
                                <a href="javascript:void(0);" onclick="confirmDelete('delete_category.php?id=<?php echo $category['id']; ?>')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-secondary">Default</span>
                            <?php endif; ?>
                        </li>
                    <?php 
                        endif;
                    endforeach; 
                    
                    if (!$hasIncomeCategories):
                    ?>
                        <li class="list-group-item text-center text-muted">No income categories found</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-arrow-up text-danger me-2"></i> Expense Categories</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php 
                    $hasExpenseCategories = false;
                    foreach ($categories as $category): 
                        if ($category['type'] == 'expense'):
                            $hasExpenseCategories = true;
                    ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo $category['name']; ?>
                            <?php if (!is_null($category['user_id'])): ?>
                                <a href="javascript:void(0);" onclick="confirmDelete('delete_category.php?id=<?php echo $category['id']; ?>')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-secondary">Default</span>
                            <?php endif; ?>
                        </li>
                    <?php 
                        endif;
                    endforeach; 
                    
                    if (!$hasExpenseCategories):
                    ?>
                        <li class="list-group-item text-center text-muted">No expense categories found</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="categories.php" method="POST">
                <div class="modal-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type-income" value="income" checked>
                            <label class="form-check-label" for="type-income">
                                Income
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type-expense" value="expense">
                            <label class="form-check-label" for="type-expense">
                                Expense
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once $rootPath . '/includes/footer.php';
?>
