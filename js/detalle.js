// ...dentro de tu render de productos destacados...
// prod.nombre es el nombre del producto
let url = 'detail.html?nombre=' + encodeURIComponent(prod.nombre);
html += `<a href="${url}" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-eye"></i> Detalles
        </a>`;