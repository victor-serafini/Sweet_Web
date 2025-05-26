<!-- filepath: c:\xampp\htdocs\sweet\test.php -->
<?php
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

// Consulta para obtener todos los registros de la tabla usuarios
$sql = "SELECT id, username, password FROM usuarios";
$result = mysqli_query($conn, $sql);

// Verificar si hay resultados
if (mysqli_num_rows($result) > 0) {
    // Mostrar los registros en una tabla HTML
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Password</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['password'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron registros en la tabla usuarios.";
}

// Cerrar conexión
mysqli_close($conn);
?>