<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $contrasena = trim($_POST['contrasena']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('Correo inválido.'); window.history.back();</script>");
    }

    $stmt = $conn->prepare("SELECT id, nombre, contrasena, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nombre, $hash, $rol);
        $stmt->fetch();

        if (password_verify($contrasena, $hash)) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['rol'] = $rol; 

            if($rol === 'admin'){
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            echo "<script>alert('Acceso Denegado.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Acceso Denegado.'); window.history.back();</script>";
    }
    $stmt->close();
    $conn->close();
}
?>
