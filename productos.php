<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    // Redirigir al login si no hay sesión activa
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

// INICIO: Paginación y listado de productos
$registros_por_pagina = 10;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $registros_por_pagina;

// Contar total de productos
$total_result = $conn->query("SELECT COUNT(*) as total FROM productos");
$total_filas = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$total_paginas = ceil($total_filas / $registros_por_pagina);

// Obtener productos paginados
$sql = "SELECT id, producto FROM productos ORDER BY id DESC LIMIT $registros_por_pagina OFFSET $offset";
$result = $conn->query($sql);
// FIN: Paginación y listado de productos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Agregar Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Sweety Snack y el Gran Cheff en Villa Constitución</span>
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </nav>
    </header>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Listado de Productos</h1>
        <!-- INICIO: Fila de búsqueda y agregar nuevo -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="get" class="d-flex">
                    <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar producto..." value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="nuevo_producto.php" class="btn btn-success">Agregar Nuevo</a>
            </div>
        </div>
        <!-- FIN: Fila de búsqueda y agregar nuevo -->

        <!-- INICIO: Tabla de productos paginada -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <!-- INICIO: Encabezados de acciones -->
                    <th class="text-center">Acciones</th>
                    <!-- FIN: Encabezados de acciones -->
                </tr>
            </thead>
            <tbody>
                <?php
                // INICIO: Filtrado por búsqueda
                $where = "";
                if (!empty($_GET['buscar'])) {
                    $buscar = $conn->real_escape_string($_GET['buscar']);
                    $where = "WHERE producto LIKE '%$buscar%'";
                }
                // Actualizar conteo y consulta si hay búsqueda
                if ($where) {
                    $total_result = $conn->query("SELECT COUNT(*) as total FROM productos $where");
                    $total_filas = $total_result ? $total_result->fetch_assoc()['total'] : 0;
                    $total_paginas = ceil($total_filas / $registros_por_pagina);
                    $sql = "SELECT id, producto FROM productos $where ORDER BY id DESC LIMIT $registros_por_pagina OFFSET $offset";
                    $result = $conn->query($sql);
                }
                // FIN: Filtrado por búsqueda

                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['producto']); ?></td>
                            <!-- INICIO: Iconos de editar y eliminar -->
                            <td class="text-center">
                                <a href="editar_producto.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                    <span class="bi bi-pencil-square"></span> <!-- Bootstrap icon -->
                                </a>
                                <a href="eliminar_producto.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro que desea eliminar este producto?');">
                                    <span class="bi bi-trash"></span> <!-- Bootstrap icon -->
                                </a>
                            </td>
                            <!-- FIN: Iconos de editar y eliminar -->
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center">No hay productos.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- FIN: Tabla de productos paginada -->

        <!-- INICIO: Navegación de paginación -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php if ($i == $pagina) echo 'active'; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <!-- FIN: Navegación de paginación -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>