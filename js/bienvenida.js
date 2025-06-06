<script src="../js/bienvenida.js"></script>

document.addEventListener('DOMContentLoaded', function() {
    var nombre = localStorage.getItem('usuarioNombre');
    // Mostrar el nombre en el encabezado
    var nombreElem = document.getElementById('usuario-nombre');
    if (nombre && nombreElem) {
        nombreElem.textContent = '¡Hola, ' + nombre + '!';
    }
    // Ocultar los botones de registro, ingreso y "¿Olvidaste tu contraseña?" si hay usuario
    var authBtns = document.getElementById('auth-buttons');
    if (nombre && authBtns) {
        authBtns.innerHTML = '';
        // Botón cerrar sesión
        var logoutBtn = document.createElement('button');
        logoutBtn.className = 'btn btn-outline-danger btn-sm mt-2';
        logoutBtn.textContent = 'Cerrar sesión';
        logoutBtn.onclick = function() {
            localStorage.removeItem('usuarioNombre');
            window.location.reload();
        };
        authBtns.appendChild(logoutBtn);
    }
});