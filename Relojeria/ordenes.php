<?php
session_start();
include("conexion.php");

// Verificar sesión
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit;
}

// Bloquear acceso a admins
if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'){
    header("Location: admin.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nombre = htmlspecialchars($_SESSION['usuario_nombre']);

// Obtener las órdenes del usuario
$ordenes = $conn->query("SELECT * FROM ordenes WHERE usuario_id=$usuario_id ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Órdenes</title>
<link rel="stylesheet" href="estilos.css">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f0f0f0, #d9d9d9);
    margin: 0;
    padding: 0;
}

header {
    position: relative;
    background-color: #111;
    color: #fff;
    padding: 15px 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 18px;
}

header span {
    font-weight: 500;
}

.cerrar-sesion {
    position: absolute;
    right: 20px;
    top: 15px;
    background-color: #ffcc00;
    color: #111;
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}
.cerrar-sesion:hover {
    background-color: #fff;
    color: #111;
}

h1 {
    text-align: center;
    margin: 40px 0 20px 0;
    color: #111;
    font-weight: 600;
}

.orden {
    background: #fff;
    margin: 25px auto;
    padding: 25px;
    border-radius: 15px;
    width: 85%;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.orden:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.orden h2 {
    margin: 0 0 10px 0;
    font-size: 20px;
    color: #111;
}

.detalle {
    margin-left: 10px;
    margin-top: 10px;
}

.detalle-item {
    display: flex;
    align-items: center;
    background-color: #fafafa;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 10px;
    transition: background 0.3s;
}
.detalle-item:hover {
    background-color: #f0f0f0;
}

.detalle-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    margin-right: 15px;
}

.detalle-item p {
    margin: 0 10px 0 0;
    font-size: 16px;
}

.total {
    font-weight: bold;
    margin-top: 10px;
    font-size: 18px;
    color: #222;
}

.estado {
    font-weight: bold;
    color: #444;
    margin-top: 5px;
}

.fecha-entrega {
    font-style: italic;
    color: #555;
    margin-top: 5px;
}

.btn {
    display: block;
    width: 220px;
    margin: 40px auto;
    text-align: center;
    padding: 12px;
    background-color: #111;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s;
}
.btn:hover {
    background-color: #ffcc00;
    color: #111;
}

footer {
    text-align: center;
    padding: 15px;
    color: #eee;
    background-color: #111;
    font-size: 14px;
    margin-top: 40px;
}
</style>
</head>
<body>

<header>
    <span>Historial de Órdenes de <?php echo $nombre; ?> 📜</span>
    <a href="logout.php" class="cerrar-sesion">Cerrar sesión</a>
</header>

<main>
<?php if($ordenes->num_rows > 0): ?>
    <?php while($orden = $ordenes->fetch_assoc()): ?>
        <div class="orden">
            <h2>Orden #<?php echo $orden['id']; ?> - Fecha: <?php echo $orden['fecha']; ?></h2>
            <p class="estado">Estado: <?php echo $orden['estado']; ?></p>
            <?php if(!empty($orden['fecha_entrega'])): ?>
                <p class="fecha-entrega">Fecha de entrega: <?php echo $orden['fecha_entrega']; ?></p>
            <?php endif; ?>

            <?php
            $detalle = $conn->query("SELECT d.*, r.nombre, r.imagen, r.descripcion 
                                     FROM detalle_orden d 
                                     JOIN relojes r ON d.reloj_id=r.id 
                                     WHERE d.orden_id=".$orden['id']);
            ?>
            <div class="detalle">
                <?php while($item = $detalle->fetch_assoc()): ?>
                    <div class="detalle-item">
                        <img src="imagenes/<?php echo $item['imagen']; ?>" alt="<?php echo $item['nombre']; ?>">
                        <div>
                            <p><b><?php echo $item['nombre']; ?></b></p>
                            <p>Cantidad: <?php echo $item['cantidad']; ?></p>
                            <p>Precio unitario: $<?php echo number_format($item['precio_unitario'],2); ?></p>
                            <p>Subtotal: $<?php echo number_format($item['cantidad']*$item['precio_unitario'],2); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="total">Total Orden: $<?php echo number_format($orden['total'],2); ?></div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center; font-size:18px; color:#555;">No tienes órdenes aún.</p>
<?php endif; ?>

<a href="dashboard.php" class="btn">Volver al inicio</a>
</main>

</body>
</html>
