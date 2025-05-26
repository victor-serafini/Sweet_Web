<!-- filepath: c:\xampp\htdocs\sweet\index.php -->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Sweety Snack y el Gran Cheff en Villa Constitución</span>
            </div>
        </nav>
    </header>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3 class="text-center">Iniciar Sesión</h3>
                <?php
                if (isset($_GET['error'])) {
                    echo "<div class='alert alert-danger text-center'>{$_GET['error']}</div>";
                }
                ?>
                <form action="authenticate.php" method="POST"> <!-- Redirigir a authenticate.php -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Espacios en blanco antes del footer -->
    <div style="height: 11rem;"></div> <!-- 5rem + 4rem adicionales -->

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
</body>
</html>