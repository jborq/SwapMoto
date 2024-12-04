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

    const errorMessages = {
        firstName: {
            valueMissing: 'First name is required',
            patternMismatch: 'Please enter a valid name'
        },
        lastName: {
            valueMissing: 'Last name is required',
            patternMismatch: 'Please enter a valid name'
        },
        email: {
            valueMissing: 'Email is required',
            typeMismatch: 'Please enter a valid email address'
        },
        phone: {
            valueMissing: 'Phone number is required',
            patternMismatch: 'Please enter a valid phone number'
        },
        dob: {
            valueMissing: 'Date of birth is required',
            underAge: 'You must be at least 13 years old'
        },
        cardNumber: {
            valueMissing: 'Card number is required',
            patternMismatch: 'Please enter a valid 16-digit card number'
        },
        cardHolder: {
            valueMissing: 'Card holder name is required',
            patternMismatch: 'Please enter a valid name'
        },
        expiryDate: {
            valueMissing: 'Expiry date is required',
            invalid: 'Please enter a valid future date'
        },
        cvv: {
            valueMissing: 'CVV is required',
            patternMismatch: 'Please enter a valid 3 or 4 digit CVV'
        }
    };

    function createErrorElement(field) {
        const error = document.createElement('div');
        error.classList.add('validation-message');
        field.parentNode.appendChild(error);
        return error;
    }

    // Format first name as user types
    fields.firstName.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^a-zA-Z]/g, '');
        if (fields.firstName.validity.valueMissing) {
            errors.firstName.textContent = errorMessages.firstName.valueMissing;
            errors.firstName.style.display = 'block';
        } else {
            errors.firstName.textContent = '';
            errors.firstName.style.display = 'none';
        }
    });

    // Format last name as user types
    fields.lastName.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^a-zA-Z]/g, '');
        if (fields.lastName.validity.valueMissing) {
            errors.lastName.textContent = errorMessages.lastName.valueMissing;
            errors.lastName.style.display = 'block';
        } else {
            errors.lastName.textContent = '';
            errors.lastName.style.display = 'none';
        }
    });

    // Format email as user types
    fields.email.addEventListener('input', function(e) {
        e.target.value = e.target.value.toLowerCase();
        if (fields.email.validity.valueMissing) {
            errors.email.textContent = errorMessages.email.valueMissing;
            errors.email.style.display = 'block';
        } else if (!fields.email.validity.valid) {
            errors.email.textContent = errorMessages.email.typeMismatch;
            errors.email.style.display = 'block';
        } else {
            errors.email.textContent = '';
            errors.email.style.display = 'none';
        }
    });

    // Format phone number as user types
    fields.phone.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0,9);
        if (fields.phone.validity.valueMissing) {
            errors.phone.textContent = errorMessages.phone.valueMissing;
            errors.phone.style.display = 'block';
        } else if (!fields.phone.validity.valid) {
            errors.phone.textContent = errorMessages.phone.patternMismatch;
            errors.phone.style.display = 'block';
        } else {
            errors.phone.textContent = '';
            errors.phone.style.display = 'none';
        }
    });

    // Format card number as user types
    fields.cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '').substring(0,16);
        let formattedValue = '';
        for(let i = 0; i < value.length; i++) {
            if(i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }
        e.target.value = formattedValue;
    });

    // Format expiry date as user types
    fields.expiryDate.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0,2) + '/' + value.substring(2,4);
        }
        e.target.value = value;
    });
    

    // Validate date of birth
    fields.dob.addEventListener('change', function() {
        const birthDate = new Date(this.value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if (age < 13) {
            errors.dob.textContent = errorMessages.dob.underAge;
            errors.dob.style.display = 'block';
        } else {
            errors.dob.style.display = 'none';
        }
    });

    // Validate expiry date
    fields.expiryDate.addEventListener('change', function() {
        const [month, year] = this.value.split('/');
        const expiry = new Date(2000 + parseInt(year), parseInt(month) - 1);
        const today = new Date();
        
        if (expiry < today) {
            errors.expiryDate.textContent = errorMessages.expiryDate.invalid;
            errors.expiryDate.style.display = 'block';
        } else {
            errors.expiryDate.style.display = 'none';
        }
    });

    // Validate card number
    fields.cardNumber.addEventListener('input', function() {
        if (fields.cardNumber.validity.valueMissing) {
            errors.cardNumber.textContent = errorMessages.cardNumber.valueMissing;
            errors.cardNumber.style.display = 'block';
        } else if (!fields.cardNumber.validity.valid) {
            errors.cardNumber.textContent = errorMessages.cardNumber.patternMismatch;
            errors.cardNumber.style.display = 'block';
        } else {
            errors.cardNumber.textContent = '';
            errors.cardNumber.style.display = 'none';
        }
    });

    // Validate CVV
    fields.cvv.addEventListener('input', function() {
        if (fields.cvv.validity.valueMissing) {
            errors.cvv.textContent = errorMessages.cvv.valueMissing;
            errors.cvv.style.display = 'block';
        } else if (!fields.cvv.validity.valid) {
            errors.cvv.textContent = errorMessages.cvv.patternMismatch;
            errors.cvv.style.display = 'block';
        } else {
            errors.cvv.textContent = '';
            errors.cvv.style.display = 'none';
        }
    });

    // Validate card holder name
    fields.cardHolder.addEventListener('input', function(e) {
        if (fields.cardHolder.validity.valueMissing) {
            errors.cardHolder.textContent = errorMessages.cardHolder.valueMissing;
            errors.cardHolder.style.display = 'block';
        } else {
            errors.cardHolder.textContent = '';
            errors.cardHolder.style.display = 'none';
        }
    });

    form.addEventListener('submit', function(event) {
        let isValid = true;

        // Validate all fields
        Object.keys(fields).forEach(key => {
            const field = fields[key];
            if (!field.checkValidity()) {
                isValid = false;
                errors[key].textContent = field.validity.valueMissing ? 
                    errorMessages[key].valueMissing : 
                    errorMessages[key].patternMismatch;
                errors[key].style.display = 'block';
            }
        });

        // Additional date of birth validation
        const birthDate = new Date(fields.dob.value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        if (age < 13) {
            isValid = false;
            errors.dob.textContent = errorMessages.dob.underAge;
            errors.dob.style.display = 'block';
        }

        // Additional expiry date validation
        const [month, year] = fields.expiryDate.value.split('/');
        const expiry = new Date(2000 + parseInt(year), parseInt(month) - 1);
        if (expiry < today) {
            isValid = false;
            errors.expiryDate.textContent = errorMessages.expiryDate.invalid;
            errors.expiryDate.style.display = 'block';
        }

        if (!isValid) {
            event.preventDefault();
        }
    });
});