<?php
/**
 * ============================================================================
 * la cuponera sv - suite de pruebas automatizadas del módulo persona 1
 * ============================================================================
 * ejecuta pruebas unitarias y de integración sobre la base de datos real,
 * modelos, helpers, validadores y mecanismos de seguridad.
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/helpers.php';
require_once __DIR__ . '/../app/helpers/Flash.php';
require_once __DIR__ . '/../app/helpers/Csrf.php';
require_once __DIR__ . '/../app/helpers/Validator.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Cliente.php';
require_once __DIR__ . '/../app/models/Empresa.php';
require_once __DIR__ . '/../app/models/PasswordReset.php';

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function assertTest(string $description, bool $condition, string $details = ''): void {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo " [PASS] $description\n";
    } else {
        $failedTests++;
        echo " [FAIL] $description\n";
        if ($details) {
            echo "        Detalle: $details\n";
        }
    }
}

echo "====================================================================\n";
echo " INICIANDO SUITE DE PRUEBAS — LA CUPONERA SV (PERSONA 1)\n";
echo " Entorno: PHP " . PHP_VERSION . " | " . date('Y-m-d H:i:s') . "\n";
echo "====================================================================\n\n";

// -----------------------------------------------------------------------------
// 1. conexión a base de datos
// -----------------------------------------------------------------------------
echo "--- 1. PRUEBAS DE CONEXIÓN A LA BASE DE DATOS ---\n";
try {
    $pdo = Database::getConnection();
    assertTest("Conexión PDO activa a MariaDB/MySQL", $pdo instanceof PDO);
    $tables = $pdo->query("SHOW TABLES LIKE 'usuarios'")->fetchAll();
    assertTest("Tabla 'usuarios' existe en base de datos", count($tables) > 0);
} catch (Exception $e) {
    assertTest("Conexión PDO fallida", false, $e->getMessage());
}

// -----------------------------------------------------------------------------
// 2. validación de mayoría de edad (18+)
// -----------------------------------------------------------------------------
echo "\n--- 2. PRUEBAS DE CÁLCULO DE EDAD Y MAYORÍA DE EDAD (18+) ---\n";
// adulto de 22 años (2004-05-10)
$adulto = Validator::validateAge('2004-05-10', 18);
assertTest("Acepta persona de 18+ años (Nacimiento 2004-05-10)", $adulto['valid'] && $adulto['age'] >= 18);

// nacido en bisiesto (2004-02-29)
$bisiesto = Validator::validateAge('2004-02-29', 18);
assertTest("Maneja correctamente años bisiestos (2004-02-29)", $bisiesto['valid'] && $bisiesto['age'] >= 18);

// menor de edad (16 años)
$menor = Validator::validateAge(date('Y-m-d', strtotime('-16 years')), 18);
assertTest("Rechaza persona menor de 18 años (-16 años)", !$menor['valid'] && $menor['age'] === 16);

// fecha futura
$futura = Validator::validateAge(date('Y-m-d', strtotime('+1 year')), 18);
assertTest("Rechaza fecha de nacimiento en el futuro", !$futura['valid']);

// fecha inválida de calendario (31 de febrero)
$fechaInvalida = Validator::validateAge('2000-02-31', 18);
assertTest("Rechaza fecha inexistente en calendario (2000-02-31)", !$fechaInvalida['valid']);

// -----------------------------------------------------------------------------
// 3. validación de formato de dui (el salvador)
// -----------------------------------------------------------------------------
echo "\n--- 3. PRUEBAS DE FORMATO DE DUI (00000000-0) ---\n";
assertTest("Acepta DUI con formato válido '01234567-8'", Validator::validateDUI('01234567-8'));
assertTest("Rechaza DUI sin guion '012345678'", !Validator::validateDUI('012345678'));
assertTest("Rechaza DUI con guion en posición incorrecta '01234-5678'", !Validator::validateDUI('01234-5678'));
assertTest("Rechaza DUI con letras '0123456A-8'", !Validator::validateDUI('0123456A-8'));
assertTest("Rechaza DUI vacío", !Validator::validateDUI(''));

// -----------------------------------------------------------------------------
// 4. política de contraseñas
// -----------------------------------------------------------------------------
echo "\n--- 4. PRUEBAS DE POLÍTICA DE SEGURIDAD EN CONTRASEÑAS ---\n";
$pwdFuerte = Validator::validatePassword('Cuponera2025!');
assertTest("Acepta contraseña que cumple toda la política ('Cuponera2025!')", $pwdFuerte['valid']);

$pwdCorta = Validator::validatePassword('Cup25!');
assertTest("Rechaza contraseña con menos de 8 caracteres", !$pwdCorta['valid']);

$pwdSinMayus = Validator::validatePassword('cuponera2025!');
assertTest("Rechaza contraseña sin letra mayúscula", !$pwdSinMayus['valid']);

$pwdSinNumero = Validator::validatePassword('CuponeraSV!');
assertTest("Rechaza contraseña sin número", !$pwdSinNumero['valid']);

$pwdSinEspecial = Validator::validatePassword('Cuponera2025');
assertTest("Rechaza contraseña sin carácter especial", !$pwdSinEspecial['valid']);

// -----------------------------------------------------------------------------
// 5. validación de correo y nombres
// -----------------------------------------------------------------------------
echo "\n--- 5. PRUEBAS DE FORMATO DE CORREO Y NOMBRE DE USUARIO ---\n";
assertTest("Acepta correo válido 'cliente@dominio.sv'", Validator::validateEmail('cliente@dominio.sv'));
assertTest("Rechaza correo con formato inválido 'cliente@dominio'", !Validator::validateEmail('cliente@dominio'));
assertTest("Acepta nombre de usuario alfanumérico 'carlos_sv'", Validator::validateUsername('carlos_sv'));
assertTest("Rechaza nombre de usuario con espacios 'carlos sv'", !Validator::validateUsername('carlos sv'));
assertTest("Rechaza nombre de usuario muy corto 'abc'", !Validator::validateUsername('abc'));

// -----------------------------------------------------------------------------
// 6. registro real en base de datos y comprobación de unicidad
// -----------------------------------------------------------------------------
echo "\n--- 6. PRUEBAS DE REGISTRO TRANSACCIONAL Y UNICIDAD ---\n";

// limpiar usuario de prueba previo si existe
$testUser = 'carlos_test_' . time();
$testMail = $testUser . '@prueba.sv';
$testDui = '09876543-2';

// limpiar posible dui previo de prueba
$pdo->prepare("DELETE FROM clientes WHERE dui = :dui")->execute([':dui' => $testDui]);
$pdo->prepare("DELETE FROM usuarios WHERE usuario LIKE 'carlos_test_%'")->execute();

$passwordHash = password_hash('Segura2025!', PASSWORD_DEFAULT);

$usuarioData = [
    'usuario'       => $testUser,
    'correo'        => $testMail,
    'password_hash' => $passwordHash,
];

$clienteData = [
    'nombres'          => 'Carlos Eduardo',
    'apellidos'        => 'Méndez Guardado',
    'dui'              => $testDui,
    'fecha_nacimiento' => '1998-08-14',
    'telefono'         => '7000-1122',
    'direccion'        => 'Colonia Médica, San Salvador',
];

$clienteId = Cliente::registrarCliente($usuarioData, $clienteData);
assertTest("Registro atómico de cliente de 18+ años en BD (ID: $clienteId)", $clienteId > 0);

// verificar almacenamiento seguro de contraseña
$usuarioEnBD = Usuario::findByIdentifier($testUser);
assertTest("Contraseña almacenada como hash seguro (empieza con '$2y$')", str_starts_with($usuarioEnBD['password_hash'], '$2y$'));
assertTest("La contraseña NO está en texto plano", $usuarioEnBD['password_hash'] !== 'Segura2025!');

// comprobación de unicidad
assertTest("Detecta duplicidad de nombre de usuario", Usuario::existsByUsername($testUser));
assertTest("Detecta duplicidad de correo electrónico", Usuario::existsByEmail($testMail));
assertTest("Detecta duplicidad de DUI", Cliente::existsByDui($testDui));

// -----------------------------------------------------------------------------
// 7. pruebas de inicio de sesión y verificación de credenciales
// -----------------------------------------------------------------------------
echo "\n--- 7. PRUEBAS DE AUTENTICACIÓN Y PASSWORD_VERIFY ---\n";

// login exitoso con credenciales válidas
$usuarioAuth = Usuario::findByIdentifier($testUser);
$passOk = password_verify('Segura2025!', $usuarioAuth['password_hash']);
assertTest("Autenticación exitosa con credenciales correctas", $passOk);

// login fallido con contraseña incorrecta
$passErr = password_verify('PasswordIncorrecta123!', $usuarioAuth['password_hash']);
assertTest("Rechazo de autenticación ante contraseña incorrecta", !$passErr);

// login con usuario inexistente
$inexistente = Usuario::findByIdentifier('usuario_fantasma_xyz');
assertTest("Rechazo ante usuario inexistente en base de datos", $inexistente === null);

// -----------------------------------------------------------------------------
// 8. autenticación unificada de empresas y control de aprobación
// -----------------------------------------------------------------------------
echo "\n--- 8. PRUEBAS DE AUTENTICACIÓN DE EMPRESAS Y ESTADOS ---\n";

// empresa aprobada
$empresaAprobada = Usuario::findByIdentifier('pizzanostra');
assertTest("Existe usuario de empresa 'pizzanostra'", $empresaAprobada !== null);
assertTest("Rol asignado es 'empresa'", ($empresaAprobada['rol_nombre'] ?? '') === 'empresa');

$datosEmpresa = Empresa::findByUserId((int)$empresaAprobada['id']);
assertTest("Perfil de empresa cargado correctamente", $datosEmpresa !== null);
assertTest("Estado de aprobación es 'aprobada'", ($datosEmpresa['estado_aprobacion'] ?? '') === 'aprobada');
assertTest("Helper isApproved() retorna true para empresa aprobada", Empresa::isApproved((int)$empresaAprobada['id']));

// empresa pendiente
$empresaPendiente = Usuario::findByIdentifier('cafebistro');
$datosPendiente = Empresa::findByUserId((int)$empresaPendiente['id']);
assertTest("Estado de aprobación de 'cafebistro' es 'pendiente'", ($datosPendiente['estado_aprobacion'] ?? '') === 'pendiente');
assertTest("Helper isApproved() retorna false para empresa pendiente", !Empresa::isApproved((int)$empresaPendiente['id']));

// -----------------------------------------------------------------------------
// 9. control de acceso, roles y manipulación
// -----------------------------------------------------------------------------
echo "\n--- 9. PRUEBAS DE CONTROL DE ACCESO Y SEPARACIÓN DE ROLES ---\n";
// simular sesión de cliente
$_SESSION = [
    'is_authenticated' => true,
    'user_id'          => 4,
    'usuario'          => 'juanperez',
    'correo'           => 'juan.perez@example.sv',
    'id_rol'           => 2,
    'rol'              => 'cliente',
    'nombre_mostrar'   => 'Juan Carlos Pérez Alvarado',
];

assertTest("Auth::check() reconoce sesión activa", Auth::check());
assertTest("Auth::isCliente() reconoce rol cliente", Auth::isCliente());
assertTest("Auth::isEmpresa() deniega acceso como empresa al cliente", !Auth::isEmpresa());
assertTest("Auth::isAdmin() deniega acceso administrativo al cliente", !Auth::isAdmin());

// simular cierre de sesión
Auth::logout();
assertTest("Auth::logout() limpia completamente la sesión", !Auth::check());

// -----------------------------------------------------------------------------
// 10. seguridad csrf y tokens de recuperación
// -----------------------------------------------------------------------------
echo "\n--- 10. PRUEBAS DE SEGURIDAD CSRF Y RECUPERACIÓN DE CONTRASEÑA ---\n";
$csrfToken = Csrf::getToken();
assertTest("Genera token CSRF criptográfico de 64 caracteres", strlen($csrfToken) === 64);
assertTest("Valida token CSRF legítimo con hash_equals", Csrf::validate($csrfToken));
assertTest("Rechaza token CSRF manipulado", !Csrf::validate('token_falso_invalido_123'));

// esqueleto de recuperación de contraseña
$rawResetToken = PasswordReset::createResetToken((int)$usuarioEnBD['id'], 3600);
assertTest("Genera token de recuperación seguro", strlen($rawResetToken) === 64);

$tokenVerificado = PasswordReset::verifyToken($rawResetToken);
assertTest("Verifica token de recuperación activo en base de datos", $tokenVerificado !== null);

// consumir el token
PasswordReset::markAsUsed((int)$tokenVerificado['id']);
$tokenReusado = PasswordReset::verifyToken($rawResetToken);
assertTest("Token consumido queda invalidado para futuros intentos", $tokenReusado === null);

// limpieza final de registro de pruebas
$pdo->prepare("DELETE FROM clientes WHERE dui = :dui")->execute([':dui' => $testDui]);
$pdo->prepare("DELETE FROM usuarios WHERE usuario = :u")->execute([':u' => $testUser]);

// -----------------------------------------------------------------------------
// resumen general de resultados
// -----------------------------------------------------------------------------
echo "\n====================================================================\n";
echo " RESUMEN FINAL DE PRUEBAS DEL MÓDULO PERSONA 1:\n";
echo " Total ejecutadas: $totalTests\n";
echo " Aprobadas:       $passedTests\n";
echo " Fallidas:        $failedTests\n";
echo "====================================================================\n";

if ($failedTests === 0) {
    echo " >>> TODAS LAS PRUEBAS SE COMPLETARON SATISFACTORIAMENTE (100% OK) <<<\n\n";
    exit(0);
} else {
    echo " >>> ALGUNAS PRUEBAS FALLARON. REVISAR DETALLES ARRIBA <<<\n\n";
    exit(1);
}
