<!-- filepath: c:\xampp\htdocs\sweet\login.php -->
<?php
session_start();

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Configuración de la conexión a la base de datos
    $servername = "localhost";
    $username_db = "phpmyadmin";
    $password_db = "Galito*789";
    $dbname = "sweet";

    // Crear conexión
    $conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

    // Verificar conexión
    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Actualizar las contraseñas existentes en la base de datos
    $sql_update = "SELECT id, password FROM usuarios";
    $result_update = mysqli_query($conn, $sql_update);
    while ($row_update = mysqli_fetch_assoc($result_update)) {
        $hashed_password = password_hash($row_update['password'], PASSWORD_DEFAULT);
        $update_sql = "UPDATE usuarios SET password = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $row_update['id']);
        mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
    }

    // Obtener datos del formulario
    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Consulta para verificar las credenciales
    $sql = "SELECT * FROM usuarios WHERE username = ?"; // Cambié 'usuario' por 'username'
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Validar credenciales
    if ($row = mysqli_fetch_assoc($result)) {
        // Verificar la contraseña cifrada
        if (password_verify($password, $row['password'])) {
            $_SESSION['authenticated'] = true;
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['perfil'] = $row['perfil'];

            // Verificar si los encabezados ya se enviaron
            if (headers_sent($file, $line)) {
                die("Error: Los encabezados ya se enviaron en el archivo $file en la línea $line.");
            }

            header("Location: test.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    // Cerrar conexión
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>