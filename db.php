<?php
$host = "localhost"; // o IP del servidor
$db   = "productos_db"; // <-- tu base de datos
$user = "root";        // tu usuario
$pass = "";            // tu contraseña
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
?>
