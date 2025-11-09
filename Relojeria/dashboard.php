<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Redirigir admin al panel de administración
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    header("Location: admin.php");
    exit;
}

// Guardar nombre del usuario de manera segura
$nombre = htmlspecialchars($_SESSION['usuario_nombre']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Relojería</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #1b1b1b, #3a3a3a);
            color: #fff;
        }

        header {
            background-color: #111;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            position: relative;
        }

        header h1 {
            font-size: 24px;
            letter-spacing: 1px;
            color: #ffcc00;
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

        main {
            padding: 60px 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            max-width: 1000px;
            margin: auto;
        }

        .card {
            background-color: #222;
            border-radius: 15px;
            text-align: center;
            padding: 40px 25px;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            background-color: #2e2e2e;
        }

        footer {
            text-align: center;
            color: #bbb;
            padding: 20px;
            font-size: 14px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Relojería Elegance ⌚</h1>
        <div class="user-info">
            <span>Hola, <?php echo $nombre; ?></span>
            <a href="logout.php" class="logout-btn">Cerrar sesión</a>
        </div>
    </header>

    <main>
        <a href="catalogo.php" class="card">🕰 Catálogo</a>
        <a href="perfil.php" class="card">👤 Mi perfil</a>
        <a href="carrito.php" class="card">🛒 Mi carrito</a>
        <a href="ordenes.php" class="card">📜 Historial</a>
    </main>

    <footer>
        © <?php echo date("Y"); ?> Relojería Elegance. Todos los derechos reservados.
    </footer>
</body>
</html>
