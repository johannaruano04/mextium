<?php
include '../../modelo/conexion.php'; // Ajusta la ruta según tu estructura
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Ajusta la ruta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $query = "SELECT id FROM usuarios WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $usuario = mysqli_fetch_assoc($result);

    if ($usuario) {
        // 1. Generar token y fecha de expiración
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));
        mysqli_query($conn, "UPDATE usuarios SET reset_token='$token', reset_expira='$expira' WHERE id={$usuario['id']}");

        // 2. Preparar y enviar el correo
        $enlace = "http://tusitio.com/vista/Usuario/restablecer.php?token=$token";
        $asunto = "Recupera tu contraseña - Mextium";
        $mensaje = "Hola,\n\nHaz clic en este enlace para restablecer tu contraseña:\n$enlace\n\nSi no solicitaste este cambio, ignora este mensaje.";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'TU_USUARIO_MAILTRAP';
            $mail->Password = 'TU_PASSWORD_MAILTRAP';
            $mail->Port = 2525;

            $mail->setFrom('no-reply@mextium.com', 'Mextium');
            $mail->addAddress($email);

            $mail->Subject = $asunto;
            $mail->Body = $mensaje;

            $mail->send();
            echo "<script>alert('Se ha enviado un enlace a tu correo.'); window.location='ingreso.html';</script>";
        } catch (Exception $e) {
            echo "<script>alert('No se pudo enviar el correo.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Correo no encontrado.'); window.history.back();</script>";
    }
}
?>