# Desplegar Mercado Digital en Render

La aplicación usa PHP, Apache y MySQL (`mysqli`). El servicio web se ejecuta en Docker; la base MySQL debe estar en un proveedor externo accesible desde Render. Render no crea una base MySQL con este Blueprint.

## 1. Preparar la base de datos

El archivo SQL recibido contiene tanto la estructura como registros insertados. No lo subas al repositorio ni lo importes a una base externa si no quieres copiar esos datos. Para empezar con una base vacía, importa `database_schema.sql`; crea las tablas `users`, `products`, `purchases`, `conversations`, `messages`, `admin`, `credits`, `invite_codes` y `password_reset_tokens`, con sus índices y relaciones.

La estructura recibida ya incluye `users.email` y `password_reset_tokens`; no ejecutes `password_reset_migration.sql` al crear esta base.

Después, crea una instancia **TiDB Cloud Starter en modo MySQL-compatible** e importa el esquema limpio. En la opción de conexión pública/directa, copia el host, puerto, nombre de base, usuario completo (incluido el prefijo que muestra TiDB) y contraseña que te entrega TiDB. Starter exige TLS. El servicio ya está preparado para verificar el certificado del servidor usando el almacén CA del contenedor.

No subas volcados con registros ni credenciales a GitHub: pueden contener datos personales o información de autenticación.

Si ya tienes una base creada antes de esta actualización, ejecuta una sola vez el contenido de `product_soft_delete_migration.sql` en esa base antes de desplegar el nuevo código. La migración agrega el marcador necesario para retirar productos sin borrar el historial de compras. En una base nueva no hace falta ejecutarla porque la columna ya está incluida en `database_schema.sql`.

El panel de administrador permite consultar productos publicados, agotados, pausados y retirados. Los productos agotados o retirados se conservan en la base, pero ya no aparecen disponibles para comprar. Al editar un producto agotado, el vendedor puede cargar stock nuevamente. Si una imagen antigua aparece como “Sin imagen”, vuelve a subirla desde la edición del producto; las rutas de archivos locales no están disponibles en Render.

## 2. Publicar el código en GitHub

Sube estos archivos del proyecto a un repositorio privado o público. No agregues contraseñas ni el volcado SQL con datos.

## 3. Crear el servicio en Render

1. En Render, crea un **Blueprint** y conecta el repositorio.
2. Render detectará `render.yaml` y propondrá el servicio web `mercado-digital`. Confirma la creación.
3. En el servicio, abre **Environment** y configura `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y `DB_PASS` con los valores de TiDB. Para TiDB Cloud Starter, el puerto que normalmente muestra la conexión es `4000`. Deja `DB_SSL=verify_ca` y `DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt`.
4. Espera a que el despliegue termine y abre la URL pública del servicio.

El Blueprint solicita el plan gratuito para evitar cargos automáticos; los servicios web gratuitos pueden suspenderse cuando están inactivos y tardar en despertar. El Dockerfile instala las extensiones `mysqli` y `mbstring`, certificados CA del sistema y configura Apache para escuchar en el puerto `10000`.

Configura también `ADMIN_SIGNUP_CODE` y, solo si vas a permitir registros con un código de invitación temporal, `TEMP_SIGNUP_CODE` como secretos en Render. No compartas esos códigos ni los incluyas en el repositorio. Si `TEMP_SIGNUP_CODE` no se define, los registros requieren un código presente y sin usar en `invite_codes`.

La base nueva no tiene cuentas ni códigos de invitación. Antes de abrir el registro, crea un código aleatorio de hasta 20 caracteres en `invite_codes` y registra la primera cuenta usando ese código junto con `ADMIN_SIGNUP_CODE`. Para que las comisiones se guarden, inicializa también `admin` con `id = 1` y `total_comisiones = 0`. Una vez creada la primera cuenta administradora, elimina `ADMIN_SIGNUP_CODE` de Render para impedir que se creen más administradores con ese código. No configures un código temporal compartido para el registro público.

## Recuperación de contraseña por correo

`password_reset.php` envía los mensajes con `mail()` de PHP. La configuración local de sendmail de XAMPP no se incluye en el contenedor, por lo que la recuperación de contraseña por correo no quedará lista automáticamente en Render. Para habilitarla hace falta configurar un servicio SMTP y adaptar el envío de correo; `MAIL_FROM` por sí solo no configura un servidor SMTP.

`APP_BASE_URL` se toma de `RENDER_EXTERNAL_URL` en Render para construir los enlaces de recuperación. En local se conserva `http://localhost/mercado_digital`.
