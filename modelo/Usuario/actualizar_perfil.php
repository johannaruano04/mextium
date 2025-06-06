<?php
// Después de actualizar la contraseña
$mail = new PHPMailer(true);
// ...configuración SMTP igual que arriba...
$mail->addAddress($email);
$mail->Subject = "Tu contraseña ha sido cambiada";
$mail->Body = "Hola,\n\nTe informamos que tu contraseña ha sido actualizada. Si no fuiste tú, contacta soporte de inmediato.";
$mail->send();

// Después de un login exitoso
$mail = new PHPMailer(true);
// ...configuración SMTP igual que arriba...
$mail->addAddress($email);
$mail->Subject = "Nuevo inicio de sesión en tu cuenta";
$mail->Body = "Hola,\n\nSe ha detectado un nuevo inicio de sesión en tu cuenta. Si no fuiste tú, cambia tu contraseña.";
$mail->send();