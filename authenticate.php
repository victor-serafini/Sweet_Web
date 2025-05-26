<?php
session_start();

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Conexión a la base de datos
    $servername = "localhost";
    $username_db = "phpmyadmin";
    $password_db = "Galito*789";
    $dbname = "sweet";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    // Validar credenciales
    $sql = "SELECT * FROM usuarios WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Depuración: Verificar si se obtuvieron resultados
    if ($result === false) {
        die("Error en la ejecución de la consulta: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        // Credenciales válidas, establecer sesión
        $_SESSION['username'] = $username;
        echo "Inicio de sesión exitoso. Redirigiendo al dashboard...";
        header("Refresh: 2; URL=https://apptest.fullsoluciones.com.ar/dashboard.php"); // Redirigir al dashboard
        exit();
    } else {
        // Redirigir al login con un mensaje de error
        echo "Credenciales incorrectas. Redirigiendo al login...";
        header("Refresh: 2; URL=https://apptest.fullsoluciones.com.ar/index.php?error=Credenciales incorrectas.");
        exit();
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();
} else {
    // Si se accede directamente, redirigir al login
    echo "Acceso no autorizado. Redirigiendo al login...";
    header("Refresh: 2; URL=https://apptest.fullsoluciones.com.ar/index.php");
    exit();
}
?>