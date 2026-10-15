<?php
/**
 * ============================================================================
 * la cuponera sv - controlador de autenticación (authcontroller)
 * ============================================================================
 * módulo persona 1:
 * - inicio de sesión unificado (clientes y empresas)
 * - registro completo de clientes con validación estricta de mayoría de edad (18+)
 * - manejo de errores y preservación de datos no sensibles
 * - cierre de sesión y protección de rutas
 */

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Empresa.php';

class AuthController
{
    /**
     * muestra la pantalla de inicio de sesión
     */
    public function showLogin(): void
    {
        Auth::requireGuest();

        $oldIdentifier = $_SESSION['old_login_identifier'] ?? '';
        unset($_SESSION['old_login_identifier']);

        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * procesa la autenticación de credenciales (clientes y empresas)
     */
    public function login(): void
    {
        Auth::requireGuest();

        // 1. verificación del token csrf
        $token = $_POST['csrf_token'] ?? '';
        if (!Csrf::validate($token)) {
            Flash::error('La sesión de seguridad ha expirado. Por favor, intente iniciar sesión nuevamente.');
            header('Location: ' . url('/login'));
            exit;
        }

        // 2. extracción y saneamiento de entradas
        $identifier = Validator::sanitize($_POST['identifier'] ?? '');
        $password   = $_POST['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            Flash::error('Debe completar todos los campos del formulario.');
            $_SESSION['old_login_identifier'] = $identifier;
            header('Location: ' . url('/login'));
            exit;
        }

        // 3. consulta de usuario por nombre de usuario o correo
        $usuario = Usuario::findByIdentifier($identifier);

        // 4. verificación segura de contraseña mediante password_verify
        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            // mensaje genérico para evitar enumeración de usuarios existentes
            Flash::error('Credenciales incorrectas. Verifique su usuario o correo y contraseña.');
            $_SESSION['old_login_identifier'] = $identifier;
            header('Location: ' . url('/login'));
            exit;
        }

        // 5. comprobación del estado general de la cuenta
        if ($usuario['estado'] !== 'activo') {
            Flash::error(sprintf('La cuenta se encuentra en estado "%s". Contacte al administrador.', htmlspecialchars($usuario['estado'])));
            header('Location: ' . url('/login'));
            exit;
        }

        // 6. carga de datos específicos según el rol autenticado
        $perfil = [];
        $rolNombre = $usuario['rol_nombre'];

        if ($rolNombre === 'cliente') {
            $cliente = Cliente::findByUserId((int)$usuario['id']);
            $perfil = [
                'tipo'           => 'cliente',
                'nombre_mostrar' => $cliente ? ($cliente['nombres'] . ' ' . $cliente['apellidos']) : $usuario['usuario'],
                'datos'          => $cliente,
            ];
        } elseif ($rolNombre === 'empresa') {
            $empresa = Empresa::findByUserId((int)$usuario['id']);
            $perfil = [
                'tipo'           => 'empresa',
                'nombre_mostrar' => $empresa ? $empresa['nombre_comercial'] : $usuario['usuario'],
                'datos'          => $empresa,
                'aprobada'       => $empresa ? ($empresa['estado_aprobacion'] === 'aprobada') : false,
            ];
        } elseif ($rolNombre === 'administrador') {
            $perfil = [
                'tipo'           => 'administrador',
                'nombre_mostrar' => 'Administrador ' . $usuario['usuario'],
                'datos'          => [],
            ];
        }

        // 7. establecer sesión segura con regeneración de identificador
        Auth::login($usuario, $perfil);

        // 8. mensaje de bienvenida adaptado
        if ($rolNombre === 'empresa' && !empty($empresa) && $empresa['estado_aprobacion'] !== 'aprobada') {
            Flash::warning(sprintf(
                'Bienvenido(a), %s. Su empresa se encuentra en estado "%s". El acceso administrativo revisará su solicitud.',
                htmlspecialchars($perfil['nombre_mostrar']),
                strtoupper($empresa['estado_aprobacion'])
            ));
        } else {
            Flash::success(sprintf('¡Bienvenido(a) a La Cuponera SV, %s!', htmlspecialchars($perfil['nombre_mostrar'])));
        }

        // 9. redirección automática según el rol asignado
        Auth::redirectToDashboard();
    }

    /**
     * muestra el formulario de registro de clientes
     */
    public function showRegistro(): void
    {
        Auth::requireGuest();

        $old = $_SESSION['old_registro'] ?? [];
        $errors = $_SESSION['registro_errors'] ?? [];
        unset($_SESSION['old_registro'], $_SESSION['registro_errors']);

        require __DIR__ . '/../views/auth/registro.php';
    }

    /**
     * procesa el registro de un nuevo cliente individual
     */
    public function registro(): void
    {
        Auth::requireGuest();

        // 1. verificación de token csrf
        $token = $_POST['csrf_token'] ?? '';
        if (!Csrf::validate($token)) {
            Flash::error('La sesión de seguridad ha expirado. Por favor, envíe el formulario nuevamente.');
            header('Location: ' . url('/registro'));
            exit;
        }

        // 2. extracción y saneamiento de datos de entrada
        $usuario          = Validator::sanitize($_POST['usuario'] ?? '');
        $correo           = Validator::sanitize($_POST['correo'] ?? '');
        $password         = $_POST['password'] ?? '';
        $passwordConfirm  = $_POST['password_confirm'] ?? '';
        $nombres          = Validator::sanitize($_POST['nombres'] ?? '');
        $apellidos        = Validator::sanitize($_POST['apellidos'] ?? '');
        $dui              = Validator::sanitize($_POST['dui'] ?? '');
        $fechaNacimiento  = Validator::sanitize($_POST['fecha_nacimiento'] ?? '');
        $telefono         = Validator::sanitize($_POST['telefono'] ?? '');
        $direccion        = Validator::sanitize($_POST['direccion'] ?? '');
        $aceptaTerminos   = !empty($_POST['terminos']);

        // preservar datos no sensibles para rellenar formulario en caso de error
        // nunca se preservan las contraseñas
        $_SESSION['old_registro'] = [
            'usuario'          => $usuario,
            'correo'           => $correo,
            'nombres'          => $nombres,
            'apellidos'        => $apellidos,
            'dui'              => $dui,
            'fecha_nacimiento' => $fechaNacimiento,
            'telefono'         => $telefono,
            'direccion'        => $direccion,
        ];

        $errors = [];

        // 3. validaciones de campos obligatorios
        if (empty($usuario)) {
            $errors['usuario'] = 'El nombre de usuario es obligatorio.';
        } elseif (!Validator::validateUsername($usuario)) {
            $errors['usuario'] = 'El nombre de usuario debe tener entre 4 y 30 caracteres alfanuméricos o guiones bajos.';
        }

        if (empty($correo)) {
            $errors['correo'] = 'El correo electrónico es obligatorio.';
        } elseif (!Validator::validateEmail($correo)) {
            $errors['correo'] = 'El correo electrónico no tiene un formato válido.';
        }

        if (empty($nombres)) {
            $errors['nombres'] = 'Los nombres son obligatorios.';
        } elseif (!Validator::validateName($nombres)) {
            $errors['nombres'] = 'Los nombres solo deben contener letras y espacios válidos.';
        }

        if (empty($apellidos)) {
            $errors['apellidos'] = 'Los apellidos son obligatorios.';
        } elseif (!Validator::validateName($apellidos)) {
            $errors['apellidos'] = 'Los apellidos solo deben contener letras y espacios válidos.';
        }

        if (empty($dui)) {
            $errors['dui'] = 'El número de DUI es obligatorio.';
        } elseif (!Validator::validateDUI($dui)) {
            $errors['dui'] = 'El formato de DUI es inválido. Debe ser 8 dígitos, un guion y 1 dígito (Ejemplo: 01234567-8).';
        }

        // 4. validación de fecha de nacimiento y cálculo estricto de mayoría de edad (18+)
        if (empty($fechaNacimiento)) {
            $errors['fecha_nacimiento'] = 'La fecha de nacimiento es obligatoria.';
        } else {
            $ageValidation = Validator::validateAge($fechaNacimiento, 18);
            if (!$ageValidation['valid']) {
                $errors['fecha_nacimiento'] = $ageValidation['error'];
            }
        }

        // 5. validación de política de contraseñas
        $pwdValidation = Validator::validatePassword($password);
        if (!$pwdValidation['valid']) {
            $errors['password'] = implode(' ', $pwdValidation['errors']);
        } elseif ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Las contraseñas no coinciden.';
        }

        // 6. validación de aceptación de términos
        if (!$aceptaTerminos) {
            $errors['terminos'] = 'Debe aceptar los términos y condiciones del servicio para registrarse.';
        }

        // 7. validaciones de unicidad en base de datos (solo si el formato previo es válido)
        if (empty($errors['usuario']) && Usuario::existsByUsername($usuario)) {
            $errors['usuario'] = 'El nombre de usuario ya se encuentra registrado. Elija otro.';
        }

        if (empty($errors['correo']) && Usuario::existsByEmail($correo)) {
            $errors['correo'] = 'La dirección de correo electrónico ya se encuentra registrada en el sistema.';
        }

        if (empty($errors['dui']) && Cliente::existsByDui($dui)) {
            $errors['dui'] = 'El número de DUI ya está asociado a otra cuenta registrada.';
        }

        // si existen errores, regresar al formulario con los mensajes explicativos
        if (!empty($errors)) {
            $_SESSION['registro_errors'] = $errors;
            Flash::error('Por favor, revise los campos marcados y corrija los errores indicados.');
            header('Location: ' . url('/registro'));
            exit;
        }

        // 8. registro en base de datos mediante transacción atómica
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $usuarioData = [
                'usuario'       => $usuario,
                'correo'        => $correo,
                'password_hash' => $passwordHash,
            ];

            $clienteData = [
                'nombres'          => $nombres,
                'apellidos'        => $apellidos,
                'dui'              => $dui,
                'fecha_nacimiento' => $fechaNacimiento,
                'telefono'         => !empty($telefono) ? $telefono : null,
                'direccion'        => !empty($direccion) ? $direccion : null,
            ];

            Cliente::registrarCliente($usuarioData, $clienteData);

            // limpieza de datos temporales
            unset($_SESSION['old_registro'], $_SESSION['registro_errors']);

            // almacenar nombre para pantalla de confirmación
            $_SESSION['registro_exitoso_nombre'] = $nombres . ' ' . $apellidos;
            $_SESSION['registro_exitoso_usuario'] = $usuario;

            Flash::success('¡Registro completado exitosamente! Ya puedes iniciar sesión con tu nueva cuenta.');
            header('Location: ' . url('/registro/exitoso'));
            exit;
        } catch (Exception $e) {
            error_log('Error en registro de cliente: ' . $e->getMessage());
            Flash::error('Ocurrió un error inesperado al procesar su registro. Por favor, intente nuevamente.');
            header('Location: ' . url('/registro'));
            exit;
        }
    }

    /**
     * muestra la pantalla de confirmación tras un registro exitoso
     */
    public function showRegistroExitoso(): void
    {
        $nombreCompleto = $_SESSION['registro_exitoso_nombre'] ?? 'Nuevo Cliente';
        $usuario = $_SESSION['registro_exitoso_usuario'] ?? '';
        unset($_SESSION['registro_exitoso_nombre'], $_SESSION['registro_exitoso_usuario']);

        require __DIR__ . '/../views/auth/registro_exitoso.php';
    }

    /**
     * cierra la sesión activa de forma segura
     */
    public function logout(): void
    {
        Auth::logout();
        Flash::success('Has cerrado tu sesión de forma segura. ¡Hasta pronto!');
        header('Location: ' . url('/login'));
        exit;
    }
}
