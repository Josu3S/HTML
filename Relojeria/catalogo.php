<?php
session_start();
include("conexion.php"); 

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit;
}
if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'){
    header("Location: admin.php");
    exit;
}
$nombre = htmlspecialchars($_SESSION['usuario_nombre']);

$limite = 6;
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$inicio = ($pagina - 1) * $limite;

$total_resultado = $conn->query("SELECT COUNT(*) as total FROM relojes");
$total_fila = $total_resultado->fetch_assoc();
$total_productos = $total_fila['total'];
$total_paginas = ceil($total_productos / $limite);

$resultado = $conn->query("SELECT * FROM relojes LIMIT $inicio, $limite");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Catálogo de Relojes</title>
<link rel="stylesheet" href="estilos.css">
<style>
/* Estilo principal refinado */
body {
    background-color: #0e0e0e;
    color: #fff;
    margin: 0;
    font-family: 'Montserrat', sans-serif;
}

/* HEADER */
header {
    background-color: #1a1a1a;
    color: #D4AF37;
    text-align: center;
    padding: 20px 0;
    box-shadow: 0 5px 15px rgba(0,0,0,0.4);
}
header h1 {
    margin: 0;
    font-size: 2rem;
    letter-spacing: 2px;
}
header p {
    font-size: 1rem;
    color: #ccc;
}

.btn-logout {
    position: absolute;
    top: 20px;
    right: 30px;
    background-color: #D4AF37;
    color: #000;
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}
.btn-logout:hover {
    background-color: #b9972c;
    color: #fff;
}


/* CARRUSEL */
.carrusel {
    position: relative;
    width: 100%;
    height: 350px;
    overflow: hidden;
    border-bottom: 2px solid #D4AF37;
}
.carrusel img {
    width: 100%;
    height: 350px;
    object-fit: cover;
    display: none;
}
.carrusel img.active {
    display: block;
    animation: fade 1.5s ease-in-out;
}
@keyframes fade {
    from { opacity: 0.4; }
    to { opacity: 1; }
}

/* CATÁLOGO */
.catalogo {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    padding: 40px;
    justify-content: center;
}
.producto {
    background: #111;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(212,175,55,0.2);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}
.producto:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(212,175,55,0.4);
}
.producto img {
    width: 100%;
    height: 200px;
    border-radius: 10px;
    object-fit: cover;
}
.producto h3 {
    color: #D4AF37;
    margin: 15px 0 8px 0;
    font-size: 1.2rem;
}
.producto p {
    color: #ccc;
    font-size: 0.9rem;
    height: 50px;
}
.producto strong {
    display: block;
    margin: 8px 0;
    color: #fff;
    font-size: 1.1rem;
}
.btn-agregar {
    background-color: #D4AF37;
    color: #000;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
.btn-agregar:hover {
    background-color: #b9972c;
}

/* PAGINACIÓN */
.paginacion {
    text-align: center;
    margin: 30px 0;
}
.paginacion a {
    display: inline-block;
    margin: 0 5px;
    padding: 10px 14px;
    text-decoration: none;
    color: #D4AF37;
    border: 1px solid #D4AF37;
    border-radius: 8px;
    transition: 0.3s;
}
.paginacion a:hover {
    background-color: #D4AF37;
    color: #000;
}
.paginacion a.active {
    background-color: #D4AF37;
    color: #000;
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


/* Responsivo */
@media(max-width:700px){
    .carrusel { height: 250px; }
    .carrusel img { height: 250px; }
}
</style>
</head>
<body>

<header>
    <div style="position: relative;">
        <h1>Catálogo de Relojes</h1>
        <p>Bienvenido, <?php echo $nombre; ?></p>

        <!-- Botón de cerrar sesión -->
        <a href="logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
</header>


<!-- CARRUSEL DE IMÁGENES -->
<div class="carrusel">
    <img src="imagenes/carrusel1.jpg" class="active" alt="Reloj de lujo 1">
    <img src="imagenes/carrusel2.jpg" alt="Reloj de lujo 2">
    <img src="imagenes/carrusel3.jpg" alt="Reloj de lujo 3">
</div>

<script>
let indice = 0;
const imagenes = document.querySelectorAll('.carrusel img');
setInterval(() => {
    imagenes[indice].classList.remove('active');
    indice = (indice + 1) % imagenes.length;
    imagenes[indice].classList.add('active');
}, 4000);
</script>

<!-- CATÁLOGO -->
<div class="catalogo">
<?php while($fila = $resultado->fetch_assoc()): ?>
    <div class="producto">
        <img src="imagenes/<?php echo $fila['imagen']; ?>" alt="<?php echo $fila['nombre']; ?>">
        <h3><?php echo $fila['nombre']; ?></h3>
        <p><?php echo $fila['descripcion']; ?></p>
        <strong>$<?php echo number_format($fila['precio'],2); ?></strong>
        <button class="btn-agregar" onclick="agregarCarrito(<?php echo $fila['id']; ?>)">Agregar al carrito</button>
    </div>
<?php endwhile; ?>
</div>

<!-- PAGINACIÓN -->
<div class="paginacion">
<?php if($pagina > 1): ?>
    <a href="?page=<?php echo $pagina-1; ?>">« Anterior</a>
<?php endif; ?>
<?php for($i=1; $i<=$total_paginas; $i++): ?>
    <a href="?page=<?php echo $i; ?>" class="<?php if($i==$pagina) echo 'active'; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
<?php if($pagina < $total_paginas): ?>
    <a href="?page=<?php echo $pagina+1; ?>">Siguiente »</a>
<?php endif; ?>
</div>

<div class="perfil">
    <center><a href="dashboard.php" class="boton-volver">Volver al inicio</a></center>
</div>

<script>
function agregarCarrito(reloj_id){
    fetch('agregar_carrito_ajax.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'reloj_id=' + reloj_id
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>
