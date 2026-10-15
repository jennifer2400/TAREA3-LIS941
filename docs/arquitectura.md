# Arquitectura de Software — La Cuponera SV

## 1. Visión General del Sistema

**La Cuponera SV** está construida bajo el patrón arquitectónico **Modelo-Vista-Controlador (MVC)** utilizando **PHP 8 nativo**, sin dependencias de frameworks externos pesados. Esto garantiza un código limpio, comprensible y directamente defendible ante el comité evaluador de la asignatura **LIS941 (Lenguajes Interpretados en el Servidor)**.

```
                              [ Petición HTTP ]
                                      │
                                      ▼
                           public/index.php (Front Controller)
                                      │
                         ┌────────────┴────────────┐
                         ▼                         ▼
                  Auth::initSession()      Router (Despacho)
                                                   │
                                                   ▼
                                         [ Controlador ]
                                      (ej. AuthController)
                                       /               \
                                      /                 \
                                     ▼                   ▼
                              [ Modelo (PDO) ]       [ Vista (HTML5) ]
                              (ej. Usuario, Cliente) (ej. login, dashboard)
                                     │                   │
                                     ▼                   ▼
                              MySQL / MariaDB      Respuesta al Navegador
```

---

## 2. Organización del Proyecto

```
c:\proyectos_blueone\UDB\LIS\TAREA3/
├── app/
│   ├── config/
│   │   ├── config.php            # Configuración activa del entorno local
│   │   ├── config.example.php    # Plantilla de configuración versionable sin contraseñas
│   │   └── database.php          # Conexión PDO Singleton (UTF-8, emulación desactivada)
│   ├── controllers/
│   │   ├── HomeController.php    # Catálogo público y promociones demostrativas
│   │   ├── AuthController.php    # Login unificado, registro 18+, logout y control de sesión
│   │   ├── ClienteController.php # Panel privado del cliente autenticado y perfil
│   │   ├── EmpresaController.php # Panel de la empresa, validación de aprobación y esqueleto
│   │   ├── AdminController.php   # Panel del administrador y métricas del esqueleto compartido
│   │   └── PasswordResetController.php # Base técnica compartida para recuperación de contraseñas
│   ├── models/
│   │   ├── Usuario.php           # Núcleo de credenciales, roles y búsqueda de usuarios
│   │   ├── Cliente.php           # Perfil de cliente y registro atómico transaccional
│   │   ├── Empresa.php           # Perfil empresarial, consulta de NIT y estado de aprobación
│   │   └── PasswordReset.php     # Generación de tokens SHA-256 y expiración
│   ├── helpers/
│   │   ├── helpers.php           # Funciones globales: e() para XSS, url() y asset()
│   │   ├── Auth.php              # Guardias de autenticación, control de roles y regeneración de ID
│   │   ├── Csrf.php              # Generación y verificación criptográfica de tokens CSRF
│   │   ├── Validator.php         # Validación de DUI, mayoría de edad exacta (18+) y contraseñas
│   │   └── Flash.php             # Mensajes temporales de sesión (success, error, warning, info)
│   └── views/
│       ├── layouts/              # Plantillas maestras (header, navbar, footer, alerts)
│       ├── home/                 # Vista pública de inicio con ofertas demostrativas
│       ├── auth/                 # Formulario de login, registro de clientes y pantalla de éxito
│       ├── cliente/              # Panel de control del cliente individual
│       ├── empresa/              # Panel de control de la empresa
│       ├── admin/                # Panel de control del administrador (esqueleto de integración)
│       └── errors/               # Páginas personalizadas de error HTTP 403 y 404
├── database/
│   ├── schema.sql                # DDL reproducible de tablas, llaves foráneas e índices
│   └── seeds.sql                 # Datos demostrativos con contraseñas seguras hasheadas
├── docs/
│   ├── arquitectura.md           # Explicación del ciclo de vida y diseño de software
│   ├── base_datos.md             # Diccionario de datos y diagrama relacional
│   ├── integracion_companeros.md # Puntos de acople para Persona 2 y Persona 3
│   └── defensa_academica.md      # Guía para la defensa académica y preguntas del docente
├── public/
│   ├── index.php                 # Front controller con enrutamiento limpio
│   ├── .htaccess                 # Reescritura mod_rewrite para Apache
│   ├── css/
│   │   └── app.css               # Estilos visuales de La Cuponera SV
│   └── js/
│       ├── app.js                # Interacciones generales y conmutador ocular de contraseñas
│       └── registro.js           # Validación reactiva de DUI, cálculo de edad y contraseña
├── tests/
│   └── test_persona1.php         # Suite de pruebas automatizadas con 49 aserciones
├── .gitignore                    # Reglas de exclusión para Git y control de versiones
├── .htaccess                     # Protección de carpetas privadas en la raíz
└── README.md                     # Guía de instalación y puesta en marcha
```

---

## 3. Ciclo de Vida de una Petición (Request Lifecycle)

1. **Entrada Única (`public/index.php`):**
   - Apache redirige todas las solicitudes a `public/index.php` mediante `.htaccess`.
   - Se carga la configuración activa (`app/config/config.php`).
   - Se establece la zona horaria oficial (`America/El_Salvador`).
   - Se inicializa la sesión mediante `Auth::initSession()` con cookies seguras (`HttpOnly`, `SameSite=Lax`).

2. **Resolución y Enrutamiento:**
   - El enrutador analiza la URL limpia (`REQUEST_URI` o `PATH_INFO`) o el parámetro alternativo `?route=...`.
   - Se determina el método HTTP (`GET` o `POST`) y la ruta solicitada.

3. **Intervención del Controlador:**
   - Si la ruta requiere autenticación, el controlador invoca `Auth::requireAuth()` o `Auth::requireRole(...)`.
   - Si se trata de un formulario `POST`, se valida el token anti-CSRF con `Csrf::validate()`.
   - Se sanean las entradas con `Validator::sanitize()`.
   - Se validan las reglas de negocio con `Validator`.

4. **Persistencia mediante Modelos (PDO):**
   - El controlador interactúa con los modelos (`Usuario`, `Cliente`, `Empresa`).
   - Todas las consultas SQL emplean **declaraciones preparadas (Prepared Statements)** con parámetros vinculados (`:param`).
   - Las operaciones compuestas (como el registro simultáneo de usuario y cliente) se ejecutan dentro de una **transacción PDO** atómica (`beginTransaction()`, `commit()`, `rollBack()`).

5. **Renderizado de la Vista:**
   - El controlador prepara los datos y carga la vista correspondiente (`app/views/...`).
   - Toda salida generada dinámicamente es escapada con `e()` (`htmlspecialchars`) para evitar vulnerabilidades XSS.

---

## 4. Medidas de Seguridad Implementadas

| Vulnerabilidad | Mecanismo de Protección Implementado |
| :--- | :--- |
| **Inyección SQL** | Uso obligatorio de PDO con `PDO::ATTR_EMULATE_PREPARES => false` y consultas parametrizadas. |
| **Robo / Fijación de Sesión** | `session_regenerate_id(true)` inmediatamente después del login exitoso; cookies con banderas `HttpOnly` y `SameSite=Lax`. |
| **Ataques CSRF** | Tokens criptográficos aleatorios de 32 bytes (`bin2hex(random_bytes(32))`), verificados con `hash_equals()`. |
| **Cross-Site Scripting (XSS)** | Filtrado estricto en vistas mediante la función global `e($valor)`. |
| **Contraseñas en Texto Plano** | Hasheo unidireccional con `password_hash($pass, PASSWORD_DEFAULT)` (Bcrypt) y verificación con `password_verify()`. |
| **Manipulación de Roles en Cliente** | El rol de un usuario se lee directamente de la base de datos al autenticar; ningún formulario o parámetro HTTP puede alterar el rol. |
| **Acceso a Archivos Privados** | Regla en `.htaccess` raíz que bloquea el acceso HTTP directo a `app/`, `database/` y `docs/`. |
