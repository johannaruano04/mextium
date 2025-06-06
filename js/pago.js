document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    if (!form) return;

    // Simulación de usuarios registrados (puedes reemplazar por consulta real)
    const usuariosRegistrados = [
        "juanperez@email.com",
        "anagomez@email.com",
        "carlosruiz@email.com"
    ];

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Obtener valores
        const nombre = document.getElementById('nombre').value.trim();
        const numero = document.getElementById('numero').value.replace(/\s+/g, '');
        const expira = document.getElementById('expira').value.trim();
        const cvv = document.getElementById('cvv').value.trim();
        const direccion = document.getElementById('direccion').value.trim();
        const correo = document.getElementById('correo').value.trim();

        // Validaciones
        if (!nombre || !numero || !expira || !cvv || !direccion || !correo) {
            alert('Por favor, completa todos los campos.');
            return;
        }

        if (!/^\d{16}$/.test(numero)) {
            alert('El número de tarjeta debe contener exactamente 16 dígitos numéricos.');
            return;
        }

        if (!/^\d{2}\/\d{2}$/.test(expira)) {
            alert('La fecha de expiración debe tener el formato MM/AA.');
            return;
        }

        const [mes, anio] = expira.split('/').map(Number);
        if (mes < 1 || mes > 12) {
            alert('El mes de expiración es inválido.');
            return;
        }

        if (!/^\d{3,4}$/.test(cvv)) {
            alert('El CVV debe tener 3 o 4 dígitos numéricos.');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
            alert('El correo electrónico no es válido.');
            return;
        }

        // Verificar si el correo está registrado
        if (!usuariosRegistrados.includes(correo.toLowerCase())) {
            alert('El correo electrónico no está registrado. Solo los usuarios registrados pueden pagar.');
            return;
        }

        // Si todo está bien, puedes enviar el formulario (aquí solo mostramos mensaje)
        alert('Pago procesado correctamente.');
        // form.submit(); // Descomenta si quieres enviar el formulario realmente
    });

    // Solo permitir números en el campo de tarjeta y CVV
    document.getElementById('numero').addEventListener('input', function () {
        this.value = this.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
    });
    document.getElementById('cvv').addEventListener('input', function () {
        this.value = this.value.replace(/[^\d]/g, '');
    });
    document.getElementById('expira').addEventListener('input', function () {
        let val = this.value.replace(/[^\d]/g, '');
        if (val.length > 2) val = val.slice(0,2) + '/' + val.slice(2,4);
        this.value = val.slice(0,5);
    });
});