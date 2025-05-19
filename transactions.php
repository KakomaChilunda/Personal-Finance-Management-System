<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter by category, date, and type
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Get transactions with filters and pagination
$transactions = getTransactions($userId, $limit, $offset, $categoryId, $startDate, $endDate);
$transactionCount = getTransactionCount($userId, $categoryId, $startDate, $endDate);
$totalPages = ceil($transactionCount / $limit);

// Get categories for filter
$categories = getCategories($userId);
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page-header">
            Transactions
            <a href="add_transaction.php" class="btn btn-primary float-end">
                <i class="fas fa-plus"></i> Add Transaction
            </a>
        </h1>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="transactions.php" class="row g-3">
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <optgroup label="Income">
                        <?php foreach ($categories as $category): ?>
                            <?php if ($category['type'] == 'income'): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($categoryId == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo $category['name']; ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Expense">
                        <?php foreach ($categories as $category): ?>
                            <?php if ($category['type'] == 'expense'): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($categoryId == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo $category['name']; ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">From Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">To Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="transactions.php" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Transactions List -->
<div class="card">
    <div class="card-body">
        <?php if (count($transactions) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($transaction['date'])); ?></td>
                                <td><?php echo $transaction['category_name']; ?></td>
                                <td><?php echo $transaction['description']; ?></td>
                                <td><?php echo CURRENCY; ?> <?php echo number_format($transaction['amount'], 2); ?></td>
                                <td>
                                    <?php if ($transaction['transaction_type'] == 'income'): ?>
                                        <span class="badge bg-success">Income</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Expense</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_transaction.php?id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);" onclick="confirmDelete('delete_transaction.php?id=<?php echo $transaction['id']; ?>')" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $categoryId ? '&category=' . $categoryId : ''; ?><?php echo $startDate ? '&start_date=' . $startDate : ''; ?><?php echo $endDate ? '&end_date=' . $endDate : ''; ?>">
                                    Previous
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $categoryId ? '&category=' . $categoryId : ''; ?><?php echo $startDate ? '&start_date=' . $startDate : ''; ?><?php echo $endDate ? '&end_date=' . $endDate : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $categoryId ? '&category=' . $categoryId : ''; ?><?php echo $startDate ? '&start_date=' . $startDate : ''; ?><?php echo $endDate ? '&end_date=' . $endDate : ''; ?>">
                                    Next
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted">No transactions found matching your criteria</p>
                <a href="add_transaction.php" class="btn btn-primary">Add Transaction</a>
            </div>
        <?php endif; ?>
        
        <?php if (count($transactions) > 0): ?>
            <div class="text-center mt-3">
                <a href="export_transactions.php<?php echo isset($_GET['category']) || isset($_GET['start_date']) || isset($_GET['end_date']) ? '?' . http_build_query($_GET) : ''; ?>" class="btn btn-success">
                    <i class="fas fa-file-csv me-2"></i> Export to CSV
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once $rootPath . '/includes/footer.php';
?>
