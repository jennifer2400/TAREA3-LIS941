<?php
/**
 * ============================================================================
 * la cuponera sv - helper de validación de datos
 * ============================================================================
 * centraliza las reglas de validación de negocio y formatos para formularios.
 */

class Validator
{
    /**
     * valida formato de correo electrónico
     */
    public static function validateEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * valida nombre de usuario
     * regla: entre 4 y 30 caracteres alfanuméricos y guiones bajos
     */
    public static function validateUsername(?string $username): bool
    {
        if (empty($username)) {
            return false;
        }
        return (bool) preg_match('/^[a-zA-Z0-9_]{4,30}$/', trim($username));
    }

    /**
     * valida nombres y apellidos
     * regla: de 2 a 80 caracteres alfabéticos, tildes, espacios y guiones
     */
    public static function validateName(?string $name): bool
    {
        if (empty($name)) {
            return false;
        }
        return (bool) preg_match('/^[\p{L}\s\'\-]{2,80}$/u', trim($name));
    }

    /**
     * valida formato de dui (documento único de identidad de el salvador)
     * regla: 8 dígitos seguidos de un guión y 1 dígito de control (total 10 caracteres).
     * expresión regular: /^\d{8}-\d$/
     * ejemplo válido: 01234567-8
     */
    public static function validateDUI(?string $dui): bool
    {
        if (empty($dui)) {
            return false;
        }
        return (bool) preg_match('/^\d{8}-\d$/', trim($dui));
    }

    /**
     * valida mayoría de edad (mínimo 18 años cumplidos a la fecha actual)
     * toma en cuenta años bisiestos y cálculo exacto de días transcurridos.
     * 
     * @param string|null $birthdate cadena en formato yyyy-mm-dd
     * @param int $minage edad mínima requerida (por defecto 18)
     * @return array [ 'valid' => bool, 'age' => int, 'error' => ?string ]
     */
    public static function validateAge(?string $birthdate, int $minAge = 18): array
    {
        if (empty($birthdate)) {
            return [
                'valid' => false,
                'age'   => 0,
                'error' => 'La fecha de nacimiento es obligatoria.'
            ];
        }

        // validar formato estricto yyyy-mm-dd
        $parts = explode('-', trim($birthdate));
        if (count($parts) !== 3) {
            return [
                'valid' => false,
                'age'   => 0,
                'error' => 'Formato de fecha inválido. Utilice el formato AAAA-MM-DD.'
            ];
        }

        $year = (int)$parts[0];
        $month = (int)$parts[1];
        $day = (int)$parts[2];

        // checkdate valida meses de 28, 29 (bisiestos), 30 y 31 días
        if (!checkdate($month, $day, $year)) {
            return [
                'valid' => false,
                'age'   => 0,
                'error' => 'La fecha ingresada no corresponde a un día de calendario válido.'
            ];
        }

        try {
            $dob = new DateTime(sprintf('%04d-%02d-%02d', $year, $month, $day));
            $dob->setTime(0, 0, 0);

            $today = new DateTime('today');
            $today->setTime(0, 0, 0);

            if ($dob > $today) {
                return [
                    'valid' => false,
                    'age'   => 0,
                    'error' => 'La fecha de nacimiento no puede ser una fecha futura.'
                ];
            }

            // diff calcula años, meses y días exactos contemplando bisiestos
            $interval = $today->diff($dob);
            $age = $interval->y;

            if ($age < $minAge) {
                return [
                    'valid' => false,
                    'age'   => $age,
                    'error' => sprintf(
                        'Debe tener al menos %d años cumplidos para registrarse. Su edad calculada es de %d año(s).',
                        $minAge,
                        $age
                    )
                ];
            }

            return [
                'valid' => true,
                'age'   => $age,
                'error' => null
            ];
        } catch (Exception $e) {
            return [
                'valid' => false,
                'age'   => 0,
                'error' => 'Error al procesar la fecha de nacimiento.'
            ];
        }
    }

    /**
     * valida la política de contraseñas
     * reglas:
     * - longitud mínima de 8 caracteres
     * - al menos una letra mayúscula (a-z)
     * - al menos una letra minúscula (a-z)
     * - al menos un dígito numérico (0-9)
     * - al menos un carácter especial (!@#$%^&*()_+-=[]{}|;:,.<>?)
     * 
     * @return array [ 'valid' => bool, 'errors' => string[] ]
     */
    public static function validatePassword(?string $password): array
    {
        $errors = [];

        if (empty($password)) {
            return [
                'valid'  => false,
                'errors' => ['La contraseña es obligatoria.']
            ];
        }

        if (strlen($password) < 8) {
            $errors[] = 'Debe tener al menos 8 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Debe incluir al menos una letra mayúscula (A-Z).';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Debe incluir al menos una letra minúscula (a-z).';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Debe incluir al menos un número (0-9).';
        }

        if (!preg_match('/[\W_]/', $password)) {
            $errors[] = 'Debe incluir al menos un carácter especial (ej. !@#$%&*?-).';
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * limpia cadenas de texto de caracteres nulos y espacios extremos
     */
    public static function sanitize(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return trim(str_replace("\0", '', $value));
    }
}
