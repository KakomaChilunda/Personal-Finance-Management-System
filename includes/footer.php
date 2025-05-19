    </div>
    
    <!-- Footer Section -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4"><?php echo SITE_NAME; ?></h5>
                    <p class="small">
                        Your trusted personal finance manager designed to help you take control of your finances with easy tracking, insightful reports, and smart budgeting tools.
                    </p>
                    <div class="mt-4">
                        <!-- Social Media Icons -->
                        <a href="#" class="btn btn-outline-light btn-floating me-2" data-aos="fade-up" data-aos-delay="100">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-floating me-2" data-aos="fade-up" data-aos-delay="200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-floating me-2" data-aos="fade-up" data-aos-delay="300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-floating" data-aos="fade-up" data-aos-delay="400">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="index.php" class="text-white text-decoration-none"><i class="fas fa-home me-2"></i> Home</a>
                        </li>
                        <li class="mb-2">
                            <a href="login.php" class="text-white text-decoration-none"><i class="fas fa-sign-in-alt me-2"></i> Login</a>
                        </li>
                        <li class="mb-2">
                            <a href="register.php" class="text-white text-decoration-none"><i class="fas fa-user-plus me-2"></i> Register</a>
                        </li>
                        <?php if(isLoggedIn()): ?>
                        <li class="mb-2">
                            <a href="dashboard.php" class="text-white text-decoration-none"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Features -->
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">Features</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-white text-decoration-none"><i class="fas fa-chart-line me-2"></i> Expense Tracking</a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-white text-decoration-none"><i class="fas fa-chart-pie me-2"></i> Financial Reports</a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-white text-decoration-none"><i class="fas fa-tags me-2"></i> Category Management</a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-white text-decoration-none"><i class="fas fa-file-export me-2"></i> Data Export</a>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i> Lusaka, Zambia
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i> support@financemanager.com
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i> +260 97 8523028
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock me-2"></i> Mon - Fri: 9:00 AM - 5:00 PM
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <!-- Copyright -->
            <div class="row align-items-center">
                <div class="col-md-7 col-lg-8 text-md-start">
                    <p class="small mb-0">
                        © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-5 col-lg-4 text-md-end">
                    <a href="#" class="text-white text-decoration-none small me-3">Privacy Policy</a>
                    <a href="#" class="text-white text-decoration-none small me-3">Terms of Service</a>
                    <a href="#" class="text-white text-decoration-none small">FAQ</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <!-- Custom Animations JS -->
    <script src="assets/js/animations.js"></script>
    
    <!-- Initialize AOS -->
    <script>
        // Initialize AOS animation library
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    </script>
</body>
</html>
