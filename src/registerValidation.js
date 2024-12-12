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

    // Validation patterns
    const namePattern = /^[A-Za-z0-9]+$/;
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;

    const errorMessages = {
        firstName: {
            valueMissing: 'First name is required',
            invalidFormat: 'First name can only contain letters'
        },
        lastName: {
            valueMissing: 'Last name is required',
            invalidFormat: 'Last name can only contain letters'
        },
        email: {
            valueMissing: 'Email is required',
            invalidFormat: 'Please enter a valid email address'
        },
        password: {
            valueMissing: 'Password is required',
            invalidFormat: 'Password must contain at least 8 characters, one uppercase letter and one special character'
        },
        confirmPassword: {
            valueMissing: 'Please confirm your password',
            mismatch: 'Passwords do not match'
        }
    };

    function validateName(name, errorElement, type) {
        if (!name) {
            errorElement.textContent = errorMessages[type].valueMissing;
            errorElement.style.display = 'block';
            return false;
        }
        if (!namePattern.test(name)) {
            errorElement.textContent = errorMessages[type].invalidFormat;
            errorElement.style.display = 'block';
            return false;
        }
        errorElement.style.display = 'none';
        return true;
    }

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

    function validateConfirmPassword(password, confirmPass) {
        if (!confirmPass) {
            confirmPasswordError.textContent = errorMessages.confirmPassword.valueMissing;
            confirmPasswordError.style.display = 'block';
            return false;
        }
        if (password !== confirmPass) {
            confirmPasswordError.textContent = errorMessages.confirmPassword.mismatch;
            confirmPasswordError.style.display = 'block';
            return false;
        }
        confirmPasswordError.style.display = 'none';
        return true;
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const isFirstNameValid = validateName(firstName.value.trim(), firstNameError, 'firstName');
        const isLastNameValid = validateName(lastName.value.trim(), lastNameError, 'lastName');
        const isEmailValid = validateEmail(email.value.trim());
        const isPasswordValid = validatePassword(password.value);
        const isConfirmPasswordValid = validateConfirmPassword(password.value, confirmPassword.value);

        if (isFirstNameValid && isLastNameValid && isEmailValid && 
            isPasswordValid && isConfirmPasswordValid) {
            this.submit();
        }
    });
});