<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $contrasena = trim($_POST['contrasena']);

    // Validación de nombre
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/", $nombre)) {
        die("<script>alert('El nombre solo debe contener letras y al menos 3 caracteres.'); window.history.back();</script>");
    }

    // Validación de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('Correo electrónico inválido.'); window.history.back();</script>");
    }

    // Validación de contraseña segura
    if (strlen($contrasena) < 8 || 
        !preg_match("/[A-Z]/", $contrasena) ||
        !preg_match("/[a-z]/", $contrasena) ||
        !preg_match("/[0-9]/", $contrasena)) {
        die("<script>alert('La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números.'); window.history.back();</script>");
    }

    // Verificar si el correo ya existe
    $verificar = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verificar->bind_param("s", $email);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        die("<script>alert('El correo ya está registrado. Usa otro.'); window.history.back();</script>");
    }
    $verificar->close();

    // Encriptar la contraseña
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar el usuario
    $sql = "INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $email, $hash);

    if ($stmt->execute()) {
        echo "<script>alert('Registro exitoso. ¡Bienvenido a Relojería Tiempo Exacto!'); window.location='index.html';</script>";
    } else {
        echo "<script>alert('Error al registrar. Intenta de nuevo.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
