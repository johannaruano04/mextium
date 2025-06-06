document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="login.php"]');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        if (!email || !password) {
            alert('Por favor, completa todos los campos.');
            e.preventDefault();
        }
    });
});