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

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto = trim($_POST['producto']);
    if ($producto === '') {
        $error = "El nombre del producto no puede estar vacío.";
    } else {
        $stmt = $conn->prepare("INSERT INTO productos (producto) VALUES (?)");
        $stmt->bind_param("s", $producto);
        if ($stmt->execute()) {
            header("Location: productos.php?mensaje=Producto agregado correctamente");
            exit();
        } else {
            $error = "Error al agregar el producto.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Nuevo Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Agregar Nuevo Producto</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="producto" class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="producto" name="producto" required>
        </div>
        <button type="submit" class="btn btn-success">Agregar</button>
        <a href="productos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
