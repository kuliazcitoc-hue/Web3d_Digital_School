# Configurar correo de recuperación en Render

El plan gratuito de Render bloquea las conexiones SMTP salientes en los puertos 25, 465 y 587. Para mantener el servicio gratis, el proyecto envía los mensajes mediante la API HTTPS de Brevo por el puerto 443.

## Configuración de Brevo

1. Crea una cuenta en Brevo y abre **Transactional > Senders** (o **Settings > Senders, Domains, IPs > Senders**).
2. Añade como remitente el Gmail que quieres usar. Brevo enviará un código de verificación a esa dirección cuando el dominio del remitente no esté autenticado. Completa la verificación dentro de Brevo.
3. En Brevo, crea una API key transaccional desde **SMTP & API > API Keys**. No uses una clave SMTP. Cópiala en un lugar seguro: se mostrará una sola vez.
4. En Render, abre el servicio y ve a **Environment**. Configura:

   | Variable | Valor |
   | --- | --- |
   | `MAIL_TRANSPORT` | `brevo` |
   | `BREVO_API_KEY` | La API key de Brevo |
   | `MAIL_FROM` | La dirección Gmail verificada en Brevo |
   | `MAIL_FROM_NAME` | `Mercado Digital` |

5. Guarda los cambios y espera a que Render termine el redeploy.
6. Solicita la recuperación para una cuenta cuyo correo esté guardado en `users.email`. Revisa también spam.

No pongas la API key en GitHub ni la compartas en capturas o mensajes. Si la clave se expone, revócala en Brevo y genera otra. Los errores HTTP de envío se registran en los logs de Render sin imprimir la clave ni el correo del destinatario.

## Desarrollo local con XAMPP

Si `MAIL_TRANSPORT` no está definido, el código conserva el envío local con PHP `mail()`. Para probar Brevo localmente, define las mismas variables de entorno para Apache/PHP. La contraseña de aplicación Gmail no sirve como API key de Brevo.
