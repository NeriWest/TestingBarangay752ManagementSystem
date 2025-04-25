document.addEventListener('DOMContentLoaded', function () {
    // ... (keep existing navbar and mobile menu code) ...

    // Enhanced Password Toggle
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const icon = togglePassword.querySelector('i');

    togglePassword.addEventListener('click', function (e) {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      icon.classList.toggle('bi-eye-slash');
      icon.classList.toggle('bi-eye');
    });

    // Form Validation
    const form = document.querySelector('form');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    form.addEventListener('submit', function(e) {
      let isValid = true;

      // Username validation
      if (usernameInput.value.trim() === '') {
        usernameInput.classList.add('is-invalid');
        usernameInput.nextElementSibling.textContent = 'Please enter your username or phone number';
        isValid = false;
      } else {
        usernameInput.classList.remove('is-invalid');
      }

      // Password validation
      if (passwordInput.value.trim() === '') {
        passwordInput.classList.add('is-invalid');
        passwordInput.nextElementSibling.textContent = 'Please enter your password';
        isValid = false;
      } else {
        passwordInput.classList.remove('is-invalid');
      }

      if (!isValid) {
        e.preventDefault();
      }
    });

    // Real-time validation
    usernameInput.addEventListener('input', function() {
      if (this.value.trim() !== '') {
        this.classList.remove('is-invalid');
      }
    });

    passwordInput.addEventListener('input', function() {
      if (this.value.trim() !== '') {
        this.classList.remove('is-invalid');
      }
    });

    // Update copyright year
    document.getElementById('copyrightYear').textContent = new Date().getFullYear();
  });