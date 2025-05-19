/**
 * Custom animations for Personal Finance Management System
 * Enhances user experience with smooth transitions and interactive elements
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add section fade-in animations on scroll
    const sections = document.querySelectorAll('.section-fade');
    
    // Intersection Observer to detect when elements enter viewport
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.2
    });
    
    // Observe all section-fade elements
    sections.forEach(section => {
        observer.observe(section);
    });
    
    // Add hover effects to navigation links
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.classList.add('nav-link-hover');
        });
        
        link.addEventListener('mouseleave', function() {
            this.classList.remove('nav-link-hover');
        });
    });
    
    // Add pulse animation to CTA buttons
    const ctaButtons = document.querySelectorAll('.btn-lg');
    ctaButtons.forEach(button => {
        button.classList.add('btn-pulse');
    });
    
    // Add logo rotation animation
    const logo = document.querySelector('.navbar-brand');
    if (logo) {
        const logoIcon = document.createElement('i');
        logoIcon.className = 'fas fa-chart-line me-2 logo-rotate';
        logo.prepend(logoIcon);
    }
    
    // Add feature icon animations
    const featureIcons = document.querySelectorAll('.fa-3x');
    featureIcons.forEach(icon => {
        icon.classList.add('feature-icon');
    });
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add card tilt effect on mouse move for testimonial cards
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    testimonialCards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const xc = rect.width/2;
            const yc = rect.height/2;
            
            const dx = (x - xc) / 20;
            const dy = (y - yc) / 20;
            
            this.style.transform = `perspective(1000px) rotateY(${dx}deg) rotateX(${-dy}deg) translateZ(10px)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateY(0) rotateX(0) translateZ(0)';
            this.style.transition = 'transform 0.5s ease';
        });
    });
});
