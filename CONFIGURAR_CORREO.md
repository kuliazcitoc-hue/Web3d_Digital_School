# Configurar correo de recuperación en XAMPP

La aplicación usa el `sendmail.exe` incluido en XAMPP y Gmail por SMTP TLS. En esta instalación, la URL de la aplicación es `http://localhost/mercado_digital`; `mail_config.php` la usa para construir los enlaces de recuperación.

1. En la cuenta Gmail que enviará los mensajes, activa la verificación en dos pasos.
2. Crea una contraseña de aplicación desde la sección **Contraseñas de aplicaciones** de la cuenta de Google. Usa esa contraseña, no la contraseña normal de Gmail.
3. Abre `C:\xampp\sendmail\sendmail.ini` y reemplaza `TU_CORREO@gmail.com` por el correo remitente y `PEGA_AQUI_LA_CONTRASENA_DE_APLICACION` por la contraseña de aplicación. Pégala sin espacios.
4. Abre `C:\xampp\php\php.ini` y reemplaza `TU_CORREO@gmail.com` en `sendmail_from` por el mismo remitente.
5. Abre `mail_config.php` del proyecto y reemplaza `TU_CORREO@gmail.com` en `MAIL_FROM` por el mismo remitente.
6. En el panel de XAMPP, detén y vuelve a iniciar Apache para que lea `php.ini`.
7. Solicita una recuperación para una cuenta cuyo email esté guardado en `users.email`.

La configuración SMTP usa `smtp.gmail.com`, puerto `587` y TLS. Los errores de entrega se registran en `C:\xampp\sendmail\error.log`. No compartas la contraseña de aplicación ni la publiques en el proyecto.

Si Google no permite crear contraseñas de aplicación para la cuenta (por ejemplo, por políticas de una cuenta escolar), se necesita usar un proveedor SMTP autorizado por esa organización.