<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    // Redirigir al login si no hay sesión activa
    header("Location: https://apptest.fullsoluciones.com.ar/index.php?error=Por favor, inicie sesión.");
    exit();
}

// Ruta del directorio de remitos
$directory = __DIR__ . '/remitos'; // Ruta local del directorio
$base_url = 'https://apptest.fullsoluciones.com.ar/remitos/'; // URL base para los archivos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Remitos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h1 class="text-center mb-4">Lista de Remitos</h1>
        <?php
        if (is_dir($directory)) {
            $files = array_diff(scandir($directory, SCANDIR_SORT_DESCENDING), ['.', '..']); // Obtener archivos en orden descendente
            $pdf_files = array_filter($files, fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'pdf'); // Filtrar solo PDFs

            if (!empty($pdf_files)) {
                echo '<ul class="list-group">';
                foreach ($pdf_files as $file) {
                    $file_path = $base_url . $file;
                    $file_date = date("d-m-Y H:i:s", filemtime($directory . '/' . $file)); // Fecha de modificación
                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                            <a href='$file_path' target='_blank' class='text-decoration-none'>$file</a>
                            <span class='badge bg-secondary'>$file_date</span>
                          </li>";
                }
                echo '</ul>';
            } else {
                echo '<div class="alert alert-warning text-center">No se encontraron archivos PDF en el directorio.</div>';
            }
        } else {
            echo '<div class="alert alert-danger text-center">El directorio no existe.</div>';
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>