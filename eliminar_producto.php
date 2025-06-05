<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: https://apptest.fullsoluciones.com.ar/index.php?error=Por favor, inicie sesión.");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username_db = "phpmyadmin";
$password_db = "Galito*789";
$dbname = "sweet";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el ID del producto a eliminar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: productos.php?mensaje=ID de producto no válido");
    exit();
}

// Eliminar el producto
$stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: productos.php?mensaje=Producto eliminado correctamente");
} else {
    header("Location: productos.php?mensaje=Error al eliminar el producto");
}
$stmt->close();
$conn->close();
exit();
?>
