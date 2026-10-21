# Configurar el correo de recuperación de contraseña

La aplicación envía el correo de recuperación mediante SMTP con PHPMailer. En Render, las credenciales se configuran como variables de entorno; nunca las guardes en el repositorio.

## Gmail en Render

1. En la cuenta remitente de Google, activa la verificación en dos pasos y crea una contraseña de aplicación. No uses la contraseña normal de Gmail.
2. En Render, abre el servicio y entra a **Environment**. Configura:

   | Variable | Valor |
   | --- | --- |
   | `MAIL_TRANSPORT` | `smtp` |
   | `SMTP_HOST` | `smtp.gmail.com` |
   | `SMTP_PORT` | `587` |
   | `SMTP_SECURE` | `tls` |
   | `SMTP_USER` | Correo completo de Gmail remitente |
   | `SMTP_PASS` | Contraseña de aplicación de Google |
   | `MAIL_FROM` | El mismo correo remitente |
   | `MAIL_FROM_NAME` | `Mercado Digital` |

3. Guarda los cambios y espera a que Render complete el redeploy.
4. Solicita la recuperación de contraseña para una cuenta cuyo correo exista en `users.email`.

PHPMailer valida el certificado TLS del servidor SMTP de forma predeterminada. Para revisar fallos de entrega, abre los logs del servicio en Render. La API responde con el mismo mensaje tanto si el correo existe como si no, para no revelar qué cuentas están registradas.

## Desarrollo local con XAMPP

Para usar el mismo SMTP localmente, define las variables anteriores para Apache/PHP y reinicia Apache. No guardes la contraseña de aplicación en un archivo del proyecto. Si `MAIL_TRANSPORT` no está definido, el código conserva el envío local de PHP `mail()`.

Si Google no permite contraseñas de aplicación para la cuenta, usa las credenciales SMTP de un proveedor autorizado y configura su host, puerto, usuario y contraseña en las mismas variables. No desactives la verificación TLS del servidor.
