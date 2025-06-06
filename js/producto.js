// Cargar productos desde localStorage al iniciar
function cargarProductos() {
    const productos = JSON.parse(localStorage.getItem('productos')) || [];
    const tbody = document.querySelector('table tbody');
    tbody.innerHTML = '';
    productos.forEach((prod, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><img src="${prod.imgSrc}" alt="Foto producto" class="product-img-thumb"></td>
            <td>${prod.nombre}</td>
            <td>${prod.categoria}</td>
            <td>$${parseFloat(prod.precio).toLocaleString()}</td>
            <td>${prod.stock}</td>
            <td>
                <button class="btn btn-sm btn-info btn-editar" data-idx="${idx}"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger btn-borrar" data-idx="${idx}"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Botones borrar
    document.querySelectorAll('.btn-borrar').forEach(btn => {
        btn.addEventListener('click', function() {
            borrarProducto(this.dataset.idx);
        });
    });

    // Botones editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            editarProducto(this.dataset.idx);
        });
    });
}

// Guardar producto en localStorage
function guardarProducto(prod, idx = null) {
    const productos = JSON.parse(localStorage.getItem('productos')) || [];
    if (idx !== null) {
        productos[idx] = prod; // Editar
    } else {
        productos.push(prod); // Nuevo
    }
    localStorage.setItem('productos', JSON.stringify(productos));
}

// Borrar producto
function borrarProducto(idx) {
    const productos = JSON.parse(localStorage.getItem('productos')) || [];
    productos.splice(idx, 1);
    localStorage.setItem('productos', JSON.stringify(productos));
    cargarProductos();
}

// Editar producto
function editarProducto(idx) {
    const productos = JSON.parse(localStorage.getItem('productos')) || [];
    const prod = productos[idx];

    // Rellenar el formulario del modal
    document.getElementById('previewImg').src = prod.imgSrc;
    document.querySelector('#modalProducto input[type="text"]').value = prod.nombre;
    document.querySelector('#modalProducto select').value = prod.categoria;
    document.querySelectorAll('#modalProducto input[type="number"]')[0].value = prod.precio;
    document.querySelectorAll('#modalProducto input[type="number"]')[1].value = prod.stock;

    // Guardar el índice en un atributo del formulario
    document.querySelector('#modalProducto form').setAttribute('data-edit', idx);

    // Mostrar el modal
    $('#modalProducto').modal('show');
}

// Vista previa de la imagen seleccionada
document.getElementById('inputImg').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            document.getElementById('previewImg').src = evt.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Agregar o editar producto
document.querySelector('#modalProducto form').addEventListener('submit', function(e) {
    e.preventDefault();

    const nombre = this.querySelector('input[type="text"]').value;
    const categoria = this.querySelector('select').value;
    const precio = this.querySelectorAll('input[type="number"]')[0].value;
    const stock = this.querySelectorAll('input[type="number"]')[1].value;
    let imgSrc = document.getElementById('previewImg').src;

    if (imgSrc.includes('placeholder')) {
        imgSrc = 'https://via.placeholder.com/60x60.png?text=Foto';
    }

    const producto = { nombre, categoria, precio, stock, imgSrc };

    // Revisar si es edición o nuevo
    const idx = this.getAttribute('data-edit');
    if (idx !== null) {
        guardarProducto(producto, idx);
        this.removeAttribute('data-edit');
    } else {
        guardarProducto(producto);
    }

    cargarProductos();

    // Limpiar formulario y cerrar modal
    this.reset();
    document.getElementById('previewImg').src = 'https://via.placeholder.com/100x100.png?text=Foto';
    $('#modalProducto').modal('hide');
});

// Al cargar la página, mostrar productos guardados
window.addEventListener('DOMContentLoaded', cargarProductos);
