<?php
session_start(); // Debe ser la primera línea del archivo

require 'db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Verificar si el email ya está registrado
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('El email ya está registrado.'); window.history.back();</script>";
        exit;
    }
    $stmt->close();

    // Hashear la contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario en la base de datos
    $stmt = $conn->prepare("INSERT INTO users (nombre, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $hash);

    if ($stmt->execute()) {
        // Guardar nombre en sesión
        $_SESSION['user'] = $nombre;

        // Redirigir con popup
        echo "<script>
            alert('Registro exitoso. ¡Bienvenido, ".htmlspecialchars($nombre)."!');
            window.location.href='index.php';
        </script>";
    } else {
        echo "<script>alert('Error al registrar usuario.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
