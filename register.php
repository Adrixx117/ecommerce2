ç<?php
session_start();
require 'db.php'; // Archivo con conexión a HeidiSQL / MySQL

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Verificar si email ya existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        echo "<script>alert('El email ya está registrado.'); window.history.back();</script>";
        exit;
    }
    $stmt->close();

    // Hashear contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = $conn->prepare("INSERT INTO users (nombre,email,password) VALUES (?,?,?)");
    $stmt->bind_param("sss", $nombre, $email, $hash);

    if($stmt->execute()){
        // Guardar nombre en sesión para mostrar mensaje
        $_SESSION['user'] = $nombre;
        // Redirigir con popup
        echo "<script>
            alert('Registro exitoso. ¡Bienvenido, $nombre!');
            window.location.href='index.html';
        </script>";
    } else {
        echo "<script>alert('Error al registrar usuario.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
