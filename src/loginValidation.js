document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    const errorMessages = {
        email: {
            valueMissing: 'Email is required',
            typeMismatch: 'Please enter a valid email address'
        },
        password: {
            valueMissing: 'Password is required',
            tooShort: 'Password must be at least 8 characters long',
        }
    };

    // Hide error messages for email
    email.addEventListener('input', function() {
        if (!email.validity.valid) {
            emailError.textContent = email.validity.valueMissing ? 
                errorMessages.email.valueMissing : errorMessages.email.typeMismatch;
            emailError.style.display = 'block';
        } else {
            emailError.textContent = '';
            emailError.style.display = 'none';
        }
    });

    // Hide error messages for password
    password.addEventListener('input', function() {
        if (!password.validity.valid) {
            passwordError.textContent = password.validity.valueMissing ? 
                errorMessages.password.valueMissing : errorMessages.password.tooShort;
            passwordError.style.display = 'block';
        } else {
            passwordError.textContent = '';
            passwordError.style.display = 'none';
        }
    });

    form.addEventListener('submit', function(event) {
        let isValid = true;

        if (!email.validity.valid || !password.validity.valid) {
            isValid = false;
            event.preventDefault();
        }

        if (isValid) {
            return true;
        }
    });
});