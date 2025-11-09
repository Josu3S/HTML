<?php
session_start();
include("conexion.php");
if(!isset($_SESSION['usuario_id'])) {
    echo "Debes iniciar sesión";
    exit;
}

if(isset($_POST['reloj_id'])){
    $usuario_id = $_SESSION['usuario_id'];
    $reloj_id = $_POST['reloj_id'];

    // Verifica si ya está en el carrito
    $stmt = $conn->prepare("SELECT id FROM carrito WHERE usuario_id=? AND reloj_id=?");
    $stmt->bind_param("ii",$usuario_id,$reloj_id);
    $stmt->execute();
    $stmt->store_result();
    
    if($stmt->num_rows > 0){
        echo "El reloj ya está en el carrito";
    } else {
        $stmt2 = $conn->prepare("INSERT INTO carrito (usuario_id,reloj_id) VALUES (?,?)");
        $stmt2->bind_param("ii",$usuario_id,$reloj_id);
        $stmt2->execute();
        echo "Reloj agregado al carrito ✅";
        $stmt2->close();
    }
    $stmt->close();
}
?>
