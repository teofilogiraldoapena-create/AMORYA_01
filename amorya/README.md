# AMORYA - Tienda virtual PHP + MySQL

## Requisitos
- XAMPP con Apache y MySQL
- PHP 8.0+ recomendado
- Navegador web

## Instalación local
1. Copia la carpeta `amorya` a `C:\xampp\htdocs\amorya`.
2. En XAMPP inicia Apache y MySQL.
3. Abre `http://localhost/phpmyadmin`.
4. Importa `database/amorya.sql`.
5. Revisa `config/config.php` y ajusta usuario/clave de MySQL si corresponde.
6. Abre `http://localhost/amorya/setup_admin.php` para crear el primer administrador.
7. Inicia sesión en `http://localhost/amorya/admin/login.php`.
8. **Por seguridad, elimina `setup_admin.php` después de crear al administrador.**

## Funciones incluidas en este primer entregable
- Catálogo público y búsqueda por nombre.
- Carrito de compras en sesión.
- Checkout con datos del cliente, delivery o recojo, y selección de método de pago.
- Registro de pedidos y detalle de productos.
- Panel administrativo con acceso protegido.
- Gestión básica de productos (crear, editar, activar/desactivar).
- Listado de pedidos y cambio de estado.
- Registro de venta al marcar un pedido como pagado/confirmado, descontando stock una sola vez.
- Consultas preparadas PDO, escape de salida y token CSRF en formularios de escritura.

## Importante
- Los métodos Yape, Plin, transferencia y efectivo se registran como método seleccionado; esta versión **no confirma pagos automáticamente**.
- No se recopilan números completos de tarjeta. Para tarjetas se debe integrar una pasarela real (por ejemplo, un proveedor compatible con Perú) en el servidor.
- Antes de publicar en Internet: HTTPS obligatorio, contraseñas fuertes, configuración de producción, copias de seguridad, pruebas de seguridad y eliminación del instalador.
- Es una base inicial funcional que se puede ampliar con comprobantes, reportes avanzados, envío de correos, cálculo de delivery y seguimiento para clientes.
