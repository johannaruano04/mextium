<script>
    // Suponiendo que cada producto tiene una propiedad "stock" (cantidad máxima disponible)
    // Ejemplo de productos:
    let productos = [
        { nombre: "Producto 1", precio: 150, cantidad: 1, stock: 5, img: "../vista/img/product-1.jpg" },
        { nombre: "Producto 2", precio: 200, cantidad: 2, stock: 3, img: "../vista/img/product-2.jpg" }
    ];

    // Calcula totales dinámicamente en el carrito
    function actualizarTotales() {
        let subtotal = 0;
        document.querySelectorAll('tbody.align-middle tr').forEach(function(row) {
            const precio = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('$', '')) || 0;
            const cantidad = parseInt(row.querySelector('input[type="text"]').value) || 1;
            const total = precio * cantidad;
            row.querySelector('td:nth-child(4)').textContent = '$' + total.toFixed(2);
            subtotal += total;
        });
        // Actualiza el subtotal, IVA y total general
        const subtotalElem = document.querySelectorAll('.d-flex.justify-content-between.mb-3 h6')[1];
        if (subtotalElem) subtotalElem.textContent = '$' + subtotal.toFixed(2);

        const iva = subtotal * 0.07; // Por ejemplo, 7% de IVA
        const ivaElem = document.querySelectorAll('.d-flex.justify-content-between')[1]?.querySelector('h6.font-weight-medium');
        if (ivaElem) ivaElem.textContent = '$' + iva.toFixed(2);

        const totalGeneral = subtotal + iva;
        const totalElem = document.querySelectorAll('.d-flex.justify-content-between.mt-2 h5')[1];
        if (totalElem) totalElem.textContent = '$' + totalGeneral.toFixed(2);
    }

    function asignarEventos() {
        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.onclick = function() {
                const idx = this.dataset.idx;
                if (productos[idx].cantidad < productos[idx].stock) {
                    productos[idx].cantidad++;
                    renderCarrito();
                } else {
                    alert("No puedes agregar más de la cantidad disponible en stock.");
                }
            };
        });
        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.onclick = function() {
                const idx = this.dataset.idx;
                if (productos[idx].cantidad > 1) {
                    productos[idx].cantidad--;
                    renderCarrito();
                }
            };
        });
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.onclick = function() {
                const idx = this.dataset.idx;
                if (confirm("¿Seguro que deseas eliminar este producto del carrito?")) {
                    // Ejemplo al eliminar:
                    productos.splice(idx, 1);
                    localStorage.setItem('carrito', JSON.stringify(productos));
                    renderCarrito();
                }
            };
        });
    }

    // Espera a que el DOM esté listo para asignar eventos
    document.addEventListener('DOMContentLoaded', function() {
        // Botón +
        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.closest('.quantity').querySelector('input[type="text"]');
                input.value = parseInt(input.value) + 1;
                actualizarTotales();
            });
        });
        // Botón -
        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.closest('.quantity').querySelector('input[type="text"]');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    actualizarTotales();
                }
            });
        });
        // Actualiza totales al cargar
        actualizarTotales();
    });
</script>