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



        /* Services */

        // You can add any interactive functionality here if needed
        document.addEventListener('DOMContentLoaded', function () {
            // Example: Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Optional: Add animation effects when scrolling to services
            const observerOptions = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.service-card').forEach(card => {
                observer.observe(card);
            });
        });

        /* Office Hours */

        document.addEventListener('DOMContentLoaded', function () {
            const officeHours = [
                { day: "Monday", hours: "8:00 AM - 5:00 PM" },
                { day: "Tuesday", hours: "8:00 AM - 5:00 PM" },
                { day: "Wednesday", hours: "8:00 AM - 5:00 PM" },
                { day: "Thursday", hours: "8:00 AM - 5:00 PM" },
                { day: "Friday", hours: "8:00 AM - 5:00 PM" },
                { day: "Saturday", hours: "8:00 AM - 12:00 PM" },
                { day: "Sunday", hours: "Closed" }
            ];

            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const today = new Date().getDay();
            const currentDay = days[today];
            document.getElementById("currentDay").textContent = currentDay;

            const scheduleContainer = document.getElementById("scheduleContainer");

            officeHours.forEach(schedule => {
                const isActive = schedule.day === currentDay;

                const card = document.createElement("div");
                card.className = `schedule-card ${isActive ? "active" : ""}`;

                card.innerHTML = `
                    <h4 class="schedule-day">${schedule.day}</h4>
                    <div class="schedule-hours">
                        <i class="bi bi-clock"></i>
                        <span>${schedule.hours}</span>
                    </div>
                `;

                scheduleContainer.appendChild(card);
            });
        });




        // Footer
        // Set current year in copyright
        document.getElementById('copyrightYear').textContent = new Date().getFullYear();

        // Optional: Add resize event listener for debugging transitions
        window.addEventListener('resize', function () {
            console.log('Window resized to:', window.innerWidth, '×', window.innerHeight);
        });