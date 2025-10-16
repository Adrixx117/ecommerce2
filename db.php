<?php
$servername = "sqlXXX.epizy.com"; // Cambia esto por tu host
$username = "epiz_XXXXXX";        // Tu usuario de DB
$password = "tu_contraseña";      // Tu contraseña
$dbname = "epiz_XXXXXX_productos_db"; // Tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
