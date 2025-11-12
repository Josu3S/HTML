<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit;
}

if(isset($_SESSION['rol']) && $_SESSION['rol']==='admin'){
    header("Location: admin.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// ---- Eliminar un producto ----
if(isset($_POST['eliminar'])){
    $carrito_id = intval($_POST['carrito_id']);
    $stmt = $conn->prepare("DELETE FROM carrito WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii",$carrito_id,$usuario_id);
    $stmt->execute();
    $stmt->close();
}

// ---- Actualizar cantidades ----
if(isset($_POST['actualizar'])){
    foreach($_POST['cantidades'] as $carrito_id => $cantidad){
        $cantidad = max(1,intval($cantidad));
        $stmt = $conn->prepare("UPDATE carrito SET cantidad=? WHERE id=? AND usuario_id=?");
        $stmt->bind_param("iii",$cantidad,$carrito_id,$usuario_id);
        $stmt->execute();
        $stmt->close();
    }
}

// ---- Realizar pedido ----
if(isset($_POST['ordenar'])){
    $items = $conn->query("SELECT r.id as reloj_id, r.precio, c.cantidad 
                           FROM carrito c JOIN relojes r ON c.reloj_id=r.id 
                           WHERE c.usuario_id=$usuario_id");
    $total = 0;
    $detalles = [];
    while($fila = $items->fetch_assoc()){
        $total += $fila['precio']*$fila['cantidad'];
        $detalles[] = $fila;
    }
    if($total>0){
        $stmt = $conn->prepare("INSERT INTO ordenes (usuario_id,total) VALUES (?,?)");
        $stmt->bind_param("id",$usuario_id,$total);
        $stmt->execute();
        $orden_id = $stmt->insert_id;
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO detalle_orden (orden_id,reloj_id,cantidad,precio_unitario) VALUES (?,?,?,?)");
        foreach($detalles as $item){
            $stmt->bind_param("iiid",$orden_id,$item['reloj_id'],$item['cantidad'],$item['precio']);
            $stmt->execute();
        }
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM carrito WHERE usuario_id=?");
        $stmt->bind_param("i",$usuario_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Pedido realizado. Total: $".number_format($total,2)."'); window.location='ordenes.php';</script>";
        exit;
    }else{
        echo "<script>alert('Carrito vacío');</script>";
    }
}

$items = $conn->query("SELECT c.id as carrito_id, r.*, c.cantidad 
                       FROM carrito c JOIN relojes r ON c.reloj_id=r.id 
                       WHERE c.usuario_id=$usuario_id");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Carrito - Relojería Elegance</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1b1b1b, #2e2e2e);
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
    box-shadow: 0 4px 10px rgba(0,0,0,0.6);
    position: sticky;
    top: 0;
    z-index: 100;
}
header h1 {
    color: #ffcc00;
    font-size: 24px;
    letter-spacing: 1px;
}
.logout-btn {
    background-color: #ffcc00;
    color: #111;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}
.logout-btn:hover {
    background-color: #e6b800;
    transform: scale(1.05);
}
h2, h1 {
    text-align: center;
    margin-top: 25px;
    color: #ffcc00;
}
.carrito-lista {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
    padding: 40px;
}
.carrito-item {
    background-color: #222;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.4);
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.3s;
}
.carrito-item:hover {
    transform: translateY(-5px);
}
.carrito-item img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 10px;
}
.carrito-item h3 {
    color: #ffcc00;
    margin: 10px 0 5px;
}
.carrito-item p {
    font-size: 0.9em;
    color: #ccc;
    flex-grow: 1;
}
.carrito-item strong {
    margin: 8px 0;
    color: #fff;
}
.carrito-item input {
    width: 60px;
    padding: 5px;
    text-align: center;
    border-radius: 5px;
    border: none;
    margin-bottom: 10px;
}
.carrito-item button {
    padding: 10px;
    width: 100%;
    background-color: #ffcc00;
    color: #111;
    border: none;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    margin-bottom: 8px;
}
.carrito-item button:hover {
    background-color: #e6b800;
    transform: translateY(-2px);
}
.btn, .ordenar-btn {
    display: block;
    width: 220px;
    margin: 30px auto;
    padding: 12px;
    text-align: center;
    border-radius: 25px;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}
.btn {
    background-color: #ffcc00;
    color: #111;
}
.btn:hover {
    background-color: #e6b800;
}
.ordenar-btn {
    background-color: #28a745;
    color: #fff;
    border: none;
}
.ordenar-btn:hover {
    background-color: #218838;
    transform: scale(1.05);
}
footer {
    text-align: center;
    padding: 20px;
    color: #bbb;
    font-size: 14px;
    margin-top: 50px;
}
</style>
</head>
<body>

<header>
    <h1>Relojería Elegance 🛒</h1>
    <a href="logout.php" class="logout-btn">Cerrar sesión</a>
</header>

<h1>Mi Carrito</h1>

<div class="carrito-lista">
<?php 
$total=0;
while($fila=$items->fetch_assoc()):
    $subtotal = $fila['precio']*$fila['cantidad'];
    $total += $subtotal;
?>
<div class="carrito-item">
    <img src="imagenes/<?php echo $fila['imagen']; ?>" alt="<?php echo htmlspecialchars($fila['nombre']); ?>">
    <h3><?php echo htmlspecialchars($fila['nombre']); ?></h3>
    <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
    <strong>$<?php echo number_format($fila['precio'],2); ?> c/u</strong>

    <form method="POST">
        <input type="number" name="cantidades[<?php echo $fila['carrito_id']; ?>]" value="<?php echo $fila['cantidad']; ?>" min="1">
        <button type="submit" name="actualizar" value="1">Actualizar Cantidad</button>
    </form>

    <form method="POST">
        <input type="hidden" name="carrito_id" value="<?php echo $fila['carrito_id']; ?>">
        <button type="submit" name="eliminar" value="1" style="background-color:#dc3545;color:#fff;">Eliminar</button>
    </form>

    <strong>Subtotal: $<?php echo number_format($subtotal,2); ?></strong>
</div>
<?php endwhile; ?>
</div>

<h2>Total: $<?php echo number_format($total,2); ?></h2>

<form method="POST">
    <button type="submit" name="ordenar" class="ordenar-btn">Realizar Pedido</button>
</form>

<a href="dashboard.php" class="btn">Volver al inicio</a>

</body>
</html>
