<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Relojería</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
        <h1>Iniciar Sesión</h1>
        <form action="auth.php" method="POST" onsubmit="return validarLogin()">
            <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
            <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        <p>¿No tienes cuenta? <a href="registro.html">Regístrate aquí</a></p>
    </div>

    <script>
    function validarLogin() {
        const email = document.getElementById('email').value.trim();
        const contrasena = document.getElementById('contrasena').value.trim();
        if (email === "" || contrasena === "") {
            alert("Todos los campos son obligatorios.");
            return false;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("Ingresa un correo válido.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
