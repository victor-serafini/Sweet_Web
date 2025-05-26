<!-- filepath: c:\xampp\htdocs\sweet\remitos.php -->
<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    // Redirigir al login si no hay sesión activa
    header("Location: https://apptest.fullsoluciones.com.ar/index.php?error=Por favor, inicie sesión.");
    exit();
}

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el autoloader de Composer
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Conexión a la base de datos
$servername = "localhost";
$username_db = "phpmyadmin";
$password_db = "Galito*789";
$dbname = "sweet";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos.<br>";
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insertar datos en la tabla remitos (maestro)
    $fecha = $_POST['fecha']; // Nuevo campo
    $cli_nom = $_POST['cli_nom'];
    $cli_dir = $_POST['cli_dir'];
    $cli_loca = $_POST['cli_loca'];
    $cli_pcia = $_POST['cli_pcia'];
    $cli_iva = $_POST['cli_iva'];
    $cli_cuit = $_POST['cli_cuit'];
    $trans_nom = $_POST['trans_nom'];
    $trans_dir = $_POST['trans_dir'];
    $trans_tel = $_POST['trans_tel'];
    $trans_cuit = $_POST['trans_cuit'];
    $total = $_POST['total'];

    // Validar el formato de la fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        die("Error: El formato de la fecha debe ser AAAA-MM-DD.");
    }

    // Validar el formato del CUIT
    if (!preg_match('/^\d{11}$/', $cli_cuit)) {
        die("Error: El CUIT debe tener exactamente 11 dígitos.");
    }

    // Validar el valor de total
    if (!is_numeric($total) || $total < -9999999999.99 || $total > 9999999999.99) {
        die("Error: El valor de 'total' está fuera del rango permitido (-9999999999.99 a 9999999999.99).");
    }

    $sql_remito = "INSERT INTO remitos (fecha, cli_nom, cli_dir, cli_loca, cli_pcia, cli_iva, cli_cuit, trans_nom, trans_dir, trans_tel, trans_cuit, total) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_remito = $conn->prepare($sql_remito);
    $stmt_remito->bind_param("sssssssssssd", $fecha, $cli_nom, $cli_dir, $cli_loca, $cli_pcia, $cli_iva, $cli_cuit, $trans_nom, $trans_dir, $trans_tel, $trans_cuit, $total);

    // Depurar inserción en la tabla remitos
    if ($stmt_remito->execute()) {
        echo "Remito insertado correctamente.<br>";
    } else {
        die("Error al insertar remito: " . $stmt_remito->error);
    }

    $id_remito = $stmt_remito->insert_id; // Obtener el ID del remito insertado

    // Insertar datos en la tabla detalle_remitos (detalle)
    foreach ($_POST['detalle'] as $index => $detalle) {
        $cantidad = $_POST['cantidad'][$index];
        $costo = $_POST['costo'][$index];
        $costo_total = $_POST['costo_total'][$index];

        // Validar el valor de costo_total
        if (!is_numeric($costo_total) || $costo_total < -9999999999.99 || $costo_total > 9999999999.99) {
            die("Error: El valor de 'costo_total' está fuera del rango permitido (-9999999999.99 a 9999999999.99).");
        }

        $sql_detalle = "INSERT INTO detalle_remitos (id_rto, cantidad, detalle, costo, costo_total) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt_detalle = $conn->prepare($sql_detalle);
        $stmt_detalle->bind_param("iisdd", $id_remito, $cantidad, $detalle, $costo, $costo_total);

        // Depurar inserción en la tabla detalle_remitos
        if ($stmt_detalle->execute()) {
            echo "Detalle insertado correctamente.<br>";
        } else {
            die("Error al insertar detalle: " . $stmt_detalle->error);
        }
    }

    // Generar el contenido HTML para el PDF
    $fecha_formateada = date("d-m-Y", strtotime($fecha)); // Formatear la fecha a dd-mm-aaaa
    $html = "
    <div style='text-align: right; margin-bottom: 10px; margin-top: 80px;'> <!-- Ajustar margen superior para bajar el campo -->
        <p><strong>$fecha_formateada</strong></p> <!-- Eliminar el texto 'Fecha:' -->
    </div>
    <div style='margin-bottom: 8px; line-height: 1.2; margin-top: 75px;'> <!-- Ajustar margen superior para bajar cuatro líneas -->
        <p><strong>Nombre   :</strong> " . substr($cli_nom, 0, 50) . "<span style='display: inline-block; width: 200px;'></span><strong>Provincia:</strong> $cli_pcia</p>
        <p><strong>Dirección:</strong> " . substr($cli_dir, 0, 50) . "<span style='display: inline-block; width: 200px;'></span><strong>Localidad:</strong> $cli_loca</p>
        <p><strong>Condición IVA:</strong> $cli_iva<span style='display: inline-block; width: 200px;'></span><strong>CUIT:</strong> $cli_cuit</p>
    </div>

    <div style='margin-top: 20px; padding-left: 30px;'> <!-- Ajustar margen izquierdo para alinear la tabla -->
        <table style='width: 100%; border-collapse: collapse; border: 1px solid white; color: black;'> <!-- Bordes blancos y texto negro -->
            <tbody>";

    foreach ($_POST['detalle'] as $index => $detalle) {
        $cantidad = $_POST['cantidad'][$index];
        $costo = $_POST['costo'][$index];
        $costo_total = $_POST['costo_total'][$index];
        $html .= "
            <tr>
                <td style='width: 9%; text-align: center; border: 1px solid white;'>$cantidad</td> <!-- Bordes blancos -->
                <td style='width: 51%; border: 1px solid white;'>$detalle</td> <!-- Bordes blancos -->
                <td style='width: 20%; text-align: right; border: 1px solid white;'>" . number_format($costo, 2) . "</td> <!-- Bordes blancos -->
                <td style='width: 20%; text-align: right; border: 1px solid white;'>" . number_format($costo_total, 2) . "</td> <!-- Bordes blancos -->
            </tr>";
    }

    $html .= "
        </tbody>
    </table>
    <div style='position: fixed; bottom: 3cm; right: 20px; text-align: right;'> <!-- Bajar el total un enter más -->
        <h3>Total: $total</h3>
    </div>
    <div style='position: fixed; bottom: -70px; left: 0; right: 0; height: 5cm; text-align: left; font-size: 10px; padding-left: 20px; line-height: 0.8;'> <!-- Ajustar bottom -->
        <p><strong>Transporte:</strong> $trans_nom</p>
        <p><strong>Dirección:</strong> $trans_dir</p>
        <p><strong>Teléfono:</strong> $trans_tel</p>
        <p><strong>CUIT:</strong> $trans_cuit</p>
    </div>
    <style>
        table {
            width: 90%; /* Ajustar el ancho de la tabla para dejar espacio al pie */
            margin: 0 auto; /* Centrar la tabla */
        }
    </style>";

    // Configurar DOMPDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // Cargar el contenido HTML (sin incluir el head)
    $dompdf->loadHtml($html);

    // Renderizar el PDF
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Guardar el PDF en la carpeta /remitos
    $pdf_file = __DIR__ . "/remitos/remito_$id_remito.pdf"; // Ruta completa al archivo en el servidor

    // Depurar generación del PDF
    if (file_put_contents($pdf_file, $dompdf->output())) {
        echo "PDF generado correctamente.<br>";
    } else {
        die("Error al generar el PDF.");
    }

    // Generar la URL del PDF para el navegador
    $pdf_url = "https://apptest.fullsoluciones.com.ar/remitos/remito_$id_remito.pdf"; // URL pública del archivo

    // Mostrar mensaje con opción de imprimir
    echo "<script>
        const newWindow = window.open('$pdf_url', '_blank');
        if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
            alert('Por favor, habilite las ventanas emergentes para abrir el PDF.');
        }
        window.location.href='https://apptest.fullsoluciones.com.ar/dashboard.php';
    </script>";
    exit();
}

// Incluir el head de dashboard.php si existe
$head_path = __DIR__ . '/dashboard_head.php';
if (file_exists($head_path)) {
    include $head_path;
} else {
    // Incluir manualmente Bootstrap si el head no está disponible
    echo '
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Formulario de Remito</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>';
}

?>
<body>
    <!-- Encabezado -->
    <header>
        <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Sweety Snack y el Gran Cheff en Villa Constitución</span>
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a> <!-- Botón de cerrar sesión -->
            </div>
        </nav>
    </header>
    <div class="container mt-5">
        <h1>Formulario de Remito</h1>
        <form method="POST">
            <!-- Nuevo campo para la fecha -->
            <div class="row mb-3">
                <div class="col">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" style="max-width: 300px;" required> <!-- Ajustar ancho -->
                </div>
            </div>

            <!-- Datos del cliente -->
            <h3>Datos del Cliente</h3>
            <div class="row mb-3">
                <div class="col">
                    <label for="cli_nom" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="cli_nom" name="cli_nom" required>
                </div>
                <div class="col">
                    <label for="cli_dir" class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="cli_dir" name="cli_dir" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="cli_loca" class="form-label">Localidad</label>
                    <input type="text" class="form-control" id="cli_loca" name="cli_loca" required>
                </div>
                <div class="col">
                    <label for="cli_pcia" class="form-label">Provincia</label>
                    <input type="text" class="form-control" id="cli_pcia" name="cli_pcia" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="cli_iva" class="form-label">Condición IVA</label>
                    <select class="form-control" id="cli_iva" name="cli_iva" required>
                        <option value="Responsable Inscripto">Responsable Inscripto</option>
                        <option value="Monotributo">Monotributo</option>
                        <option value="Exento">Exento</option>
                    </select>
                </div>
                <div class="col">
                    <label for="cli_cuit" class="form-label">CUIT</label>
                    <input type="number" class="form-control" id="cli_cuit" name="cli_cuit" required>
                </div>
            </div>

            <hr style="border: 2px solid black;"> <!-- Línea horizontal antes de Datos del Transporte -->

            <!-- Datos del transporte -->
            <h3>Datos del Transporte</h3>
            <div class="row mb-3">
                <div class="col">
                    <label for="trans_nom" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="trans_nom" name="trans_nom" required>
                </div>
                <div class="col">
                    <label for="trans_dir" class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="trans_dir" name="trans_dir" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="trans_tel" class="form-label">Teléfono</label>
                    <input type="number" class="form-control" id="trans_tel" name="trans_tel" required>
                </div>
                <div class="col">
                    <label for="trans_cuit" class="form-label">CUIT</label>
                    <input type="number" class="form-control" id="trans_cuit" name="trans_cuit" required>
                </div>
            </div>

            <hr style="border: 2px solid black;"> <!-- Línea horizontal antes de Detalle del Remito -->

            <!-- Detalle del remito -->
            <h3>Detalle del Remito</h3>
            <div id="detalle-container">
                <div class="row mb-3">
                    <div class="col-2">
                        <input type="number" class="form-control cantidad" name="cantidad[]" placeholder="Cantidad" maxlength="3" max="999" required>
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control" name="detalle[]" placeholder="Detalle" required>
                    </div>
                    <div class="col-2">
                        <input type="number" step="0.01" class="form-control costo" name="costo[]" placeholder="Costo" required>
                    </div>
                    <div class="col-3">
                        <input type="number" step="0.01" class="form-control costo_total" name="costo_total[]" placeholder="Costo Total" readonly>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-row">Agregar Fila</button>

            <!-- Total -->
            <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="number" step="0.01" class="form-control" id="total" name="total" required>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Remito</button>
        </form>
    </div>
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

    <?php
    // Incluir el footer de dashboard.php si existe
    $footer_path = __DIR__ . '/dashboard_footer.php';
    if (file_exists($footer_path)) {
        include $footer_path;
    } else {
        echo "<!-- dashboard_footer.php no encontrado -->";
    }
    ?>
    <script>
        // Función para agregar una nueva fila al detalle
        document.getElementById('add-row').addEventListener('click', function () {
            const container = document.getElementById('detalle-container');
            const row = document.createElement('div');
            row.className = 'row mb-3';
            row.innerHTML = `
                <div class="col-2">
                    <input type="number" class="form-control cantidad" name="cantidad[]" placeholder="Cantidad" maxlength="3" max="999" required>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" name="detalle[]" placeholder="Detalle" required>
                </div>
                <div class="col-2">
                    <input type="number" step="0.01" class="form-control costo" name="costo[]" placeholder="Costo" required>
                </div>
                <div class="col-3">
                    <input type="number" step="0.01" class="form-control costo_total" name="costo_total[]" placeholder="Costo Total" readonly>
                </div>
            `;
            container.appendChild(row);

            // Agregar evento para calcular el costo total automáticamente
            addCalculationEvent(row);
        });

        // Función para agregar eventos de cálculo a una fila
        function addCalculationEvent(row) {
            const cantidadInput = row.querySelector('.cantidad');
            const costoInput = row.querySelector('.costo');
            const costoTotalInput = row.querySelector('.costo_total');

            // Escuchar cambios en cantidad y costo
            cantidadInput.addEventListener('input', () => {
                calculateTotal(cantidadInput, costoInput, costoTotalInput);
                updateGrandTotal();
            });
            costoInput.addEventListener('input', () => {
                calculateTotal(cantidadInput, costoInput, costoTotalInput);
                updateGrandTotal();
            });
        }

        // Función para calcular el costo total de una fila
        function calculateTotal(cantidadInput, costoInput, costoTotalInput) {
            const cantidad = parseFloat(cantidadInput.value) || 0;
            const costo = parseFloat(costoInput.value) || 0;
            const total = cantidad * costo;
            costoTotalInput.value = total.toFixed(2); // Mostrar con 2 decimales
        }

        // Función para actualizar el total general
        function updateGrandTotal() {
            const costoTotalInputs = document.querySelectorAll('.costo_total');
            let grandTotal = 0;

            costoTotalInputs.forEach(input => {
                grandTotal += parseFloat(input.value) || 0;
            });

            document.getElementById('total').value = grandTotal.toFixed(2); // Mostrar con 2 decimales
        }

        // Agregar eventos de cálculo a las filas existentes al cargar la página
        document.querySelectorAll('#detalle-container .row').forEach(row => addCalculationEvent(row));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>