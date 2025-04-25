document.addEventListener('DOMContentLoaded', function () {
    // Navbar Scroll Effect
    const navbar = document.getElementById('mainNavbar');
    window.addEventListener('scroll', function () {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Mobile Menu Toggle
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

    // Phone Number Validation
    const phoneInput = document.getElementById('phone');
    const form = document.getElementById('forgotPasswordForm');
    
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        
        // Auto-format to 09 prefix
        if (!value.startsWith('09') && value.length > 0) {
            value = '09' + value.slice(2);
        }
        
        // Limit to 11 digits
        if (value.length > 11) {
            value = value.slice(0, 11);
        }
        
        this.value = value;
        
        // Validation styling
        if (value.length === 11 && /^09\d{9}$/.test(value)) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            if (value.length > 0) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        }
    });

    // Prevent pasting invalid formats
    phoneInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedData = (e.clipboardData || window.clipboardData).getData('text');
        const sanitizedData = pastedData.replace(/[^0-9]/g, '').slice(0, 11);
        if (sanitizedData.match(/^09[0-9]{0,9}$/)) {
            phoneInput.value = sanitizedData;
        }
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const phoneValue = phoneInput.value;
        if (!/^09\d{9}$/.test(phoneValue)) {
            e.preventDefault();
            phoneInput.classList.add('is-invalid');
            phoneInput.focus();
        }
    });

    // Update copyright year
    document.getElementById('copyrightYear').textContent = new Date().getFullYear();
});