/**
 * ============================================================================
 * la cuponera sv - validación reactiva de registro de clientes
 * ============================================================================
 * módulo persona 1:
 * - auto-formateo del dui (00000000-0)
 * - cálculo reactivo en vivo de mayoría de edad (18+)
 * - lista de verificación en tiempo real de política de contraseñas
 * - validación de coincidencia de contraseñas
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formRegistro');
    const duiInput = document.getElementById('dui');
    const birthdateInput = document.getElementById('fecha_nacimiento');
    const ageBadge = document.getElementById('ageFeedbackBadge');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirm');

    // -------------------------------------------------------------------------
    // 1. auto-formateo y validación de dui (8 dígitos + guion + 1 dígito)
    // -------------------------------------------------------------------------
    if (duiInput) {
        duiInput.addEventListener('input', function (e) {
            // extraer solo dígitos numéricos
            let raw = this.value.replace(/\D/g, '');

            if (raw.length > 9) {
                raw = raw.substring(0, 9);
            }

            // aplicar formato con guión
            if (raw.length > 8) {
                this.value = raw.substring(0, 8) + '-' + raw.substring(8);
            } else {
                this.value = raw;
            }

            // validar patrón final
            const regexDui = /^\d{8}-\d$/;
            if (regexDui.test(this.value)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value.length === 10) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }

    // -------------------------------------------------------------------------
    // 2. cálculo en tiempo real de mayoría de edad (18+ años)
    // -------------------------------------------------------------------------
    function calculateAge(birthDateString) {
        if (!birthDateString) return null;

        const parts = birthDateString.split('-');
        if (parts.length !== 3) return null;

        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1; // 0-indexado
        const day = parseInt(parts[2], 10);

        const birthDate = new Date(year, month, day);
        const today = new Date();

        // si la fecha es futura
        if (birthDate > today) {
            return { valid: false, future: true, age: 0 };
        }

        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();

        // si aún no ha llegado al mes de cumpleaños o al día del mes
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return {
            valid: age >= 18,
            future: false,
            age: age
        };
    }

    function updateAgeFeedback() {
        if (!birthdateInput || !ageBadge) return;

        const val = birthdateInput.value;
        if (!val) {
            ageBadge.innerHTML = '';
            birthdateInput.classList.remove('is-valid', 'is-invalid');
            return;
        }

        const res = calculateAge(val);

        if (!res) {
            ageBadge.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i> Formato de fecha no reconocido.</span>';
            birthdateInput.classList.add('is-invalid');
            birthdateInput.classList.remove('is-valid');
            return;
        }

        if (res.future) {
            ageBadge.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i> La fecha de nacimiento no puede ser en el futuro.</span>';
            birthdateInput.classList.add('is-invalid');
            birthdateInput.classList.remove('is-valid');
            return;
        }

        if (res.valid) {
            ageBadge.innerHTML = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> Edad calculada: <strong>${res.age} años</strong> (Mayor de edad ✓)</span>`;
            birthdateInput.classList.remove('is-invalid');
            birthdateInput.classList.add('is-valid');
        } else {
            ageBadge.innerHTML = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i> Edad calculada: <strong>${res.age} año(s)</strong> (Menor de edad ✗ - Debe tener 18+)</span>`;
            birthdateInput.classList.add('is-invalid');
            birthdateInput.classList.remove('is-valid');
        }
    }

    if (birthdateInput) {
        birthdateInput.addEventListener('change', updateAgeFeedback);
        birthdateInput.addEventListener('input', updateAgeFeedback);
        // verificar si ya traía un valor previo
        if (birthdateInput.value) {
            updateAgeFeedback();
        }
    }

    // -------------------------------------------------------------------------
    // 3. checklist reactivo de política de contraseña
    // -------------------------------------------------------------------------
    const ruleLength = document.getElementById('rule-length');
    const ruleUpper = document.getElementById('rule-upper');
    const ruleLower = document.getElementById('rule-lower');
    const ruleNumber = document.getElementById('rule-number');
    const ruleSpecial = document.getElementById('rule-special');

    function updateRule(elem, passed) {
        if (!elem) return;
        const icon = elem.querySelector('i');
        if (passed) {
            elem.classList.add('rule-passed');
            elem.classList.remove('rule-failed');
            if (icon) {
                icon.className = 'bi bi-check-circle-fill me-1';
            }
        } else {
            elem.classList.remove('rule-passed');
            elem.classList.add('rule-failed');
            if (icon) {
                icon.className = 'bi bi-circle me-1';
            }
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function () {
            const val = this.value;

            const hasLength = val.length >= 8;
            const hasUpper = /[A-Z]/.test(val);
            const hasLower = /[a-z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            const hasSpecial = /[\W_]/.test(val);

            updateRule(ruleLength, hasLength);
            updateRule(ruleUpper, hasUpper);
            updateRule(ruleLower, hasLower);
            updateRule(ruleNumber, hasNumber);
            updateRule(ruleSpecial, hasSpecial);

            if (hasLength && hasUpper && hasLower && hasNumber && hasSpecial) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (val.length > 0) {
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }

            checkPasswordMatch();
        });
    }

    // -------------------------------------------------------------------------
    // 4. coincidencia de confirmación de contraseña
    // -------------------------------------------------------------------------
    function checkPasswordMatch() {
        if (!confirmInput || !passwordInput) return;
        const p1 = passwordInput.value;
        const p2 = confirmInput.value;

        if (!p2) {
            confirmInput.classList.remove('is-valid', 'is-invalid');
            return;
        }

        if (p1 === p2 && p1.length > 0) {
            confirmInput.classList.remove('is-invalid');
            confirmInput.classList.add('is-valid');
        } else {
            confirmInput.classList.remove('is-valid');
            confirmInput.classList.add('is-invalid');
        }
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', checkPasswordMatch);
    }
});
