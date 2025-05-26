<!-- filepath: c:\xampp\htdocs\sweet\logout.php -->
<?php
session_start();
session_destroy(); // Destruir la sesión
header("Location: https://apptest.fullsoluciones.com.ar/index.php?message=Sesión cerrada exitosamente.");
exit();
?>