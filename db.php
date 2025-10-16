<?php
// ==========================================================
// ** ATENCIÓN: Reemplaza todos estos valores con los de Railway **
// ** Consíguelos en la pestaña "Data" de tu servicio MySQL en Railway **
// ==========================================================
$host = "mysql.railway.internal";      // Ejemplo: monorail.proxy.rlwy.net
$username = "root";               // Según tu imagen de credenciales
$password = "qUrCdOyFQeBVckhqmYbBgXDsgqcPUsql"; // La contraseña que copiaste
$dbname = "railway";  // Ejemplo: railway
$port = 3306;                     // El puerto que te asignó Railway (debe ser un número)

// ----------------------------------------------------------

// Establecer la conexión usando MySQLi (Orientado a objetos)
// NOTA: Con Railway, el puerto es necesario.
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    // Si falla la conexión, la aplicación mostrará el error.
    die("Conexión fallida: " . $conn->connect_error);
}

// Opcional: Establecer el juego de caracteres a utf8mb4 para el soporte completo de emojis y caracteres especiales
if (!$conn->set_charset("utf8mb4")) {
    // Si falla al establecer el charset, puedes registrar el error.
    // printf("Error cargando el conjunto de caracteres utf8mb4: %s\n", $conn->error);
}

// La variable $conn es ahora tu objeto de conexión a la base de datos
?>
