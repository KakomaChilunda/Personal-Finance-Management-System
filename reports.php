<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Get date range for filtering
$startDate = isset($_GET['start_date']) ? sanitizeInput($_GET['start_date']) : date('Y-m-01'); // First day of current month
$endDate = isset($_GET['end_date']) ? sanitizeInput($_GET['end_date']) : date('Y-m-t'); // Last day of current month

// Get financial summary for selected period
$summary = getFinancialSummary($userId, $startDate, $endDate);
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page-header">Financial Reports</h1>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="reports.php" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">From Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">To Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
                <a href="reports.php" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="stat-card income-card">
                <h3><i class="fas fa-arrow-down me-2"></i> Income</h3>
                <h2 class="display-5"><?php echo CURRENCY; ?> <?php echo number_format($summary['total_income'], 2); ?></h2>
                <p>Total Income for Selected Period</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="stat-card expense-card">
                <h3><i class="fas fa-arrow-up me-2"></i> Expenses</h3>
                <h2 class="display-5"><?php echo CURRENCY; ?> <?php echo number_format($summary['total_expense'], 2); ?></h2>
                <p>Total Expenses for Selected Period</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="stat-card balance-card">
                <h3><i class="fas fa-wallet me-2"></i> Balance</h3>
                <h2 class="display-5"><?php echo CURRENCY; ?> <?php echo number_format($summary['balance'], 2); ?></h2>
                <p>Net Balance for Selected Period</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Expense by Category -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
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
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($summary['expense_by_category'] as $category): 
                                    $percentage = ($summary['total_expense'] > 0) ? 
                                        ($category['total'] / $summary['total_expense'] * 100) : 0;
                                ?>
                                    <tr>
                                        <td><?php echo $category['name']; ?></td>
                                        <td><?php echo CURRENCY; ?> <?php echo number_format($category['total'], 2); ?></td>
                                        <td><?php echo number_format($percentage, 1); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No expense data available for the selected period</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Income by Category -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Income Sources</h5>
            </div>
            <div class="card-body">
                <?php if (count($summary['income_by_category']) > 0): ?>
                    <div class="chart-container">
                        <canvas id="incomeCategoryChart"
                                data-labels='<?php echo json_encode(array_column($summary['income_by_category'], 'name')); ?>' 
                                data-values='<?php echo json_encode(array_column($summary['income_by_category'], 'total')); ?>'>
                        </canvas>
                    </div>
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($summary['income_by_category'] as $category): 
                                    $percentage = ($summary['total_income'] > 0) ? 
                                        ($category['total'] / $summary['total_income'] * 100) : 0;
                                ?>
                                    <tr>
                                        <td><?php echo $category['name']; ?></td>
                                        <td><?php echo CURRENCY; ?> <?php echo number_format($category['total'], 2); ?></td>
                                        <td><?php echo number_format($percentage, 1); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No income data available for the selected period</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Monthly Trend Chart -->
    <div class="col-md-12">
        <div class="card dashboard-card">
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

<!-- Export Button -->
<div class="row">
    <div class="col-md-12 text-center mb-4">
        <a href="export_transactions.php?start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>" class="btn btn-success btn-lg">
            <i class="fas fa-file-csv me-2"></i> Export Report to CSV
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Income Category Chart
    const incomeCategoryCanvas = document.getElementById('incomeCategoryChart');
    if (incomeCategoryCanvas) {
        const ctx = incomeCategoryCanvas.getContext('2d');
        
        // Get chart data from the data attributes
        const labels = JSON.parse(incomeCategoryCanvas.dataset.labels || '[]');
        const values = JSON.parse(incomeCategoryCanvas.dataset.values || '[]');
        
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Income by Category'
                    }
                }
            }
        });
    }
});
</script>

<?php
require_once $rootPath . '/includes/footer.php';
?>
