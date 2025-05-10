const container = document.querySelector('.container');
const registerBtn = document.querySelector('.register-btn');
const loginBtn = document.querySelector('.login-btn');

registerBtn.addEventListener('click', () => {
    container.classList.add('active');
})

loginBtn.addEventListener('click', () => {
    container.classList.remove('active');
})
document.getElementById('registerForm').addEventListener('submit', function(e) {
    var password = document.getElementById('register_password');
    var confirm = document.getElementById('confirm_password');

    if (password.value !== confirm.value) {
        e.preventDefault();
        alert('Passwords do not match!');
    }
});

document.querySelectorAll('.eye-icon').forEach(function(toggleIcon) {
    toggleIcon.addEventListener('click', function() {
        const passwordField = this.previousElementSibling;
        if (passwordField && passwordField.tagName === 'INPUT') {
            const isPassword = passwordField.getAttribute('type') === 'password';
            passwordField.setAttribute('type', isPassword ? 'text' : 'password');
            this.classList.toggle('bxs-hide', !isPassword);
            this.classList.toggle('bxs-show', isPassword);
        }
    });
});