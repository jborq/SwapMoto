document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const firstName = document.getElementById('first_name');
    const lastName = document.getElementById('last_name');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    const firstNameError = document.getElementById('firstNameError');
    const lastNameError = document.getElementById('lastNameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');

    const errorMessages = {
        firstName: {
            valueMissing: 'First name is required',
            patternMismatch: 'First name can only contain letters'
        },
        lastName: {
            valueMissing: 'Last name is required',
            patternMismatch: 'Last name can only contain letters'
        },
        email: {
            valueMissing: 'Email is required',
            typeMismatch: 'Please enter a valid email address'
        },
        password: {
            valueMissing: 'Password is required',
            patternMismatch: 'Password must contain at least 8 characters, one uppercase letter and one special character'
        },
        confirmPassword: {
            valueMissing: 'Please confirm your password',
            mismatch: 'Passwords do not match'
        }
    };

    // First name validation
    firstName.addEventListener('input', function() {
        if (!firstName.validity.valid) {
            firstNameError.textContent = firstName.validity.valueMissing ? 
                errorMessages.firstName.valueMissing : errorMessages.firstName.patternMismatch;
            firstNameError.style.display = 'block';
        } else {
            firstNameError.textContent = '';
            firstNameError.style.display = 'none';
        }
    });

    // Last name validation
    lastName.addEventListener('input', function() {
        if (!lastName.validity.valid) {
            lastNameError.textContent = lastName.validity.valueMissing ? 
                errorMessages.lastName.valueMissing : errorMessages.lastName.patternMismatch;
            lastNameError.style.display = 'block';
        } else {
            lastNameError.textContent = '';
            lastNameError.style.display = 'none';
        }
    });

    // Email validation
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

    // Password validation
    password.addEventListener('input', function() {
        if (!password.validity.valid) {
            passwordError.textContent = password.validity.valueMissing ? 
                errorMessages.password.valueMissing : errorMessages.password.patternMismatch;
            passwordError.style.display = 'block';
        } else {
            passwordError.textContent = '';
            passwordError.style.display = 'none';
        }
    });

    // Confirm password validation
    confirmPassword.addEventListener('input', function() {
        if (confirmPassword.validity.valueMissing) {
            confirmPasswordError.textContent = errorMessages.confirmPassword.valueMissing;
            confirmPasswordError.style.display = 'block';
        } else if (password.value !== confirmPassword.value) {
            confirmPasswordError.textContent = errorMessages.confirmPassword.mismatch;
            confirmPasswordError.style.display = 'block';
        } else {
            confirmPasswordError.textContent = '';
            confirmPasswordError.style.display = 'none';
        }
    });

    // Form submission
    form.addEventListener('submit', function(event) {
        let isValid = true;

        if (!firstName.validity.valid || !lastName.validity.valid || 
            !email.validity.valid || !password.validity.valid || 
            !confirmPassword.validity.valid || 
            password.value !== confirmPassword.value) {
            isValid = false;
            event.preventDefault();
        }

        if (isValid) {
            return true;
        }
    });
});