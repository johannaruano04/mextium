document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-publicar');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Obtén los valores del formulario
        const nombre = document.getElementById('nombre').value;
        const precio = document.getElementById('precio').value;
        const stock = document.getElementById('stock').value;
        const categoria = document.getElementById('categoria').value;
        const estado = document.getElementById('estado').value;
        const descripcion = document.getElementById('descripcion').value;
        const imagen = document.getElementById('imagen').value; // Si es file, puedes usar FileReader

        // Crea el objeto producto
        const producto = { nombre, precio, stock, categoria, estado, descripcion, imagen };

        // Guarda en localStorage
        let productos = JSON.parse(localStorage.getItem('misProductos') || '[]');
        productos.push(producto);
        localStorage.setItem('misProductos', JSON.stringify(productos));

        // Redirige a mis_productos.html
        window.location.href = 'mis_productos.html';
    });
});