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

// Obtener el producto a editar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "ID de producto no válido.";
    exit();
}

// Actualizar producto si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto = trim($_POST['producto']);
    if ($producto === '') {
        $error = "El nombre del producto no puede estar vacío.";
    } else {
        $stmt = $conn->prepare("UPDATE productos SET producto = ? WHERE id = ?");
        $stmt->bind_param("si", $producto, $id);
        if ($stmt->execute()) {
            header("Location: productos.php?mensaje=Producto actualizado");
            exit();
        } else {
            $error = "Error al actualizar el producto.";
        }
        $stmt->close();
    }
}

// Obtener datos actuales del producto
$stmt = $conn->prepare("SELECT producto FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($producto_actual);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Editar Producto</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="producto" class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="producto" name="producto" value="<?php echo htmlspecialchars($producto_actual); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="productos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
