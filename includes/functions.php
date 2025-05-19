<?php
// Use a directory-independent approach for including the config file
$configPath = dirname(__DIR__) . '/config/config.php';
require_once $configPath;

// Sanitize user input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect user
function redirect($url) {
    header("Location: $url");
    exit();
}

// Display error message
function displayError($message) {
    return "<div class='alert alert-danger'>$message</div>";
}

// Display success message
function displaySuccess($message) {
    return "<div class='alert alert-success'>$message</div>";
}

// Get user details
function getUserDetails($userId) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user;
}

// Get categories for user
function getCategories($userId, $type = null) {
    $conn = connectDB();
    
    if ($type) {
        $sql = "SELECT * FROM categories WHERE (user_id = ? OR user_id IS NULL) AND type = ? ORDER BY name";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $userId, $type);
    } else {
        $sql = "SELECT * FROM categories WHERE user_id = ? OR user_id IS NULL ORDER BY type, name";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = [];
    
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    return $categories;
}

// Get transactions for user
function getTransactions($userId, $limit = null, $offset = 0, $categoryId = null, $startDate = null, $endDate = null) {
    $conn = connectDB();
    
    $sql = "SELECT t.*, c.name as category_name, c.type as transaction_type 
            FROM transactions t 
            JOIN categories c ON t.category_id = c.id 
            WHERE t.user_id = ?";
    
    $types = "i";
    $params = [$userId];
    
    if ($categoryId) {
        $sql .= " AND t.category_id = ?";
        $types .= "i";
        $params[] = $categoryId;
    }
    
    if ($startDate) {
        $sql .= " AND t.date >= ?";
        $types .= "s";
        $params[] = $startDate;
    }
    
    if ($endDate) {
        $sql .= " AND t.date <= ?";
        $types .= "s";
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY t.date DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?, ?";
        $types .= "ii";
        $params[] = $offset;
        $params[] = $limit;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = [];
    
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    return $transactions;
}

// Get user's financial summary
function getFinancialSummary($userId, $startDate = null, $endDate = null) {
    $conn = connectDB();
    
    // Prepare date filters
    $dateFilter = "";
    $types = "i";
    $params = [$userId];
    
    if ($startDate && $endDate) {
        $dateFilter = " AND t.date BETWEEN ? AND ?";
        $types .= "ss";
        $params[] = $startDate;
        $params[] = $endDate;
    } elseif ($startDate) {
        $dateFilter = " AND t.date >= ?";
        $types .= "s";
        $params[] = $startDate;
    } elseif ($endDate) {
        $dateFilter = " AND t.date <= ?";
        $types .= "s";
        $params[] = $endDate;
    }
    
    // Get total income
    $sqlIncome = "SELECT SUM(t.amount) as total 
                 FROM transactions t 
                 JOIN categories c ON t.category_id = c.id 
                 WHERE t.user_id = ? AND c.type = 'income'" . $dateFilter;
    
    $stmtIncome = $conn->prepare($sqlIncome);
    $stmtIncome->bind_param($types, ...$params);
    $stmtIncome->execute();
    $resultIncome = $stmtIncome->get_result()->fetch_assoc();
    $totalIncome = $resultIncome['total'] ?: 0;
    $stmtIncome->close();
    
    // Get total expenses
    $sqlExpense = "SELECT SUM(t.amount) as total 
                  FROM transactions t 
                  JOIN categories c ON t.category_id = c.id 
                  WHERE t.user_id = ? AND c.type = 'expense'" . $dateFilter;
    
    $stmtExpense = $conn->prepare($sqlExpense);
    $stmtExpense->bind_param($types, ...$params);
    $stmtExpense->execute();
    $resultExpense = $stmtExpense->get_result()->fetch_assoc();
    $totalExpense = $resultExpense['total'] ?: 0;
    $stmtExpense->close();
    
    // Get expense by category
    $sqlExpenseByCategory = "SELECT c.name, SUM(t.amount) as total 
                            FROM transactions t 
                            JOIN categories c ON t.category_id = c.id 
                            WHERE t.user_id = ? AND c.type = 'expense'" . $dateFilter . " 
                            GROUP BY c.id 
                            ORDER BY total DESC";
    
    $stmtExpenseByCategory = $conn->prepare($sqlExpenseByCategory);
    $stmtExpenseByCategory->bind_param($types, ...$params);
    $stmtExpenseByCategory->execute();
    $resultExpenseByCategory = $stmtExpenseByCategory->get_result();
    $expenseByCategory = [];
    
    while ($row = $resultExpenseByCategory->fetch_assoc()) {
        $expenseByCategory[] = $row;
    }
    
    $stmtExpenseByCategory->close();
    
    // Get income by category
    $sqlIncomeByCategory = "SELECT c.name, SUM(t.amount) as total 
                           FROM transactions t 
                           JOIN categories c ON t.category_id = c.id 
                           WHERE t.user_id = ? AND c.type = 'income'" . $dateFilter . " 
                           GROUP BY c.id 
                           ORDER BY total DESC";
    
    $stmtIncomeByCategory = $conn->prepare($sqlIncomeByCategory);
    $stmtIncomeByCategory->bind_param($types, ...$params);
    $stmtIncomeByCategory->execute();
    $resultIncomeByCategory = $stmtIncomeByCategory->get_result();
    $incomeByCategory = [];
    
    while ($row = $resultIncomeByCategory->fetch_assoc()) {
        $incomeByCategory[] = $row;
    }
    
    $stmtIncomeByCategory->close();
    
    // Monthly stats for current year
    $currentYear = date('Y');
    $sqlMonthlyStats = "SELECT MONTH(t.date) as month, 
                        SUM(CASE WHEN c.type = 'income' THEN t.amount ELSE 0 END) as income,
                        SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) as expense
                        FROM transactions t 
                        JOIN categories c ON t.category_id = c.id 
                        WHERE t.user_id = ? AND YEAR(t.date) = ? 
                        GROUP BY MONTH(t.date) 
                        ORDER BY MONTH(t.date)";
    
    $stmtMonthlyStats = $conn->prepare($sqlMonthlyStats);
    $stmtMonthlyStats->bind_param("ii", $userId, $currentYear);
    $stmtMonthlyStats->execute();
    $resultMonthlyStats = $stmtMonthlyStats->get_result();
    $monthlyStats = [];
    
    $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    while ($row = $resultMonthlyStats->fetch_assoc()) {
        $monthIndex = $row['month'] - 1;
        $row['month_name'] = $months[$monthIndex];
        $monthlyStats[] = $row;
    }
    
    $stmtMonthlyStats->close();
    $conn->close();
    
    return [
        'total_income' => $totalIncome,
        'total_expense' => $totalExpense,
        'balance' => $totalIncome - $totalExpense,
        'expense_by_category' => $expenseByCategory,
        'income_by_category' => $incomeByCategory,
        'monthly_stats' => $monthlyStats
    ];
}

// Get count of transactions
function getTransactionCount($userId, $categoryId = null, $startDate = null, $endDate = null) {
    $conn = connectDB();
    
    $sql = "SELECT COUNT(*) as count FROM transactions WHERE user_id = ?";
    $types = "i";
    $params = [$userId];
    
    if ($categoryId) {
        $sql .= " AND category_id = ?";
        $types .= "i";
        $params[] = $categoryId;
    }
    
    if ($startDate) {
        $sql .= " AND date >= ?";
        $types .= "s";
        $params[] = $startDate;
    }
    
    if ($endDate) {
        $sql .= " AND date <= ?";
        $types .= "s";
        $params[] = $endDate;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $count = $result['count'];
    
    $stmt->close();
    $conn->close();
    return $count;
}
