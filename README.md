# La Cuponera SV — Plataforma Académica de Ofertas y Cupones

Proyecto desarrollado para la asignatura **LIS941: Lenguajes Interpretados en el Servidor**.  
**Módulo Desarrollado:** **Persona 1 — Autenticación, Registro de Clientes y Esqueleto Compartido**.

---

## 1. Alcance del Módulo (Persona 1)

### Funcionalidades 100% Desarrolladas y Verificadas:
* **Inicio de Sesión Unificado:** Acceso para Clientes, Empresas y Administrador conectado a MySQL mediante PDO y `password_verify()`.
* **Registro Completo de Clientes:** Formulario con los 7 campos reglamentarios (usuario, correo, contraseña, nombres, apellidos, DUI y fecha de nacimiento).
* **Validación Rigurosa de Mayoría de Edad (18+):** Verificación de edad exacta considerando años bisiestos (ej. 29 de febrero) y fechas reales de calendario.
* **Validación de DUI Salvadoreño:** Expresión regular `/^\d{8}-\d$/` (8 dígitos + guion + 1 dígito de control) con auto-formateo dinámico en frontend.
* **Política de Contraseñas:** Mínimo 8 caracteres, al menos 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial, almacenadas con hash Bcrypt (`password_hash`).
* **Seguridad y Control de Sesión:** Regeneración de identificador de sesión (`session_regenerate_id`), protección CSRF con tokens criptográficos de 32 bytes y prevención de inyección SQL con consultas preparadas.
* **Interfaz Visual Propia:** Diseño responsive con Bootstrap 5, Bootstrap Icons y estilos personalizados en CSS.
* **Esqueleto Compartido:** Estructura relacional completa para la integración de los compañeros (`empresas`, `roles`, `rubros`, `recuperacion_tokens`, `promociones`, `cupones_comprados`).

### Funcionalidades Delimitadas para Integración de Compañeros:
* Aprobación / rechazo administrativo de empresas (Persona 2).
* Publicación y catálogo activo de ofertas de comercios (Persona 2).
* Carrito de compra, facturación y canje en tienda (Persona 3).
* Envío de correos SMTP para recuperación de contraseñas (Persona 3).

---

## 2. Tecnologías Utilizadas

* **Lenguaje:** PHP 8.2+ (PHP nativo sin frameworks pesados).
* **Base de Datos:** MySQL / MariaDB (motor InnoDB, cotejamiento `utf8mb4_unicode_ci`).
* **Servidor Local:** Apache (vía XAMPP) o Servidor Integrado de PHP (`php -S`).
* **Frontend:** HTML5, CSS3, Bootstrap 5.3.3, Bootstrap Icons 1.11.3 y JavaScript nativo.
* **Criptografía:** `password_hash()` con algoritmo Bcrypt y `random_bytes()` para tokens de seguridad.

---

## 3. Guía Paso a Paso para la Puesta en Marcha

### Paso 1: Comprobar las herramientas necesarias
Asegúrate de contar con PHP 8.0 o superior y MySQL/MariaDB instalados en tu computadora (por ejemplo, a través de XAMPP).
Puedes verificarlo en tu terminal:
```bash
php -v
mysql --version
```

### Paso 2: Iniciar los servicios en XAMPP
1. Abre el **XAMPP Control Panel**.
2. Inicia el módulo **Apache**.
3. Inicia el módulo **MySQL**.

### Paso 3: Crear la Base de Datos e Importar los Scripts SQL
1. Abre tu terminal o cliente MySQL (phpMyAdmin o CLI) y ejecuta:
```sql
CREATE DATABASE IF NOT EXISTS lacuponera_sv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
2. Importa el esquema de tablas y los datos demostrativos ubicados en la carpeta `database/`:
```bash
# Si utilizas el puerto estándar 3306:
mysql -u root -p lacuponera_sv < database/schema.sql
mysql -u root -p lacuponera_sv < database/seeds.sql

# Si tu XAMPP utiliza el puerto alternativo 3307:
mysql -P 3307 -u root lacuponera_sv < database/schema.sql
mysql -P 3307 -u root lacuponera_sv < database/seeds.sql
```

### Paso 4: Configurar la Conexión Local
El archivo de configuración principal se encuentra en `app/config/config.php`.  
Verifica que los parámetros de conexión coincidan con tu servidor local:

```php
'database' => [
    'host'     => '127.0.0.1',
    'port'     => 3307,          // Cambiar a 3306 si tu MySQL corre en el puerto predeterminado
    'dbname'   => 'lacuponera_sv',
    'username' => 'root',
    'password' => '',            // En XAMPP suele ser una contraseña vacía
    'charset'  => 'utf8mb4',
],
```

### Paso 5: Ubicar el Proyecto en el Servidor
Puedes ejecutar el proyecto de dos formas:

#### Opción A (Recomendada con XAMPP Apache):
Ubica la carpeta del proyecto dentro del directorio raíz de XAMPP:
```
C:\xampp\htdocs\TAREA3\
```

#### Opción B (Servidor Integrado de PHP para pruebas rápidas):
Desde la carpeta raíz del proyecto, ejecuta en la terminal:
```bash
php -S 127.0.0.1:8000 -t public
```

### Paso 6: Abrir la Aplicación en el Navegador
* Si utilizas XAMPP Apache:  
  [http://localhost/TAREA3/public/](http://localhost/TAREA3/public/) o [http://localhost/TAREA3/](http://localhost/TAREA3/)
* Si utilizas el servidor integrado de PHP:  
  [http://127.0.0.1:8000/](http://127.0.0.1:8000/)

---

## 4. Credenciales de Prueba para Evaluación Académica

Todas las cuentas demostrativas utilizan la misma contraseña segura para facilitar la revisión del evaluador:

| Rol | Tipo de Cuenta | Usuario / Identificador | Contraseña | Estado / Notas |
| :--- | :--- | :--- | :--- | :--- |
| **Cliente** | Persona Natural | `juanperez` | `Cuponera2025!` | Mayor de edad, perfil activo |
| **Empresa** | Comercio Aprobado | `pizzanostra` | `Cuponera2025!` | Aprobada para operar |
| **Empresa** | Comercio en Revisión | `cafebistro` | `Cuponera2025!` | Pendiente de aprobación administrativa |
| **Administrador** | Administrador General | `admin_demo` | `Cuponera2025!` | Acceso al esqueleto administrativo |

> **Tip:** En la pantalla de inicio de sesión (`/login`) encontrarás botones interactivos para autocompletar estas credenciales con un solo clic.

---

## 5. Ejecución de la Suite de Pruebas Automatizadas

El proyecto incluye una suite de pruebas con **49 aserciones** que verifican modelos, conexión PDO, lógica de edad, bisiestos, DUI, políticas de contraseña y aislamiento de roles:

```bash
php tests/test_persona1.php
```

Resultado esperado:
```
====================================================================
 RESUMEN FINAL DE PRUEBAS DEL MÓDULO PERSONA 1:
 Total ejecutadas: 49
 Aprobadas:       49
 Fallidas:        0
====================================================================
 >>> TODAS LAS PRUEBAS SE COMPLETARON SATISFACTORIAMENTE (100% OK) <<<
```

---

## 6. Resolución de Errores Habituales de Configuración

1. **Error: `SQLSTATE[HY000] [2002] No connection could be made...`:**
   - **Causa:** El puerto de MySQL configurado en `app/config/config.php` no coincide con el puerto activo de tu servidor.
   - **Solución:** Revisa si en XAMPP MySQL está escuchando en el puerto `3306` o `3307` y ajusta el campo `'port'` en el archivo de configuración.

2. **Error: `Access denied for user 'root'@'localhost'`:**
   - **Causa:** Tu usuario `root` tiene asignada una contraseña en MySQL.
   - **Solución:** Escribe tu contraseña local en `'password'` dentro de `app/config/config.php`.

3. **Las rutas no cargan o devuelven 404 en Apache:**
   - **Causa:** El módulo `mod_rewrite` de Apache no está habilitado en `httpd.conf` o el archivo `.htaccess` no se está leyendo (`AllowOverride None`).
   - **Solución:** Activa `LoadModule rewrite_module modules/mod_rewrite.so` y asegúrate de configurar `AllowOverride All` en tu `httpd.conf` de Apache, o bien utiliza la URL con fallback: `http://localhost/TAREA3/public/?route=login`.

---

## 7. Documentación Complementaria

* **Arquitectura de Software y Seguridad:** [docs/arquitectura.md](docs/arquitectura.md)
* **Diccionario de Base de Datos y Diagrama ER:** [docs/base_datos.md](docs/base_datos.md)
* **Guía de Integración para Compañeros:** [docs/integracion_companeros.md](docs/integracion_companeros.md)
* **Guía para la Defensa Académica y Preguntas del Docente:** [docs/defensa_academica.md](docs/defensa_academica.md)
