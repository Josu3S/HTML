<?php
session_start();
include("conexion.php");

// Verificar que sea admin
if(!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin'){
    header("Location: login.php");
    exit;
}

// ---- CERRAR SESIÓN ----
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: login.php");
    exit;
}

// ---- AGREGAR RELOJ ----
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion']=="agregar") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen = $_FILES['imagen']['name'];
    $ruta = "imagenes/" . basename($imagen);
    move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta);

    $stmt = $conn->prepare("INSERT INTO relojes (nombre,descripcion,precio,imagen) VALUES (?,?,?,?)");
    $stmt->bind_param("ssds",$nombre,$descripcion,$precio,$imagen);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Reloj agregado');</script>";
}

// ---- MODIFICAR RELOJ ----
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion']=="modificar") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    if(!empty($_FILES['imagen']['name'])){
        $imagen = $_FILES['imagen']['name'];
        $ruta = "imagenes/" . basename($imagen);
        move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta);
        $stmt = $conn->prepare("UPDATE relojes SET nombre=?, descripcion=?, precio=?, imagen=? WHERE id=?");
        $stmt->bind_param("ssdsi",$nombre,$descripcion,$precio,$imagen,$id);
    } else {
        $stmt = $conn->prepare("UPDATE relojes SET nombre=?, descripcion=?, precio=? WHERE id=?");
        $stmt->bind_param("ssdi",$nombre,$descripcion,$precio,$id);
    }
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Reloj modificado');</script>";
}

// ---- ELIMINAR RELOJ ----
if (isset($_GET['eliminar_reloj'])) {
    $id = $_GET['eliminar_reloj'];
    $stmt = $conn->prepare("DELETE FROM relojes WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Reloj eliminado');</script>";
}

// ---- ACTUALIZAR ORDEN ----
if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['accion_orden'])){
    $orden_id = $_POST['orden_id'];
    $estado = $_POST['estado'];
    $fecha_entrega = !empty($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : NULL;

    $stmt = $conn->prepare("UPDATE ordenes SET fecha_entrega=?, estado=? WHERE id=?");
    $stmt->bind_param("ssi",$fecha_entrega,$estado,$orden_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Orden actualizada');</script>";
}

// ---- OBTENER RELOJES ----
$relojes = $conn->query("SELECT * FROM relojes ORDER BY id DESC");

// ---- OBTENER ORDENES ----
$ordenes = $conn->query("SELECT o.*, u.nombre AS usuario FROM ordenes o JOIN usuarios u ON o.usuario_id=u.id ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin - Relojería</title>
<link rel="stylesheet" href="estilos.css">
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}
.barra-superior {
    background-color: #222;
    color: #fff;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.barra-superior a {
    color: #ffcc00;
    text-decoration: none;
    font-weight: bold;
}
.container {
    width: 90%;
    margin: auto;
    padding: 20px 0;
}
h1, h2 {
    text-align: center;
}

/* RELOJES */
.reloj {
    background-color: #fff;
    border-radius: 10px;
    padding: 20px;
    margin: 15px 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    display: flex;
    gap: 20px;
    align-items: center;
}
.reloj img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 5px;
}
.reloj .info {
    flex: 1;
}
.reloj form input, .reloj form textarea, .reloj form select {
    width: 100%;
    margin: 5px 0;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
.reloj form button {
    padding: 10px;
    background:#222;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-weight:bold;
}
.reloj form button:hover {
    background:#ffcc00;
    color:#222;
}
.reloj a {
    display:inline-block;
    padding:5px 10px;
    background:red;
    color:#fff;
    border-radius:5px;
    text-decoration:none;
    margin-bottom: 5px;
}
.reloj a:hover {
    background:#ff4444;
}

/* ÓRDENES */
.orden {
    background:#fff;
    margin:20px auto;
    padding:15px;
    border-radius:10px;
    width:90%;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
}
.orden img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 10px;
}
.detalle-item {
    display:flex;
    align-items:center;
    margin-bottom:10px;
}
.total {
    font-weight:bold;
    margin-top:10px;
}
form label, form select, form input {
    display:block;
    margin:5px 0;
}
form button {
    padding:10px;
    background:#222;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-weight:bold;
}
form button:hover {
    background:#ffcc00;
    color:#222;
}
</style>

</head>
<body>
<h1>Panel de Administración 🛠 <a href="?logout=1" class="logout">Cerrar Sesión</a></h1>
<div class="container">

<!-- SECCIÓN AGREGAR RELOJ -->
<h2>Agregar Nuevo Reloj</h2>
<form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="agregar">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <textarea name="descripcion" placeholder="Descripción"></textarea>
    <input type="number" name="precio" step="0.01" placeholder="Precio" required>
    <input type="file" name="imagen" required>
    <button type="submit">Agregar</button>
</form>

<!-- SECCIÓN LISTA DE RELOJES -->
<h2>Lista de Relojes</h2>
<?php while($r = $relojes->fetch_assoc()): ?>
<div class="reloj">
    <img src="imagenes/<?php echo $r['imagen']; ?>" alt="<?php echo $r['nombre']; ?>">
    <div class="info">
        <p><b>Nombre:</b> <?php echo $r['nombre']; ?></p>
        <p><b>Descripción:</b> <?php echo $r['descripcion']; ?></p>
        <p><b>Precio:</b> $<?php echo number_format($r['precio'],2); ?></p>
        <a href="?eliminar_reloj=<?php echo $r['id']; ?>" onclick="return confirm('¿Eliminar este reloj?')">Eliminar</a>

        <!-- FORMULARIO MODIFICAR RELOJ -->
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="modificar">
            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <input type="text" name="nombre" value="<?php echo $r['nombre']; ?>" required>
            <textarea name="descripcion"><?php echo $r['descripcion']; ?></textarea>
            <input type="number" name="precio" step="0.01" value="<?php echo $r['precio']; ?>" required>
            <input type="file" name="imagen">
            <button type="submit">Modificar</button>
        </form>
    </div>
</div>
<?php endwhile; ?>


<!-- SECCIÓN ÓRDENES -->
<h2>Órdenes de Usuarios</h2>
<?php while($o = $ordenes->fetch_assoc()): ?>
<div class="orden">
    <p><b>Orden #<?php echo $o['id']; ?> | Usuario:</b> <?php echo $o['usuario']; ?> | <b>Total:</b> $<?php echo number_format($o['total'],2); ?> | <b>Fecha:</b> <?php echo $o['fecha']; ?></p>

    <?php
    // Detalle de cada orden
    $detalle = $conn->query("SELECT d.*, r.nombre, r.imagen FROM detalle_orden d JOIN relojes r ON d.reloj_id=r.id WHERE d.orden_id=".$o['id']);
    while($item = $detalle->fetch_assoc()):
    ?>
    <div style="display:flex;align-items:center;margin:5px 0;">
        <img src="imagenes/<?php echo $item['imagen']; ?>" alt="<?php echo $item['nombre']; ?>" style="width:50px;height:50px;margin-right:10px;">
        <p><?php echo $item['nombre']; ?> | Cantidad: <?php echo $item['cantidad']; ?> | Precio unitario: $<?php echo number_format($item['precio_unitario'],2); ?></p>
    </div>
    <?php endwhile; ?>

    <!-- FORMULARIO ACTUALIZAR ORDEN -->
    <form action="" method="POST">
        <input type="hidden" name="orden_id" value="<?php echo $o['id']; ?>">
        <label>Estado:</label>
        <select name="estado">
            <option value="Pendiente" <?php if($o['estado']=="Pendiente") echo "selected"; ?>>Pendiente</option>
            <option value="Entregado" <?php if($o['estado']=="Entregado") echo "selected"; ?>>Entregado</option>
            <option value="Cancelado" <?php if($o['estado']=="Cancelado") echo "selected"; ?>>Cancelado</option>
        </select>
        <label>Fecha de entrega:</label>
        <input type="date" name="fecha_entrega" value="<?php echo $o['fecha_entrega']; ?>">
        <button type="submit" name="accion_orden">Actualizar Orden</button>
    </form>
</div>
<?php endwhile; ?>

</div>
</body>
</html>
