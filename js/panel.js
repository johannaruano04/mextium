// Ejemplo de usuarios (esto normalmente vendría de una base de datos o localStorage)
let usuarios = JSON.parse(localStorage.getItem('usuarios')) || [
    { id: 1, nombre: "Juan Pérez", correo: "juanperez@email.com", rol: "usuario", estado: "Activo" },
    { id: 2, nombre: "Ana Gómez", correo: "anagomez@email.com", rol: "vendedor", estado: "Pendiente" },
    { id: 3, nombre: "Carlos Ruiz", correo: "carlosruiz@email.com", rol: "admin", estado: "Activo" }
];

// Guardar usuarios en localStorage
function guardarUsuarios() {
    localStorage.setItem('usuarios', JSON.stringify(usuarios));
}

// Renderizar la tabla de usuarios
function renderUsuarios(filtro = "todos") {
    const tbody = document.querySelector("table tbody");
    tbody.innerHTML = "";
    usuarios.forEach((u, idx) => {
        // Cambia "Pendiente" por "Inactivo" al mostrar
        let estadoMostrar = u.estado === "Pendiente" ? "Inactivo" : u.estado;

        // Mostrar "Baneado" o "Suspendido" solo para usuario y vendedor
        if ((u.rol === "usuario" || u.rol === "vendedor") && (u.estado === "Baneado" || u.estado === "Suspendido")) {
            estadoMostrar = u.estado;
        }

        const badgeClass =
            estadoMostrar === "Activo" ? "bg-success" :
            estadoMostrar === "Inactivo" ? "bg-warning text-dark" :
            estadoMostrar === "Baneado" ? "bg-danger" :
            estadoMostrar === "Suspendido" ? "bg-secondary" :
            "bg-warning text-dark";

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${u.id}</td>
            <td>${u.nombre}</td>
            <td>${u.correo}</td>
            <td>${u.rol}</td>
            <td>
                <span class="badge ${badgeClass}">
                    ${estadoMostrar}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-info btn-editar" data-idx="${idx}">Editar</button>
                <button class="btn btn-sm btn-danger btn-borrar" data-idx="${idx}">Eliminar</button>
                <button class="btn btn-sm btn-outline-danger btn-eliminar-cuenta" data-idx="${idx}">Solicitar eliminación</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Botón borrar
    document.querySelectorAll('.btn-borrar').forEach(btn => {
        btn.addEventListener('click', function() {
            const idx = this.dataset.idx;
            if (confirm("¿Seguro que deseas eliminar este usuario?")) {
                usuarios.splice(idx, 1);
                guardarUsuarios();
                renderUsuarios(document.getElementById('filtro-rol').value);
            }
        });
    });

    // Botón editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const idx = this.dataset.idx;
            const usuario = usuarios[idx];
            const nuevoNombre = prompt("Editar nombre:", usuario.nombre);
            const nuevoCorreo = prompt("Editar correo:", usuario.correo);
            const nuevoRol = prompt("Editar rol (usuario, vendedor, admin):", usuario.rol);
            const nuevoEstado = prompt("Editar estado (Activo, Inactivo, Baneado, Suspendido):", usuario.estado);

            // Validación de rol permitido
            const rolesPermitidos = ["usuario", "vendedor", "admin"];
            const estadosPermitidos = ["Activo", "Inactivo", "Baneado", "Suspendido", "Pendiente"];
            if (
                nuevoNombre !== null && nuevoNombre.trim() !== "" &&
                nuevoCorreo !== null && nuevoCorreo.trim() !== "" &&
                nuevoRol !== null && nuevoRol.trim() !== "" &&
                rolesPermitidos.includes(nuevoRol.trim().toLowerCase()) &&
                nuevoEstado !== null && nuevoEstado.trim() !== "" &&
                estadosPermitidos.map(e => e.toLowerCase()).includes(nuevoEstado.trim().toLowerCase())
            ) {
                usuarios[idx].nombre = nuevoNombre.trim();
                usuarios[idx].correo = nuevoCorreo.trim();
                usuarios[idx].rol = nuevoRol.trim().toLowerCase();
                usuarios[idx].estado = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1).toLowerCase();
                guardarUsuarios();
                renderUsuarios(document.getElementById('filtro-rol').value);
            } else if (nuevoRol !== null && !rolesPermitidos.includes(nuevoRol.trim().toLowerCase())) {
                alert("Solo puedes ingresar los roles: usuario, vendedor o admin.");
            } else if (nuevoEstado !== null && !estadosPermitidos.map(e => e.toLowerCase()).includes(nuevoEstado.trim().toLowerCase())) {
                alert("Solo puedes ingresar los estados: Activo, Inactivo, Baneado o Suspendido.");
            }
        });
    });

    // Botón solicitar eliminación de cuenta
    document.querySelectorAll('.btn-eliminar-cuenta').forEach(btn => {
        btn.addEventListener('click', function() {
            const idx = this.dataset.idx;
            const usuario = usuarios[idx];
            if (confirm(`¿Seguro que el usuario "${usuario.nombre}" quiere eliminar su cuenta? Esta acción no se puede deshacer.`)) {
                usuarios.splice(idx, 1);
                guardarUsuarios();
                renderUsuarios(document.getElementById('filtro-rol').value);
                alert("La cuenta ha sido eliminada.");
            }
        });
    });
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    renderUsuarios();

    const filtroRol = document.getElementById('filtro-rol');
    if (filtroRol) {
        filtroRol.addEventListener('change', function() {
            renderUsuarios(this.value);
        });
    }
});