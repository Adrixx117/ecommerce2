<?php
// ==========================================================
// ** Conexión a la base de datos de Wasmer (según la imagen) **
// ==========================================================
$host = "db.fr-pari1.bengt.wasmernet.com";
$username = "12a7273370c98000bd657fc9f194";
$password = "068f12a7-2733-724f-8000-864e8c4e7a92"; // **Pega aquí la contraseña que ves en Wasmer**
$dbname = "productos_db"; //nombre db
$port = 10272;

// Establecer la conexión usando MySQLi (Orientado a objetos)
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Opcional: Establecer el juego de caracteres
$conn->set_charset("utf8mb4");

// ¡Conexión lista! La variable $conn está disponible para el resto de tus scripts PHP.
?>