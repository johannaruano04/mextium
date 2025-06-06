<?php
include 'conexion.php';

$token = $_GET['token'];

if (isset($token)) {
    $query = "SELECT id, reset_expira FROM usuarios WHERE reset_token='$token'";
    $result = mysqli_query($conn, $query);
    $usuario = mysqli_fetch_assoc($result);

    if ($usuario && strtotime($usuario['reset_expira']) > time()) {
        // Mostrar formulario para nueva contraseña
        // Al enviar, actualiza la contraseña y borra el token
    } else {
        echo "El enlace ha expirado o es inválido.";
    }
}
?>