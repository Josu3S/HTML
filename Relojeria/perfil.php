<?php
session_start();
include("conexion.php"); // Asegúrate de tener $conn definido aquí

// Verificar si el usuario está logueado
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit;
}

// Bloquear acceso a admins
if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'){
    header("Location: admin.php");
    exit;
}

// Obtener datos del usuario
$id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT nombre, email FROM usuarios WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre, $email);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Perfil - Relojería Elegance</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #1b1b1b, #3a3a3a);
        color: #fff;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #111;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        position: relative;
    }

    header h1 {
        color: #ffcc00;
        font-size: 24px;
        letter-spacing: 1px;
    }

    .user-info {
        position: absolute;
        right: 30px;
        top: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-info span {
        font-weight: 500;
        color: #f0f0f0;
    }

    .logout-btn {
        background-color: #ffcc00;
        color: #111;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .logout-btn:hover {
        background-color: #e6b800;
        transform: scale(1.05);
    }

    .perfil {
        max-width: 500px;
        margin: 100px auto;
        background-color: #222;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        text-align: center;
    }

    .perfil h2 {
        color: #ffcc00;
        margin-bottom: 25px;
        font-size: 28px;
    }

    .perfil p {
        font-size: 18px;
        margin: 15px 0;
        color: #ddd;
    }

    .perfil p b {
        color: #ffcc00;
    }

    .boton-volver {
        display: inline-block;
        margin-top: 25px;
        padding: 12px 25px;
        background-color: #ffcc00;
        color: #111;
        border-radius: 25px;
        font-weight: bold;
        text-decoration: none;
        transition: 0.3s;
    }

    .boton-volver:hover {
        background-color: #e6b800;
        transform: translateY(-3px);
    }

    footer {
        text-align: center;
        color: #bbb;
        padding: 20px;
        font-size: 14px;
        margin-top: 50px;
    }
</style>
</head>
<body>

<header>
    <h1>Relojería Elegance ⌚</h1>
    <div class="user-info">
        <span><?php echo htmlspecialchars($nombre); ?></span>
        <a href="logout.php" class="logout-btn">Cerrar sesión</a>
    </div>
</header>

<div class="perfil">
    <h2>Mi Perfil</h2>
    <p><b>Nombre:</b> <?php echo htmlspecialchars($nombre); ?></p>
    <p><b>Email:</b> <?php echo htmlspecialchars($email); ?></p>
    <a href="dashboard.php" class="boton-volver">Volver al inicio</a>
</div>

<footer>
    © <?php echo date("Y"); ?> Relojería Elegance. Todos los derechos reservados.
</footer>

</body>
</html>
