document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    // Validation patterns
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;

    const errorMessages = {
        email: {
            valueMissing: 'Email is required',
            invalidFormat: 'Please enter a valid email address'
        },
        password: {
            valueMissing: 'Password is required',
            invalidFormat: 'Password must contain at least 8 characters, one uppercase letter and one special character'
        }
    };

    function validateEmail(email) {
        if (!email) {
            emailError.textContent = errorMessages.email.valueMissing;
            emailError.style.display = 'block';
            return false;
        }
        if (!emailPattern.test(email)) {
            emailError.textContent = errorMessages.email.invalidFormat;
            emailError.style.display = 'block';
            return false;
        }
        emailError.style.display = 'none';
        return true;
    }

    function validatePassword(password) {
        if (!password) {
            passwordError.textContent = errorMessages.password.valueMissing;
            passwordError.style.display = 'block';
            return false;
        }
        if (!passwordPattern.test(password)) {
            passwordError.textContent = errorMessages.password.invalidFormat;
            passwordError.style.display = 'block';
            return false;
        }
        passwordError.style.display = 'none';
        return true;
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const isEmailValid = validateEmail(email.value.trim());
        const isPasswordValid = validatePassword(password.value);

        if (isEmailValid && isPasswordValid) {
            this.submit();
        }
    });
});