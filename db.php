<?php
// ==========================================================
// ** Conexión a la base de datos de Wasmer (¡Verifica estos valores!) **
// ==========================================================
$host = "db.fr-pari1.bengt.wasmernet.com";
$username = "12a7273370c98000bd657fc9f194";
// ¡Pega tu contraseña real aquí!
$password = "068f12a7-2733-724f-8000-864e8c4e7a92"; 
$dbname = "productos_db";
$port = 10272;

// Establecer la conexión usando MySQLi
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Verificar si hay un error de conexión
if ($conn->connect_error) {
    // Si la conexión falla, se mostrará el mensaje de error de MySQLi.
    // **NOTA:** En producción, es mejor solo mostrar un error genérico.
    die("Error de Conexión: " . $conn->connect_error);
}

// Opcional: Establecer el juego de caracteres
$conn->set_charset("utf8mb4");

// Si llegas hasta aquí, la conexión es exitosa.
?>