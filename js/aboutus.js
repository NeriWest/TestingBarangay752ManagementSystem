

// Nav
document.addEventListener('DOMContentLoaded', function () {
    // Navbar scroll effect
    const navbar = document.getElementById('mainNavbar');
    window.addEventListener('scroll', function () {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    mobileMenuButton.addEventListener('click', function () {
        if (mobileMenu.style.display === 'none' || !mobileMenu.style.display) {
            mobileMenu.style.display = 'block';
            this.innerHTML = '<i class="bi bi-x-lg"></i>';
        } else {
            mobileMenu.style.display = 'none';
            this.innerHTML = '<span class="navbar-toggler-icon"></span>';
        }
    });

    // Close mobile menu when clicking a link
    const mobileLinks = document.querySelectorAll('.mobile-nav-link');
    mobileLinks.forEach(link => {
        link.addEventListener('click', function () {
            mobileMenu.style.display = 'none';
            mobileMenuButton.innerHTML = '<span class="navbar-toggler-icon"></span>';
        });
    });

    // Smooth scrolling for the CTA button
    const ctaButton = document.querySelector('.btn-hero-cta');
    if (ctaButton) {
        ctaButton.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId.startsWith('#')) {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    }

    // Configuration function (can be called from console if needed)
    window.configureHeroSection = function (config = {}) {
        const defaults = {
            title: "Welcome to Barangay 752 Zone-81",
            subtitle: "Serving our community with dedication and excellence. Your trusted local government unit committed to providing efficient services and fostering community development.",
            backgroundImage: "https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=1920&q=80",
            logoSrc: "https://api.dicebear.com/7.x/shapes/svg?seed=barangay752&backgroundColor=1E3A8A",
            ctaText: "Explore Services",
            ctaLink: "#services"
        };

        const settings = { ...defaults, ...config };

        const titleElement = document.querySelector('.hero-title');
        const subtitleElement = document.querySelector('.hero-subtitle');
        const backgroundElement = document.querySelector('.hero-background');
        const logoElement = document.querySelector('.hero-logo');
        const ctaElement = document.querySelector('.btn-hero-cta');

        if (titleElement) titleElement.textContent = settings.title;
        if (subtitleElement) subtitleElement.textContent = settings.subtitle;
        if (backgroundElement) backgroundElement.style.backgroundImage = `url(${settings.backgroundImage})`;
        if (logoElement) logoElement.src = settings.logoSrc;
        if (ctaElement) {
            ctaElement.textContent = settings.ctaText;
            ctaElement.href = settings.ctaLink;
        }
    }
});

// End nav

// Initialize animations
document.addEventListener('DOMContentLoaded', function () {
    // Immediately animate hero section
    document.querySelector('.animate-opacity-y').classList.add('animated');

    // Configure intersection observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all animate elements except the hero section
    document.querySelectorAll('.animate-opacity-y:not(.animated), .animate-opacity-x-left, .animate-opacity-x-right').forEach(element => {
        observer.observe(element);
    });

    // Add hover effect to navbar links
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', () => {
            link.classList.add('hover-text-primary');
        });
        link.addEventListener('mouseleave', () => {
            if (!link.classList.contains('active')) {
                link.classList.remove('hover-text-primary');
            }
        });
    });
});




// ORGANIZATION CHART



//Footer
// Set current year in copyright
document.getElementById('copyrightYear').textContent = new Date().getFullYear();

// Optional: Add resize event listener for debugging transitions
window.addEventListener('resize', function () {
    console.log('Window resized to:', window.innerWidth, '×', window.innerHeight);
});



