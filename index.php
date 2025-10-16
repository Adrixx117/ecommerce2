<?php
session_start();
$usuario = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi E-commerce - Tienda Online</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Header -->
<header>
  <div class="header-content">
    <div class="logo">🛒 Mi E-commerce</div>
    <nav class="nav-menu">
      <a href="#" onclick="mostrarInicio()">Inicio</a>
      <a href="#" onclick="mostrarCatalogo()">Productos</a>
    </nav>
    <div class="user-section">
      <div class="cart-icon" onclick="mostrarCarrito()">
        🛒 <span class="cart-count" id="cartCount">0</span>
      </div>
      <div class="user-buttons">
        <!-- Botón toggle modo oscuro dentro del header -->
        <label class="switch">
          <input type="checkbox" id="themeSwitch" />
          <span class="slider">
            <div class="star star_1"></div>
            <div class="star star_2"></div>
            <div class="star star_3"></div>
            <svg viewBox="0 0 16 16" class="cloud_1 cloud">
              <path
                transform="matrix(.77976 0 0 .78395-299.99-418.63)"
                fill="#fff"
                d="m391.84 540.91c-.421-.329-.949-.524-1.523-.524-1.351 0-2.451 1.084-2.485 2.435-1.395.526-2.388 1.88-2.388 3.466 0 1.874 1.385 3.423 3.182 3.667v.034h12.73v-.006c1.775-.104 3.182-1.584 3.182-3.395 0-1.747-1.309-3.186-2.994-3.379.007-.106.011-.214.011-.322 0-2.707-2.271-4.901-5.072-4.901-2.073 0-3.856 1.202-4.643 2.925"
              ></path>
            </svg>
          </span>
        </label>

        <?php if($usuario): ?>
          <span class="welcome">¡Bienvenido, <?php echo htmlspecialchars($usuario); ?>!</span>
          <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit" class="btn btn-secondary">Cerrar sesión</button>
          </form>
        <?php else: ?>
          <button class="btn btn-primary" onclick="mostrarLogin()">Login</button>
          <button class="btn btn-secondary" onclick="mostrarRegistro()">Register</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<!-- Hero -->
<section class="hero" id="heroSection">
  <h1>Bienvenido a Nuestra Tienda</h1>
  <p>Encuentra los mejores productos al mejor precio</p>
</section>

<!-- Contenedor principal -->
<div class="container">
  <!-- Productos Destacados -->
  <section id="destacadosSection">
    <h2>Productos Destacados</h2>
    <div id="destacadosGrid" class="products-grid"></div>
  </section>

  <!-- Catálogo -->
  <section id="catalogoSection" class="hidden">
    <div class="search-filters">
      <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Buscar productos...">
        <button class="btn btn-primary" onclick="buscarProductos()">Buscar</button>
      </div>
      <div class="filters">
        <select id="categoriaFilter" onchange="filtrarProductos()">
          <option value="">Todas las categorías</option>
        </select>
        <select id="ordenFilter" onchange="filtrarProductos()">
          <option value="id">Más recientes</option>
          <option value="precio_asc">Precio: Menor a Mayor</option>
          <option value="precio_desc">Precio: Mayor a Menor</option>
          <option value="nombre">Nombre A-Z</option>
        </select>
      </div>
    </div>
    <div id="productosGrid" class="products-grid"></div>
    <div id="pagination" class="pagination"></div>
  </section>
</div>

<!-- Footer -->
<footer>
  <div class="container">
    <p>© 2025 Mi E-commerce. Todos los derechos reservados.</p>
  </div>
</footer>

<!-- Modal Login -->
<div id="modalLogin" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Iniciar Sesión</h2>
      <span class="close-modal" onclick="cerrarModal('modalLogin')">&times;</span>
    </div>
    <form action="login.php" method="POST">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary">Entrar</button>
      <p class="switch-link">¿No tienes cuenta? <a href="#" onclick="cerrarModal('modalLogin'); mostrarRegistro();">Regístrate</a></p>
    </form>
  </div>
</div>

<!-- Modal Registro -->
<div id="modalRegistro" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Registrarse</h2>
      <span class="close-modal" onclick="cerrarModal('modalRegistro')">&times;</span>
    </div>
    <form action="register.php" method="POST">
      <div class="form-group">
        <label>Nombre Completo</label>
        <input type="text" name="nombre" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" required minlength="6">
      </div>
      <button type="submit" class="btn btn-primary">Registrarse</button>
      <p class="switch-link">¿Ya tienes cuenta? <a href="#" onclick="cerrarModal('modalRegistro'); mostrarLogin();">Inicia sesión</a></p>
    </form>
  </div>
</div>

<script src="app.js"></script>
<script>
  // ==============================
  // Toggle modo oscuro desde el switch del header
  // ==============================
  const themeSwitch = document.getElementById('themeSwitch');
  const body = document.body;

  // Estado guardado
  if(localStorage.getItem('theme') === 'dark'){
    body.classList.add('dark');
    themeSwitch.checked = true;
  }

  themeSwitch.addEventListener('change', ()=>{
    body.classList.toggle('dark');
    if(body.classList.contains('dark')){
      localStorage.setItem('theme','dark');
    } else {
      localStorage.setItem('theme','light');
    }
  });

  // Funciones para abrir/cerrar modales
  function mostrarLogin(){ document.getElementById('modalLogin').classList.add('active'); }
  function mostrarRegistro(){ document.getElementById('modalRegistro').classList.add('active'); }
  function cerrarModal(id){ document.getElementById(id).classList.remove('active'); }
</script>
</body>
</html>
