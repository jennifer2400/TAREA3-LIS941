<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Empresa.php';

class RegistroEmpresaController {
    public function formulario(): void {
        Auth::requireGuest();
        $rubros = Database::getConnection()->query('SELECT id,nombre FROM rubros ORDER BY nombre')->fetchAll();
        $old = $_SESSION['empresa_old'] ?? [];
        $errors = $_SESSION['empresa_errors'] ?? [];
        unset($_SESSION['empresa_old'], $_SESSION['empresa_errors']);
        require __DIR__ . '/../views/auth/registro_empresa.php';
    }

    public function exito(): void {
        Auth::requireGuest();
        require __DIR__ . '/../views/auth/empresa_registro_exito.php';
    }

    public function guardar(): void {
        Auth::requireGuest();
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Flash::error('Sesión de seguridad inválida.');
            header('Location: '.url('/registro-empresa')); exit;
        }
        $fields = ['usuario','correo','nombre_comercial','razon_social','nit','telefono','correo_contacto','direccion','id_rubro'];
        $data = [];
        foreach ($fields as $field) $data[$field] = trim((string)($_POST[$field] ?? ''));
        $_SESSION['empresa_old'] = $data;
        $errors = [];
        if (!Validator::validateUsername($data['usuario'])) $errors[] = 'Usuario: 4 a 30 letras, números o guiones bajos.';
        if (!Validator::validateEmail($data['correo']) || !Validator::validateEmail($data['correo_contacto'])) $errors[] = 'Ingresa correos válidos.';
        if (strlen($data['nombre_comercial']) < 2 || strlen($data['nombre_comercial']) > 120) $errors[] = 'Nombre comercial: entre 2 y 120 caracteres.';
        if (strlen($data['razon_social']) < 2 || strlen($data['razon_social']) > 150) $errors[] = 'Razón social: entre 2 y 150 caracteres.';
        if (!preg_match('/^[0-9-]{9,20}$/', $data['nit'])) $errors[] = 'NIT inválido: utiliza entre 9 y 20 caracteres numéricos o guiones.';
        if (!preg_match('/^[0-9+() -]{8,20}$/', $data['telefono'])) $errors[] = 'Número telefónico inválido.';
        if (strlen($data['direccion']) < 8) $errors[] = 'Ingresa la dirección de la empresa.';
        $password = (string)($_POST['password'] ?? '');
        $check = Validator::validatePassword($password);
        if (!$check['valid']) $errors = array_merge($errors, $check['errors']);
        if ($password !== ($_POST['password_confirm'] ?? '')) $errors[] = 'Las contraseñas no coinciden.';
        if (empty($_POST['terminos'])) $errors[] = 'Debes aceptar los términos.';
        $db = Database::getConnection();
        if ($data['id_rubro'] !== '') {
            if (!ctype_digit($data['id_rubro'])) $errors[] = 'Rubro inválido.';
            else {
                $st=$db->prepare('SELECT COUNT(*) FROM rubros WHERE id=?');$st->execute([(int)$data['id_rubro']]);
                if (!(int)$st->fetchColumn()) $errors[] = 'El rubro seleccionado no existe.';
            }
        }
        if (Usuario::existsByUsername($data['usuario'])) $errors[]='Este usuario ya está registrado.';
        if (Usuario::existsByEmail($data['correo'])) $errors[]='El correo de acceso ya está registrado.';
        if (Empresa::existsByNit($data['nit'])) $errors[]='Este NIT ya está registrado.';
        if ($errors) {$_SESSION['empresa_errors']=$errors;header('Location: '.url('/registro-empresa'));exit;}
        try {
            $db->beginTransaction();
            $role=$db->query("SELECT id FROM roles WHERE nombre='empresa' LIMIT 1")->fetchColumn();
            if (!$role) throw new RuntimeException('Falta el rol empresa. Importa seeds.sql.');
            $id=Usuario::create(['id_rol'=>(int)$role,'usuario'=>$data['usuario'],'correo'=>$data['correo'], 'password_hash'=>password_hash($password,PASSWORD_DEFAULT),'estado'=>'activo'],$db);
            $st=$db->prepare("INSERT INTO empresas (id_usuario,id_rubro,nombre_comercial,razon_social,nit,telefono,correo_contacto,direccion,estado_aprobacion) VALUES (?,?,?,?,?,?,?,?,'pendiente')");
            $st->execute([$id, $data['id_rubro']!==''?(int)$data['id_rubro']:null,$data['nombre_comercial'],$data['razon_social'],$data['nit'],$data['telefono'],$data['correo_contacto'],$data['direccion']]);
            $db->commit();
            unset($_SESSION['empresa_old']);
            Flash::success('Solicitud enviada exitosamente. La empresa está pendiente de aprobación por un administrador.');
            header('Location: '.url('/registro-empresa/exito'));exit;
        } catch(Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('Registro de empresa: '.$e->getMessage());
            $_SESSION['empresa_errors']=['No fue posible registrar la empresa. Comprueba los datos y que no estén duplicados.'];
            header('Location: '.url('/registro-empresa'));exit;
        }
    }
}
