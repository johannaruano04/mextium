document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registro-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const apellido = document.getElementById('apellido').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm-password').value;

        if (!nombre || !apellido || !email || !password || !confirm) {
            alert('Por favor, completa todos los campos.');
            e.preventDefault();
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Ingresa un correo electrónico válido.');
            e.preventDefault();
            return;
        }

        if (password.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres.');
            e.preventDefault();
            return;
        }

        if (password !== confirm) {
            alert('Las contraseñas no coinciden.');
            e.preventDefault();
            return;
        }

        // Evita el envío y redirige manualmente
        e.preventDefault();
        // Guarda el nombre en localStorage y redirige
        localStorage.setItem('usuarioNombre', nombre);
        window.location.href = '../index.html'; // Ajusta la ruta si es necesario
    });
});