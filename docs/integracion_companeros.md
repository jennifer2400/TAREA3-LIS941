# Guía de Integración para Compañeros de Equipo — La Cuponera SV

Este documento detalla los **contratos de integración, métodos disponibles y puntos de acople** preparados por el **Módulo Persona 1** para que los compañeros de equipo integren sus responsabilidades sin solapamientos ni duplicidad de código.

---

## 1. Resumen de Responsabilidades y Delimitación

| Módulo / Persona | Estado en este Entregable | Responsable |
| :--- | :--- | :--- |
| **Persona 1 (Este Módulo)** | **100% Completo y Funcional** | Registro de clientes (18+), Login unificado clientes/empresas, Sesiones, Seguridad, Esqueleto BD. |
| **Persona 2 (Administración & Empresas)** | **Esqueleto Preparado** | Mantenimiento de empresas, aprobación/rechazo administrativo, publicación de ofertas. |
| **Persona 3 (Transaccional & Soporte)** | **Esqueleto Preparado** | Compra de cupones, canje, facturación, reportes y envío de correos SMTP para recuperar contraseña. |

---

## 2. Puntos de Integración Técnicos

### 2.1. Módulo Administrativo: Aprobación / Rechazo de Empresas
- **Tabla implicada:** `empresas`
- **Campo clave:** `estado_aprobacion` (`'pendiente'`, `'aprobada'`, `'rechazada'`).
- **Método ya implementado en el modelo:**
  ```php
  // app/models/Empresa.php
  Empresa::updateApprovalStatus(int $empresaId, string $status): bool;
  ```
- **Lógica para el compañero:**
  En el controlador administrativo del compañero (`AdminController`), solo debe consultar las empresas en estado `'pendiente'`, mostrar los botones de **Aprobar** o **Rechazar**, e invocar `Empresa::updateApprovalStatus($id, 'aprobada')`.
- **Efecto automático:**
  Cuando la empresa inicie sesión, `AuthController` y `EmpresaController` ya están programados para reconocer el estado. Si está aprobada, mostrará el panel verde de operaciones; si está pendiente o rechazada, mostrará el banner de restricción.

---

### 2.2. Flujo Completo de Recuperación de Contraseñas
- **Tabla implicada:** `recuperacion_tokens`
- **Modelo disponible:** `app/models/PasswordReset.php`
- **Métodos listos para usar:**
  1. **Generar token y hash:**
     ```php
     $rawToken = PasswordReset::createResetToken($userId, 3600); // 1 hora de validez
     ```
  2. **Verificar token recibido en enlace:**
     ```php
     $tokenData = PasswordReset::verifyToken($rawToken);
     // Retorna array con datos del token y del usuario si es válido y no ha expirado
     // Retorna null si es inválido, consumido o expirado
     ```
  3. **Marcar token consumido tras actualizar contraseña:**
     ```php
     Usuario::updatePassword($userId, password_hash($newPass, PASSWORD_DEFAULT));
     PasswordReset::markAsUsed($tokenData['id']);
     ```
- **Lo que debe agregar el compañero:**
  - Configurar PHPMailer o `mail()` para enviar el correo con el enlace: `url('/restablecer-password?token=' . $rawToken)`.
  - Crear la vista del formulario para ingresar y confirmar la nueva contraseña.

---

### 2.3. Publicación de Ofertas y Promociones
- **Tabla creada en esquema:** `promociones`
- **Llave foránea:** `id_empresa` vinculada a `empresas(id)`.
- **Lógica para el compañero:**
  Al crear una oferta, se obtiene el ID de la empresa autenticada mediante:
  ```php
  $user = Auth::user();
  $empresa = Empresa::findByUserId($user['id']);
  $idEmpresa = $empresa['id'];
  ```
  Esto garantiza que una empresa nunca publique promociones a nombre de otra.

---

### 2.4. Compra y Canje de Cupones
- **Tabla creada en esquema:** `cupones_comprados`
- **Llaves foráneas:**
  - `id_promocion` vinculada a `promociones(id)`.
  - `id_cliente` vinculada a `clientes(id)`.
- **Lógica para el compañero:**
  Al procesar la compra desde la sesión del cliente:
  ```php
  $user = Auth::user();
  $cliente = Cliente::findByUserId($user['id']);
  $idCliente = $cliente['id'];
  // Generar código único de cupón (ej. CUP-XXXXXXXX) e insertar en cupones_comprados
  ```

---

## 3. Helpers y Utilidades Comunes a Disposición de los Compañeros

1. **Control de Sesión y Permisos:**
   ```php
   Auth::requireAuth(); // Redirige a login si no hay sesión
   Auth::requireRole('administrador'); // Restringe a rol específico (devuelve 403)
   Auth::requireRole(['cliente', 'empresa']); // Permite múltiples roles
   $userId = Auth::id(); // Retorna el id de usuario activo
   ```
2. **Protección CSRF en Formularios POST:**
   ```php
   // En la vista:
   <?= Csrf::field() ?>
   
   // En el controlador:
   if (!Csrf::validate($_POST['csrf_token'] ?? null)) { ... }
   ```
3. **Mensajes Flash:**
   ```php
   Flash::success('Operación completada con éxito.');
   Flash::error('Ocurrió un error.');
   Flash::warning('Advertencia de prueba.');
   Flash::info('Mensaje informativo.');
   ```
4. **Prevención XSS en Vistas:**
   ```php
   <?= e($variableProvenienteDeBD) ?>
   ```
5. **Generador de Enlaces Contextual:**
   ```php
   <?= url('/ruta') ?>       // Compatible con XAMPP y php -S
   <?= asset('/css/app.css') ?> // Para recursos estáticos
   ```
