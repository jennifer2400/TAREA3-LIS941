# Guía de Defensa Académica — La Cuponera SV (LIS941)

Este documento contiene la explicación conceptual, técnica y operativa del trabajo desarrollado para la **Persona 1**, así como un balotario de **preguntas y respuestas probables** que el docente o comité evaluador de la asignatura **LIS941** podría formular durante la presentación del proyecto.

---

## 1. Resumen Ejecutivo del Trabajo Realizado

Como responsable de la **Persona 1**, se implementó con éxito y de manera completamente funcional:
1. **Autenticación Unificada (Login):** Mecanismo de inicio de sesión seguro para clientes y empresas, conectado a MySQL mediante PDO y declaraciones preparadas.
2. **Registro Completo de Clientes:** Formulario con los 7 campos reglamentarios (usuario, correo, contraseña, nombres, apellidos, DUI, fecha de nacimiento), con validación obligatoria tanto en frontend (UX) como en backend (autoritativa).
3. **Cálculo Riguroso de Mayoría de Edad (18+):** Verificación matemática exacta con `DateTime::diff()` contemplando años bisiestos y días exactos transcurridos.
4. **Validación de Formatos Oficiales:** Validación de DUI salvadoreño mediante expresión regular `/^\d{8}-\d$/` y política estricta de contraseñas.
5. **Manejo Seguro de Sesiones:** Prevención de ataques de fijación de sesión mediante `session_regenerate_id(true)` tras el login y cookies con `HttpOnly` y `SameSite=Lax`.
6. **Esqueleto Relacional Compartido:** Diseño desacoplado entre credenciales (`usuarios`), personas naturales (`clientes`), personas jurídicas (`empresas`), catálogo (`roles`, `rubros`) y tokens de seguridad (`recuperacion_tokens`).

---

## 2. Recorrido de una Petición: Desde el Formulario hasta MySQL

### Flujo: Registro de un Cliente
1. **El Usuario envía el Formulario:**
   - El cliente llena los campos en `/registro`.
   - JavaScript (`registro.js`) formatea el DUI en tiempo real (`00000000-0`), calcula la edad instantáneamente y revisa los requisitos de la contraseña.
   - Al pulsar "Completar Registro", el navegador envía una petición `POST` al servidor con los datos y el campo oculto `csrf_token`.
2. **Entrada en el Front Controller (`public/index.php`):**
   - El servidor Apache recibe la petición y, mediante la regla `RewriteRule` de `.htaccess`, la canaliza a `public/index.php`.
   - Se ejecuta `Auth::initSession()`, configurando la sesión con directivas seguras.
   - El enrutador analiza la ruta `/registro` y el método `POST`, derivando la ejecución a `AuthController::registro()`.
3. **Validación en el Controlador (`app/controllers/AuthController.php`):**
   - **Verificación CSRF:** `Csrf::validate($_POST['csrf_token'])`. Si no coincide con `$_SESSION['csrf_token']`, se detiene la ejecución inmediatamente.
   - **Saneamiento:** `Validator::sanitize()` elimina caracteres nulos y espacios espurios.
   - **Validación de Negocio:**
     - Correo con `filter_var(..., FILTER_VALIDATE_EMAIL)`.
     - DUI con expresión regular `/^\d{8}-\d$/`.
     - Mayoría de edad con `Validator::validateAge()` comprobando que `$edad >= 18`.
     - Política de contraseña (longitud >= 8, mayúscula, minúscula, dígito y carácter especial).
   - **Comprobación de Unicidad en BD:**
     - `Usuario::existsByUsername()`
     - `Usuario::existsByEmail()`
     - `Cliente::existsByDui()`
   - Si ocurre algún error, se guardan los datos no sensibles en `$_SESSION['old_registro']` (¡la contraseña nunca se guarda!) y se redirige con `Flash::error()`.
4. **Persistencia Transaccional (`app/models/Cliente.php`):**
   - Se genera el hash irreversible de la contraseña:
     `$hash = password_hash($password, PASSWORD_DEFAULT);`
   - Se inicia una transacción PDO: `$db->beginTransaction()`.
   - Se inserta el registro en la tabla `usuarios` (con `id_rol = 2` correspondiente a cliente) y se recupera el ID autoincremental generado con `$db->lastInsertId()`.
   - Se inserta el registro complementario en la tabla `clientes` vinculándolo con el ID de usuario recién creado.
   - Se confirma la transacción con `$db->commit()`. Si alguna de las dos inserciones fallase, el bloque `catch` ejecuta `$db->rollBack()`, impidiendo que queden usuarios huérfanos o datos corruptos.
5. **Respuesta al Cliente:**
   - Se limpia la información temporal, se almacena un mensaje flash de éxito y se redirige con código HTTP 302 hacia `/registro/exitoso`.

---

## 3. Preguntas Frecuentes del Docente y Respuestas para la Defensa

### P1: ¿Por qué no utilizó un framework como Laravel o CodeIgniter?
> **Respuesta:**
> *"Para este proyecto de LIS941 se solicitó expresamente evitar dependencias y frameworks innecesarios que ocultaran la lógica de bajo nivel. Implementar un MVC artesanal en PHP 8 nativo con PDO permite demostrar un dominio pleno del ciclo de vida de la petición HTTP, el manejo nativo de sesiones, el enrutamiento y la seguridad sin recurrir a cajas negras o herramientas que compliquen la evaluación académica."*

---

### P2: ¿Cómo garantizó que un cliente no pueda falsificar su rol e ingresar como administrador?
> **Respuesta:**
> *"El rol del usuario nunca viaja como un parámetro en la petición, en campos ocultos del formulario ni en la URL. Cuando el usuario se registra, el modelo `Cliente::registrarCliente()` asigna explícitamente el `id_rol = 2` en el backend. Además, durante el login, el rol se obtiene ejecutando una consulta SQL `INNER JOIN roles r ON u.id_rol = r.id` directamente en la base de datos y se almacena en la sesión del servidor (`$_SESSION['rol']`), impidiendo cualquier manipulación desde el cliente."*

---

### P3: ¿Cómo calcula la edad y cómo maneja los años bisiestos?
> **Respuesta:**
> *"Utilizo la clase nativa `DateTime` y la función `checkdate()`. Primero valido que la fecha sea un día real del calendario gregoriano. Luego comparo la fecha de nacimiento con la fecha actual mediante `$today->diff($dob)->y`. La clase `DateTime` de PHP realiza cálculos astronómicos precisos de intervalos, por lo que una persona nacida el 29 de febrero de un año bisiesto cumplirá la mayoría de edad exactamente en la fecha legal correspondiente (1 de marzo en años no bisiestos)."*

---

### P4: ¿Por qué no utiliza `md5()` o `sha1()` para guardar las contraseñas?
> **Respuesta:**
> *"Las funciones `md5()` y `sha1()` son algoritmos de hashing generales y extremadamente rápidos, lo que los hace vulnerables a ataques de fuerza bruta modernos y tablas arcoíris (Rainbow Tables). En su lugar, utilicé la función recomendada por PHP: `password_hash($password, PASSWORD_DEFAULT)`, la cual implementa Bcrypt. Bcrypt incorpora automáticamente una sal criptográfica aleatoria (salt) y un factor de costo computacional ajustable (cost) que ralentiza deliberadamente los intentos de ataque por fuerza bruta. Para autenticar, utilizo `password_verify()`, que compara de forma segura en tiempo constante contra ataques de temporización."*

---

### P5: ¿Qué es la fijación de sesión (Session Fixation) y cómo la mitigó?
> **Respuesta:**
> *"La fijación de sesión ocurre cuando un atacante induce a una víctima a autenticarse utilizando un identificador de sesión previamente conocido por el atacante. Para mitigar esta vulnerabilidad, en el método `Auth::login()` ejecuto `session_regenerate_id(true)` inmediatamente después de verificar las credenciales con éxito. El parámetro `true` fuerza al servidor a descartar el identificador anterior y emitir uno completamente nuevo, revocando cualquier ID previo."*

---

### P6: ¿Para qué sirve el token CSRF y cómo funciona en su módulo?
> **Respuesta:**
> *"CSRF (Cross-Site Request Forgery) es un ataque en el que un sitio malicioso engaña al navegador autenticado del usuario para enviar solicitudes no autorizadas. En nuestro proyecto, el helper `Csrf` genera una cadena criptográfica de 32 bytes con `random_bytes()` que se guarda en la sesión. Cada formulario incluye este token en un campo oculto (`Csrf::field()`). Al procesar la petición `POST`, `Csrf::validate()` compara el token recibido con el de la sesión mediante `hash_equals()`. Si un atacante intenta disparar una petición externa, no podrá adivinar este token y la solicitud será rechazada."*

---

### P7: ¿Por qué separó la tabla `usuarios` de `clientes` y `empresas`?
> **Respuesta:**
> *"Por principio de responsabilidad única y normalización de base de datos. La tabla `usuarios` actúa como el núcleo común de autenticación para cualquier actor (id, usuario, correo, contraseña, rol, estado). Las tablas `clientes` y `empresas` representan especializaciones 1 a 1 de ese usuario. Un cliente almacena nombres, DUI y fecha de nacimiento; mientras que una empresa almacena nombre comercial, razón social, NIT y porcentaje de comisión. Esta arquitectura desacoplada permite que el inicio de sesión sea unificado y que mis compañeros de equipo extiendan los datos de empresas o clientes sin alterar el módulo de login."*

---

### P8: ¿Qué sucede si la validación de JavaScript en el navegador es deshabilitada?
> **Respuesta:**
> *"La seguridad del sistema permanece completamente intacta. En la arquitectura implementada, la validación en JavaScript (`registro.js`) es únicamente una mejora de experiencia de usuario (UX) para dar retroalimentación visual inmediata. Sin embargo, el servidor ejecuta en `AuthController` y `Validator` un conjunto estricto de validaciones obligatorias: formato de DUI, edad mínima de 18 años, política de contraseñas y unicidad en MySQL. Ninguna petición puede persistir datos sin superar los filtros autoritativos de PHP."*

---

### P9: ¿Cómo probó que su módulo realmente funciona?
> **Respuesta:**
> *"Desarrollé una suite automatizada de pruebas en PHP (`tests/test_persona1.php`) que ejecuta 49 aserciones reales contra la base de datos MySQL/MariaDB activa. Se verificaron casos como: registro exitoso de adultos, rechazo de menores de 16 y 17 años, manejo de nacidos en 29 de febrero, formatos incorrectos de DUI, duplicidad de usuario/correo/DUI, verificación de contraseñas válidas e inválidas, autenticación de empresas aprobadas y pendientes, y aislamiento de roles. Las 49 pruebas se ejecutaron con 100% de éxito."*

---

### P10: ¿Cuál es el límite del trabajo entregado respecto a sus compañeros?
> **Respuesta:**
> *"Mi responsabilidad abarca exclusivamente la Persona 1 (login, registro de clientes 18+, validaciones y esqueleto compartido). Por lo tanto, no implementé la aprobación/rechazo activo de empresas por parte del administrador ni el envío de correos SMTP para recuperar contraseñas, ya que corresponden a Persona 2 y Persona 3. No obstante, dejé el esqueleto técnico completo: los campos relacionales (`estado_aprobacion`, `recuperacion_tokens`, `promociones`), los modelos base y las interfaces informativas para que mis compañeros acoplen sus módulos sin tener que rediseñar la base de datos."*
