// Estado global
let usuarioActual = null;
let carritoActual = null;
let paginaActual = 1;
let categorias = [];

// Inicializar aplicación
document.addEventListener('DOMContentLoaded', () => {
    verificarSesion();
    cargarCategorias();
    cargarProductosDestacados();
    actualizarContadorCarrito();
});

// ============= AUTENTICACIÓN =============

async function verificarSesion() {
    try {
        const formData = new FormData();
        formData.append('accion', 'verificar_sesion');
        
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.logueado) {
            usuarioActual = data.usuario;
            actualizarInterfazUsuario();
        } else {
            mostrarBotonesLogin();
        }
    } catch (error) {
        console.error('Error al verificar sesión:', error);
        mostrarBotonesLogin();
    }
}

function mostrarBotonesLogin() {
    document.getElementById('userButtons').innerHTML = `
        <button class="btn btn-secondary" onclick="mostrarLogin()">Iniciar Sesión</button>
        <button class="btn btn-primary" onclick="mostrarRegistro()">Registrarse</button>
    `;
}

function actualizarInterfazUsuario() {
    document.getElementById('userButtons').innerHTML = `
        <span style="color: white;">Hola, ${usuarioActual.nombre}</span>
        <button class="btn btn-secondary" onclick="logout()">Cerrar Sesión</button>
    `;
}

async function login(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('accion', 'login');
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            usuarioActual = data.usuario;
            actualizarInterfazUsuario();
            cerrarModal('modalLogin');
            mostrarMensaje('Sesión iniciada correctamente');
            actualizarContadorCarrito();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('Error al iniciar sesión');
    }
}

async function registro(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('accion', 'registro');
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            usuarioActual = data.usuario;
            actualizarInterfazUsuario();
            cerrarModal('modalRegistro');
            mostrarMensaje('Registro exitoso');
            actualizarContadorCarrito();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('Error al registrarse');
    }
}

async function logout() {
    const formData = new FormData();
    formData.append('accion', 'logout');
    
    try {
        await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        usuarioActual = null;
        mostrarBotonesLogin();
        mostrarMensaje('Sesión cerrada');
        actualizarContadorCarrito();
    } catch (error) {
        alert('Error al cerrar sesión');
    }
}

// ============= PRODUCTOS =============

async function cargarCategorias() {
    try {
        const response = await fetch('productos.php?accion=categorias');
        const data = await response.json();
        
        if (data.success) {
            categorias = data.categorias;
            const select = document.getElementById('categoriaFilter');
            categorias.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.nombre;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
    }
}

async function cargarProductosDestacados() {
    try {
        const response = await fetch('productos.php?accion=destacados');
        const data = await response.json();
        
        if (data.success) {
            renderizarProductos(data.productos, 'destacadosGrid');
        }
    } catch (error) {
        console.error('Error al cargar productos destacados:', error);
    }
}

async function cargarProductos(pagina = 1) {
    const categoria = document.getElementById('categoriaFilter').value;
    const busqueda = document.getElementById('searchInput').value;
    const orden = document.getElementById('ordenFilter').value;
    
    const params = new URLSearchParams({
        accion: 'listar',
        pagina: pagina,
        orden: orden
    });
    
    if (categoria) params.append('categoria', categoria);
    if (busqueda) params.append('busqueda', busqueda);
    
    try {
        document.getElementById('productosGrid').innerHTML = '<div class="loading">Cargando productos...</div>';
        
        const response = await fetch(`productos.php?${params}`);
        const data = await response.json();
        
        if (data.success) {
            renderizarProductos(data.productos, 'productosGrid');
            renderizarPaginacion(data.pagina, data.total_paginas);
            paginaActual = data.pagina;
        }
    } catch (error) {
        console.error('Error al cargar productos:', error);
        document.getElementById('productosGrid').innerHTML = '<div class="loading">Error al cargar productos</div>';
    }
}

function renderizarProductos(productos, containerId) {
    const container = document.getElementById(containerId);
    
    if (productos.length === 0) {
        container.innerHTML = '<div class="loading">No se encontraron productos</div>';
        return;
    }
    
    container.innerHTML = productos.map(producto => `
        <div class="product-card">
            <div class="product-image">🎁</div>
            <div class="product-info">
                <div class="product-name">${producto.nombre}</div>
                <div class="product-description">${producto.descripcion || 'Sin descripción'}</div>
                <div class="product-price">€${parseFloat(producto.precio).toFixed(2)}</div>
                <div class="product-stock">Stock: ${producto.stock} unidades</div>
                <button class="btn-add-cart" onclick="agregarAlCarrito(${producto.id})">
                    Agregar al Carrito
                </button>
            </div>
        </div>
    `).join('');
}

function renderizarPaginacion(paginaActual, totalPaginas) {
    const container = document.getElementById('pagination');
    
    if (totalPaginas <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = `
        <button onclick="cargarProductos(${paginaActual - 1})" ${paginaActual === 1 ? 'disabled' : ''}>
            Anterior
        </button>
    `;
    
    for (let i = 1; i <= totalPaginas; i++) {
        html += `
            <button onclick="cargarProductos(${i})" class="${i === paginaActual ? 'active' : ''}">
                ${i}
            </button>
        `;
    }
    
    html += `
        <button onclick="cargarProductos(${paginaActual + 1})" ${paginaActual === totalPaginas ? 'disabled' : ''}>
            Siguiente
        </button>
    `;
    
    container.innerHTML = html;
}

// ============= CARRITO =============

async function agregarAlCarrito(productoId) {
    const formData = new FormData();
    formData.append('accion', 'agregar');
    formData.append('producto_id', productoId);
    formData.append('cantidad', 1);
    
    try {
        const response = await fetch('carrito.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarMensaje('Producto agregado al carrito');
            actualizarContadorCarrito();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('Error al agregar al carrito');
    }
}

async function obtenerCarrito() {
    try {
        const response = await fetch('carrito.php?accion=obtener');
        const data = await response.json();
        
        if (data.success) {
            carritoActual = data;
            return data;
        }
    } catch (error) {
        console.error('Error al obtener carrito:', error);
    }
    return null;
}

async function actualizarContadorCarrito() {
    const carrito = await obtenerCarrito();
    if (carrito) {
        document.getElementById('cartCount').textContent = carrito.cantidad_items || 0;
    }
}

async function mostrarCarrito() {
    const carrito = await obtenerCarrito();
    
    if (!carrito || carrito.items.length === 0) {
        document.getElementById('carritoItems').innerHTML = '<p style="text-align: center; padding: 2rem;">El carrito está vacío</p>';
        document.getElementById('carritoTotal').innerHTML = '';
        document.getElementById('modalCarrito').classList.add('active');
        return;
    }
    
    const itemsHtml = carrito.items.map(item => `
        <div class="cart-item">
            <div class="cart-item-name">
                <strong>${item.nombre}</strong>
                <div>€${parseFloat(item.precio_unitario).toFixed(2)} c/u</div>
            </div>
            <div class="quantity-controls">
                <button class="quantity-btn" onclick="cambiarCantidad(${item.id}, ${item.cantidad - 1})">-</button>
                <span style="padding: 0 1rem;">${item.cantidad}</span>
                <button class="quantity-btn" onclick="cambiarCantidad(${item.id}, ${item.cantidad + 1})">+</button>
            </div>
            <div style="font-weight: bold;">€${parseFloat(item.subtotal).toFixed(2)}</div>
            <button onclick="eliminarDelCarrito(${item.id})" style="border: none; background: none; cursor: pointer; font-size: 1.5rem;">🗑️</button>
        </div>
    `).join('');
    
    document.getElementById('carritoItems').innerHTML = itemsHtml;
    document.getElementById('carritoTotal').innerHTML = `Total: €${parseFloat(carrito.total).toFixed(2)}`;
    document.getElementById('modalCarrito').classList.add('active');
}

async function cambiarCantidad(itemId, nuevaCantidad) {
    if (nuevaCantidad < 0) return;
    
    const formData = new FormData();
    formData.append('accion', nuevaCantidad === 0 ? 'eliminar' : 'actualizar');
    formData.append('item_id', itemId);
    if (nuevaCantidad > 0) {
        formData.append('cantidad', nuevaCantidad);
    }
    
    try {
        const response = await fetch('carrito.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarCarrito();
            actualizarContadorCarrito();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('Error al actualizar carrito');
    }
}

async function eliminarDelCarrito(itemId) {
    if (!confirm('¿Eliminar este producto del carrito?')) return;
    
    const formData = new FormData();
    formData.append('accion', 'eliminar');
    formData.append('item_id', itemId);
    
    try {
        const response = await fetch('carrito.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarCarrito();
            actualizarContadorCarrito();
        }
    } catch (error) {
        alert('Error al eliminar producto');
    }
}

// ============= CHECKOUT =============

async function irACheckout() {
    cerrarModal('modalCarrito');
    
    const carrito = await obtenerCarrito();
    
    if (!carrito || carrito.items.length === 0) {
        alert('El carrito está vacío');
        return;
    }
    
    // Prellenar datos si el usuario está logueado
    if (usuarioActual) {
        document.querySelector('#checkoutForm input[name="nombre"]').value = usuarioActual.nombre || '';
        document.querySelector('#checkoutForm input[name="email"]').value = usuarioActual.email || '';
    }
    
    // Mostrar resumen
    const resumenItems = carrito.items.map(item => `
        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
            <span>${item.nombre} x${item.cantidad}</span>
            <span>€${parseFloat(item.subtotal).toFixed(2)}</span>
        </div>
    `).join('');
    
    document.getElementById('resumenItems').innerHTML = resumenItems;
    document.getElementById('resumenTotal').innerHTML = `Total: €${parseFloat(carrito.total).toFixed(2)}`;
    
    document.getElementById('modalCheckout').classList.add('active');
}

async function finalizarCompra(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    formData.append('accion', 'crear');
    
    try {
        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.textContent = 'Procesando...';
        
        const response = await fetch('pedidos.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            cerrarModal('modalCheckout');
            alert(`¡Pedido #${data.pedido_id} creado exitosamente!\n\nHemos enviado la factura a tu correo electrónico.`);
            form.reset();
            actualizarContadorCarrito();
            mostrarInicio();
        } else {
            alert(data.message);
        }
        
        button.disabled = false;
        button.textContent = 'Confirmar Pedido';
    } catch (error) {
        alert('Error al procesar el pedido');
        const button = form.querySelector('button[type="submit"]');
        button.disabled = false;
        button.textContent = 'Confirmar Pedido';
    }
}

// ============= NAVEGACIÓN =============

function mostrarInicio() {
    document.getElementById('heroSection').classList.remove('hidden');
    document.getElementById('destacadosSection').classList.remove('hidden');
    document.getElementById('catalogoSection').classList.add('hidden');
}

function mostrarCatalogo() {
    document.getElementById('heroSection').classList.add('hidden');
    document.getElementById('destacadosSection').classList.add('hidden');
    document.getElementById('catalogoSection').classList.remove('hidden');
    cargarProductos(1);
}

function buscarProductos() {
    cargarProductos(1);
}

function filtrarProductos() {
    cargarProductos(1);
}

// ============= MODALES =============

function mostrarLogin() {
    cerrarModal('modalRegistro');
    document.getElementById('modalLogin').classList.add('active');
}

function mostrarRegistro() {
    cerrarModal('modalLogin');
    document.getElementById('modalRegistro').classList.add('active');
}

function cerrarModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// ============= UTILIDADES =============

function mostrarMensaje(mensaje) {
    const div = document.createElement('div');
    div.textContent = mensaje;
    div.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: #27ae60;
        color: white;
        padding: 1rem 2rem;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 3000;
        animation: slideIn 0.3s;
    `;
    
    document.body.appendChild(div);
    
    setTimeout(() => {
        div.style.animation = 'slideOut 0.3s';
        setTimeout(() => div.remove(), 300);
    }, 3000);
}

// Añadir estilos de animación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);