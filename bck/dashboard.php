<!-- filepath: c:\xampp\htdocs\sweet\dashboard.php -->
<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    // Redirigir al login si no hay sesión activa
    header("Location: https://apptest.fullsoluciones.com.ar/index.php?error=Por favor, inicie sesión.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Encabezado -->
    <header>
        <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Sweety Snack y el Gran Cheff en Villa Constitución</span>
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <div class="container mt-5">
        <!-- Contenido principal -->
        <div class="mt-4">
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>Esta página te permitirá hacer tus presupuestos y remitos.</p>
            <ul class="list-group mt-3">
                <li class="list-group-item">
                    <i class="bi bi-file-earmark-text"></i> 
                    <a href="remitos.php" class="text-decoration-none">Ir a Remitos</a>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-list-check"></i> 
                    <a href="list_remitos.php" class="text-decoration-none">Listado de Remitos</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Espacios en blanco antes del footer -->
    <div style="height: 10rem;"></div> <!-- 5rem + 4rem adicionales -->

    <!-- Footer -->
    <footer class="text-center text-muted py-3 mt-5" style="background-color: #e3f2fd;">
        <p class="mb-0">
            Desarrollado por <strong>Full Soluciones</strong>: Soluciones informáticas inteligentes para empresas.
        </p>
        <p class="mb-0">
            <i class="bi bi-envelope"></i> Mail: <a href="mailto:contacto@fullsoluciones.com.ar" class="text-decoration-none">contacto@fullsoluciones.com.ar</a>
        </p>
        <p class="mb-0">
            <i class="bi bi-telephone"></i> Tel: <a href="tel:+5493412158945" class="text-decoration-none">+54 9 3412158945</a>
        </p>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>