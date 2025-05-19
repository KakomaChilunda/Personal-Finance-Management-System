<?php
// Define root path for consistent includes
$rootPath = __DIR__;
require_once $rootPath . '/includes/header.php';

// Redirect to dashboard if logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>

<!-- Hero Section -->
<div class="container-fluid bg-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
                <h1 class="display-4 fw-bold mb-3"><?php echo SITE_NAME; ?></h1>
                <p class="lead fs-4 mb-4">Take control of your financial future with our powerful yet simple personal finance tracking system.</p>
                <div class="d-grid gap-2 d-md-flex">
                    <a href="register.php" class="btn btn-light btn-lg px-4 me-md-2 fw-bold" data-aos="zoom-in" data-aos-delay="300">Get Started Now</a>
                    <a href="login.php" class="btn btn-outline-light btn-lg px-4" data-aos="zoom-in" data-aos-delay="400">Login</a>
                </div>
            </div>
            <div class="col-md-6 text-center" data-aos="fade-left" data-aos-delay="200">
                <img src="assets/img/finance-hero.svg" alt="Finance Management Illustration" class="img-fluid" style="max-height: 350px;">
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container mb-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-5 fw-bold">Why Choose Our Finance Manager?</h2>
        <p class="lead text-muted">Designed to help you make smarter financial decisions</p>
    </div>
    
    <div class="row g-4 py-3">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card h-100 dashboard-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                    <h3>Track Your Finances</h3>
                    <p>Easily record your income and expenses to keep track of where your money goes. Get a clear picture of your financial habits.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 dashboard-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-chart-pie fa-3x text-success mb-3"></i>
                    <h3>Visualize Your Spending</h3>
                    <p>Interactive charts and reports help you understand your spending patterns and identify areas for improvement.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card h-100 dashboard-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-piggy-bank fa-3x text-danger mb-3"></i>
                    <h3>Manage Your Budget</h3>
                    <p>Categorize transactions and monitor your budget to achieve your financial goals and build wealth over time.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="container-fluid bg-light py-5 mb-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold">How It Works</h2>
            <p class="lead text-muted">Get started in three simple steps</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 text-center mb-4 mb-md-0" data-aos="fade-right" data-aos-delay="100">
                <div class="bg-white rounded-circle shadow d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                    <i class="fas fa-user-plus fa-3x text-primary"></i>
                </div>
                <h3 class="my-3">1. Create an Account</h3>
                <p class="text-muted">Sign up for free and set up your personal profile in less than a minute.</p>
            </div>
            <div class="col-md-4 text-center mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-white rounded-circle shadow d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                    <i class="fas fa-plus-circle fa-3x text-primary"></i>
                </div>
                <h3 class="my-3">2. Add Your Transactions</h3>
                <p class="text-muted">Record your income and expenses with customizable categories.</p>
            </div>
            <div class="col-md-4 text-center" data-aos="fade-left" data-aos-delay="300">
                <div class="bg-white rounded-circle shadow d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                    <i class="fas fa-chart-bar fa-3x text-primary"></i>
                </div>
                <h3 class="my-3">3. Analyze Your Finances</h3>
                <p class="text-muted">View detailed reports and visualize your financial health at a glance.</p>
            </div>
        </div>
    </div>
</div>

<!-- Key Features Section -->
<div class="container mb-5">
    <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0" data-aos="zoom-in-right">
            <img src="assets/img/dashboard-preview.svg" alt="Dashboard Preview" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6" data-aos="zoom-in-left">
            <h2 class="fw-bold mb-4">Powerful Features for Financial Success</h2>
            <div class="d-flex mb-3" data-aos="fade-up" data-aos-delay="100">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                </div>
                <div>
                    <h5>Smart Dashboard</h5>
                    <p class="text-muted">Get a quick overview of your financial health with our intuitive dashboard.</p>
                </div>
            </div>
            <div class="d-flex mb-3" data-aos="fade-up" data-aos-delay="200">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                </div>
                <div>
                    <h5>Detailed Reports</h5>
                    <p class="text-muted">Access comprehensive reports to analyze your spending habits over time.</p>
                </div>
            </div>
            <div class="d-flex mb-3" data-aos="fade-up" data-aos-delay="300">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                </div>
                <div>
                    <h5>Category Management</h5>
                    <p class="text-muted">Customize transaction categories to match your unique financial situation.</p>
                </div>
            </div>
            <div class="d-flex" data-aos="fade-up" data-aos-delay="400">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                </div>
                <div>
                    <h5>Data Export</h5>
                    <p class="text-muted">Export your financial data to CSV for advanced analysis in other tools.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action Section -->
<div class="container-fluid bg-primary text-white text-center py-5 mb-5" data-aos="fade-up">
    <div class="container">
        <h2 class="display-5 fw-bold mb-3">Ready to Take Control of Your Finances?</h2>
        <p class="lead mb-4">Join thousands of users who are making smarter financial decisions every day.</p>
        <a href="register.php" class="btn btn-light btn-lg px-5 py-3 fw-bold" data-aos="zoom-in" data-aos-delay="200">Create Your Free Account</a>
    </div>
</div>

<!-- Testimonials Section -->
<div class="container mb-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-5 fw-bold">What Our Users Say</h2>
        <p class="lead text-muted">Trusted by individuals who care about their financial future</p>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4 mb-md-0" data-aos="flip-left" data-aos-delay="100">
            <div class="card h-100 testimonial-card">
                <div class="card-body p-4">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="mb-4">"This finance manager has completely transformed how I handle my money. Now I can see exactly where every dollar goes!"</p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">JD</div>
                        <div>
                            <h5 class="mb-0">John Doe</h5>
                            <small class="text-muted">Small Business Owner</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4 mb-md-0" data-aos="flip-left" data-aos-delay="200">
            <div class="card h-100 testimonial-card">
                <div class="card-body p-4">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="mb-4">"I've tried many finance apps, but this one stands out with its simplicity and powerful insights. Highly recommended!"</p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">JS</div>
                        <div>
                            <h5 class="mb-0">Jane Smith</h5>
                            <small class="text-muted">Financial Analyst</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="flip-left" data-aos-delay="300">
            <div class="card h-100 testimonial-card">
                <div class="card-body p-4">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="mb-4">"The visual reports helped me identify unnecessary expenses I wasn't aware of. I've already saved hundreds just in the first month!"</p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">MJ</div>
                        <div>
                            <h5 class="mb-0">Mike Johnson</h5>
                            <small class="text-muted">Freelance Designer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once $rootPath . '/includes/footer.php';
?>
