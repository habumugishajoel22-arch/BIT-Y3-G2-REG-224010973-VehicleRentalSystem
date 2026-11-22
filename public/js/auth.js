// Client-side validation for the register form (UX only - the server validates again)
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('register-form');
    if (!form) return;

    const setError = (name, message) => {
        const box = form.querySelector('[data-error-for="' + name + '"]');
        if (box) box.textContent = message;
    };

    form.addEventListener('submit', (event) => {
        const username = form.username.value.trim();
        const email    = form.email.value.trim();
        const password = form.password.value;
        const confirm  = form.confirm_password.value;
        let valid = true;

        ['username', 'email', 'password', 'confirm_password'].forEach((n) => setError(n, ''));

        if (!/^[A-Za-z0-9_]{3,50}$/.test(username)) {
            setError('username', 'Username must be 3-50 characters: letters, numbers, underscore.');
            valid = false;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setError('email', 'Enter a valid email address.');
            valid = false;
        }
        if (password.length < 8) {
            setError('password', 'Password must be at least 8 characters.');
            valid = false;
        }
        if (password !== confirm) {
            setError('confirm_password', 'Passwords do not match.');
            valid = false;
        }

        if (!valid) event.preventDefault(); // stop the form from being sent
    });
});
