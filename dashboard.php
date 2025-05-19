<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Get current month and year
$currentMonth = date('m');
$currentYear = date('Y');
$startDate = "$currentYear-$currentMonth-01";
$endDate = date('Y-m-t', strtotime($startDate)); // Last day of current month

// Get financial summary for current month
$summary = getFinancialSummary($userId, $startDate, $endDate);

// Get recent transactions (last 5)
$recentTransactions = getTransactions($userId, 5);
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page-header">Dashboard <small class="text-muted"><?php echo date('F Y'); ?></small></h1>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="stat-card income-card">
                <h3><i class="fas fa-arrow-down me-2"></i> Income</h3>
                <h2 class="display-5"><?php echo CURRENCY; ?> <?php echo number_format($summary['total_income'], 2); ?></h2>
                <p>Total Income This Month</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="stat-card expense-card">
                <h3><i class="fas fa-arrow-up me-2"></i> Expenses</h3>
                <h2 class="display-5"><?php echo CURRENCY; ?> <?php echo number_format($summary['total_expense'], 2); ?></h2>
                <p>Total Expenses This Month</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="stat-card balance-card">
                <h3><i class="fas fa-wallet me-2"></i> Balance</h3>
                <h2 class="display-5"><?php echo CURRENCY; ?> <?php echo number_format($summary['balance'], 2); ?></h2>
                <p>Net Balance This Month</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Expense Category Chart -->
    <div class="col-md-6">
        <div class="card dashboard-card h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Expense Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (count($summary['expense_by_category']) > 0): ?>
                    <div class="chart-container">
                        <canvas id="expenseCategoryChart" 
                                data-labels='<?php echo json_encode(array_column($summary['expense_by_category'], 'name')); ?>' 
                                data-values='<?php echo json_encode(array_column($summary['expense_by_category'], 'total')); ?>'>
                        </canvas>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No expense data available for this month</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Monthly Trend Chart -->
    <div class="col-md-6">
        <div class="card dashboard-card h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Monthly Trend</h5>
            </div>
            <div class="card-body">
                <?php 
                $months = [];
                $incomes = [];
                $expenses = [];
                
                foreach ($summary['monthly_stats'] as $stat) {
                    $months[] = $stat['month_name'];
                    $incomes[] = $stat['income'];
                    $expenses[] = $stat['expense'];
                }
                ?>
                
                <?php if (count($summary['monthly_stats']) > 0): ?>
                    <div class="chart-container">
                        <canvas id="monthlyTrendChart" 
                                data-months='<?php echo json_encode($months); ?>' 
                                data-incomes='<?php echo json_encode($incomes); ?>' 
                                data-expenses='<?php echo json_encode($expenses); ?>'>
                        </canvas>
                    </div>
                    
                    <script>
                        const currencySymbol = '<?php echo CURRENCY; ?>';
                    </script>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No monthly trend data available for this year</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Transactions</h5>
                <a href="transactions.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (count($recentTransactions) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
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
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No transactions found</p>
                        <a href="add_transaction.php" class="btn btn-primary">Add Transaction</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Floating action button for adding new transaction -->
<a href="add_transaction.php" class="btn btn-primary btn-floating">
    <i class="fas fa-plus"></i>
</a>

<?php
require_once $rootPath . '/includes/footer.php';
?>
