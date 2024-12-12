document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const fields = {
        firstName: document.getElementById('first_name'),
        lastName: document.getElementById('last_name'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        dob: document.getElementById('dob'),
        license: document.getElementById('license'),
        cardNumber: document.getElementById('card_number'),
        cardHolder: document.getElementById('card_holder'),
        expiryDate: document.getElementById('expiry_date'),
        cvv: document.getElementById('cvv')
    };

    const errors = {};
    Object.keys(fields).forEach(key => {
        errors[key] = document.getElementById(`${key}Error`) || createErrorElement(fields[key]);
    });

    // Validation patterns
    const patterns = {
        name: /^[A-Za-z0-9\s]+$/,
        email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        phone: /^\d{9}$/,
        cardNumber: /^[0-9]{16}$/,
        cvv: /^[0-9]{3,4}$/,
        expiryDate: /^(0[1-9]|1[0-2])\/([0-9]{2})$/
    };

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
        phone: {
            invalidFormat: 'If provided, phone number must be 9 digits'
        },
        dob: {
            valueMissing: 'Date of birth is required',
            underAge: 'You must be at least 13 years old'
        },
        cardNumber: {
            valueMissing: 'Card number is required',
            invalidFormat: 'Please enter a valid 16-digit card number'
        },
        cardHolder: {
            valueMissing: 'Card holder name is required',
            invalidFormat: 'Please enter a valid name'
        },
        expiryDate: {
            valueMissing: 'Expiry date is required',
            invalidFormat: 'Please enter a valid expiry date (MM/YY)',
            invalid: 'Card has expired'
        },
        cvv: {
            valueMissing: 'CVV is required',
            invalidFormat: 'Please enter a valid 3 or 4 digit CVV'
        }
    };

    function validateName(value, errorElement, type) {
        if (!value) {
            errorElement.textContent = errorMessages[type].valueMissing;
            errorElement.style.display = 'block';
            return false;
        }
        if (!patterns.name.test(value)) {
            errorElement.textContent = errorMessages[type].invalidFormat;
            errorElement.style.display = 'block';
            return false;
        }
        errorElement.style.display = 'none';
        return true;
    }

    function validateEmail(value) {
        if (!value) {
            errors.email.textContent = errorMessages.email.valueMissing;
            errors.email.style.display = 'block';
            return false;
        }
        if (!patterns.email.test(value)) {
            errors.email.textContent = errorMessages.email.invalidFormat;
            errors.email.style.display = 'block';
            return false;
        }
        errors.email.style.display = 'none';
        return true;
    }

    function validatePhone(value) {
        if (!value) {
            errors.phone.style.display = 'none';
            return true; // Phone is optional
        }
        if (!patterns.phone.test(value)) {
            errors.phone.textContent = errorMessages.phone.invalidFormat;
            errors.phone.style.display = 'block';
            return false;
        }
        errors.phone.style.display = 'none';
        return true;
    }

    function validateDOB(value) {
        if (!value) {
            errors.dob.textContent = errorMessages.dob.valueMissing;
            errors.dob.style.display = 'block';
            return false;
        }
        const birthDate = new Date(value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        if (age < 13) {
            errors.dob.textContent = errorMessages.dob.underAge;
            errors.dob.style.display = 'block';
            return false;
        }
        errors.dob.style.display = 'none';
        return true;
    }

    function validateExpiryDate(value) {
        if (!value) {
            errors.expiryDate.textContent = errorMessages.expiryDate.valueMissing;
            errors.expiryDate.style.display = 'block';
            return false;
        }
    
        if (!patterns.expiryDate.test(value)) {
            errors.expiryDate.textContent = errorMessages.expiryDate.invalidFormat;
            errors.expiryDate.style.display = 'block';
            return false;
        }
    
        const [month, yearStr] = value.split('/');
        const year = parseInt(yearStr, 10);
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear() % 100;
        const currentMonth = currentDate.getMonth() + 1;
    
        // Compare years and months
        if (year < currentYear || (year === currentYear && parseInt(month) <= currentMonth)) {
            errors.expiryDate.textContent = errorMessages.expiryDate.invalid;
            errors.expiryDate.style.display = 'block';
            return false;
        }
    
        errors.expiryDate.style.display = 'none';
        return true;
    }

    function validateCardNumber(value) {
        if (!value) {
            errors.cardNumber.textContent = errorMessages.cardNumber.valueMissing;
            errors.cardNumber.style.display = 'block';
            return false;
        }
        if (!patterns.cardNumber.test(value)) {
            errors.cardNumber.textContent = errorMessages.cardNumber.invalidFormat;
            errors.cardNumber.style.display = 'block';
            return false;
        }
        errors.cardNumber.textContent = '';
        errors.cardNumber.style.display = 'none';
        return true;
    }
    
    function validateCardHolder(value) {
        if (!value) {
            errors.cardHolder.textContent = errorMessages.cardHolder.valueMissing;
            errors.cardHolder.style.display = 'block';
            return false;
        }
        if (!patterns.name.test(value)) {
            errors.cardHolder.textContent = errorMessages.cardHolder.invalidFormat;
            errors.cardHolder.style.display = 'block';
            return false;
        }
        errors.cardHolder.textContent = '';
        errors.cardHolder.style.display = 'none';
        return true;
    }
    
    function validateCVV(value) {
        if (!value) {
            errors.cvv.textContent = errorMessages.cvv.valueMissing;
            errors.cvv.style.display = 'block';
            return false;
        }
        if (!patterns.cvv.test(value)) {
            errors.cvv.textContent = errorMessages.cvv.invalidFormat;
            errors.cvv.style.display = 'block';
            return false;
        }
        errors.cvv.textContent = '';
        errors.cvv.style.display = 'none';
        return true;
    }
    
    function validateCardDetails() {
        const isCardNumberValid = validateCardNumber(fields.cardNumber.value.trim());
        const isCardHolderValid = validateCardHolder(fields.cardHolder.value.trim());
        const isExpiryDateValid = validateExpiryDate(fields.expiryDate.value.trim());
        const isCVVValid = validateCVV(fields.cvv.value.trim());
    
        return isCardNumberValid && isCardHolderValid && isExpiryDateValid && isCVVValid;
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();
    
        const isFirstNameValid = validateName(fields.firstName.value.trim(), errors.firstName, 'firstName');
        const isLastNameValid = validateName(fields.lastName.value.trim(), errors.lastName, 'lastName');
        const isEmailValid = validateEmail(fields.email.value.trim());
        const isPhoneValid = validatePhone(fields.phone.value.trim());
        const isDOBValid = validateDOB(fields.dob.value);
        const areCardDetailsValid = validateCardDetails();
    
        if (isFirstNameValid && isLastNameValid && isEmailValid && 
            isPhoneValid && isDOBValid && areCardDetailsValid) {
            this.submit();
        }
    });

    function createErrorElement(field) {
        const error = document.createElement('div');
        error.classList.add('validation-message');
        field.parentNode.appendChild(error);
        return error;
    }
});