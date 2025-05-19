// Personal Finance Management System - JavaScript functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize modals
    var modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
    modalTriggerList.map(function (modalTriggerEl) {
        return new bootstrap.Modal(modalTriggerEl);
    });
    
    // Transaction Type Selector
    const transactionTypeSelect = document.getElementById('transaction-type');
    const categorySelect = document.getElementById('category_id');
    
    if (transactionTypeSelect && categorySelect) {
        transactionTypeSelect.addEventListener('change', function() {
            const type = this.value;
            
            // Reset category select
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            
            // Fetch categories based on type
            fetch(`get_categories.php?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        categorySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching categories:', error));
        });
    }
    
    // Date range picker for reports
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });
        
        endDate.addEventListener('change', function() {
            startDate.max = this.value;
        });
    }
    
    // Chart initialization
    initCharts();
});

// Initialize charts on dashboard
function initCharts() {
    // Expense by category chart
    const expenseCategoryCanvas = document.getElementById('expenseCategoryChart');
    if (expenseCategoryCanvas) {
        const ctx = expenseCategoryCanvas.getContext('2d');
        
        // Get chart data from the data attributes
        const labels = JSON.parse(expenseCategoryCanvas.dataset.labels || '[]');
        const values = JSON.parse(expenseCategoryCanvas.dataset.values || '[]');
        
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)',
                        'rgba(40, 159, 64, 0.8)',
                        'rgba(210, 199, 199, 0.8)',
                        'rgba(78, 52, 199, 0.8)',
                        'rgba(190, 152, 152, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(159, 159, 159, 1)',
                        'rgba(83, 102, 255, 1)',
                        'rgba(40, 159, 64, 1)',
                        'rgba(210, 199, 199, 1)',
                        'rgba(78, 52, 199, 1)',
                        'rgba(190, 152, 152, 1)'
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
                        text: 'Expenses by Category'
                    }
                }
            }
        });
    }
    
    // Monthly trend chart
    const monthlyTrendCanvas = document.getElementById('monthlyTrendChart');
    if (monthlyTrendCanvas) {
        const ctx = monthlyTrendCanvas.getContext('2d');
        
        // Get chart data from the data attributes
        const months = JSON.parse(monthlyTrendCanvas.dataset.months || '[]');
        const incomes = JSON.parse(monthlyTrendCanvas.dataset.incomes || '[]');
        const expenses = JSON.parse(monthlyTrendCanvas.dataset.expenses || '[]');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Income',
                        data: incomes,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Expense',
                        data: expenses,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (' + currencySymbol + ')'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Income vs Expense'
                    }
                }
            }
        });
    }
}

// Confirm delete
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        window.location.href = url;
    }
    return false;
}
