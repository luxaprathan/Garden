const Validation = {
    password(value) {
        if (value.length < 8) return 'Password must be at least 8 characters.';
        if (!/[A-Z]/.test(value)) return 'Password must contain at least one uppercase letter.';
        if (!/[a-z]/.test(value)) return 'Password must contain at least one lowercase letter.';
        return null;
    },

    phone(value) {
        const digits = value.replace(/\D/g, '');
        if (!/^\d{10}$/.test(digits)) return 'Phone number must be exactly 10 digits.';
        return null;
    },

    email(value) {
        if (!/^[^\s@]+@[^\s@]+\.com$/i.test(value)) {
            return 'Email must include @ and end with .com.';
        }
        return null;
    },

    nic(value) {
        if (!value) return null;
        if (!/^(\d{12}|\d{9}[A-Za-z])$/.test(value)) {
            return 'NIC must be 12 digits or 9 digits followed by one letter.';
        }
        return null;
    },

    ageFromDob(value) {
        if (!value) return null;
        const birth = new Date(value);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        if (age < 15) return 'Age cannot be less than 15 years.';
        return null;
    },

    price(value) {
        if (value === '' || isNaN(value) || Number(value) < 0) return 'Price must be a valid number.';
        return null;
    },

    quantity(value) {
        if (value === '' || isNaN(value) || Number(value) < 0) return 'Quantity cannot be less than 0.';
        return null;
    },

    confirmPassword(value, form) {
        const pwd = form.querySelector('[name="password"]');
        if (!pwd || value !== pwd.value) return 'Passwords do not match.';
        return null;
    },

    showError(input, message) {
        this.clearError(input);
        input.classList.add('input-error');
        const error = document.createElement('span');
        error.className = 'field-error';
        error.textContent = message;
        input.parentNode.insertBefore(error, input.nextSibling);
    },

    clearError(input) {
        input.classList.remove('input-error');
        const next = input.nextElementSibling;
        if (next && next.classList.contains('field-error')) {
            next.remove();
        }
    },

    validateForm(form, rules) {
        let valid = true;
        rules.forEach(({ name, validator }) => {
            const input = form.querySelector(`[name="${name}"]`);
            if (!input) return;
            const error = validator(input.value.trim(), form);
            if (error) {
                this.showError(input, error);
                valid = false;
            } else {
                this.clearError(input);
            }
        });
        return valid;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-validate]').forEach(form => {
        form.querySelectorAll('[data-rule]').forEach(input => {
            input.addEventListener('blur', () => {
                const rule = input.dataset.rule;
                if (Validation[rule]) {
                    const error = Validation[rule](input.value.trim(), form);
                    if (error) Validation.showError(input, error);
                    else Validation.clearError(input);
                }
            });
        });

        form.addEventListener('submit', (e) => {
            const rules = [];
            form.querySelectorAll('[data-rule]').forEach(input => {
                const rule = input.dataset.rule;
                if (Validation[rule]) {
                    rules.push({ name: input.name, validator: Validation[rule] });
                }
            });
            if (rules.length && !Validation.validateForm(form, rules)) {
                e.preventDefault();
            }
        });
    });
});
