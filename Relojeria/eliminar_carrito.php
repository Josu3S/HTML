<?php
session_start();
include("conexion.php");
if(!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['carrito_id'])){
    $id = $_POST['carrito_id'];
    $conn->query("DELETE FROM carrito WHERE id=$id");
}
header("Location: carrito.php");
exit;
?>
