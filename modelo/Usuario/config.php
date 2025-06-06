<?php
// filepath: c:\xampp\htdocs\Mextium 3\config\config.php
$host = "localhost";
$user = "root";
$pass = ""; // Cambia si tienes contraseña en tu MySQL
$dbname = "mextium"; // Cambia por el nombre real de tu base de datos

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
