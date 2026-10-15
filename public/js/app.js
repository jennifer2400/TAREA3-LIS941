/**
 * ============================================================================
 * la cuponera sv - javascript global
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. alternador para mostrar / ocultar contraseñas
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (!targetInput) return;

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            } else {
                targetInput.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });

    // 2. cargador de credenciales de prueba para el evaluador docente
    const demoButtons = document.querySelectorAll('.btn-fill-demo');
    demoButtons.forEach(button => {
        button.addEventListener('click', function () {
            const user = this.getAttribute('data-user');
            const pass = this.getAttribute('data-pass');

            const identifierInput = document.getElementById('identifier');
            const passwordInput = document.getElementById('password');

            if (identifierInput && passwordInput) {
                identifierInput.value = user;
                passwordInput.value = pass;
                identifierInput.classList.add('is-valid');
                passwordInput.classList.add('is-valid');
            }
        });
    });

    // 3. auto-cierre de alertas informativas después de 6 segundos
    const autoAlerts = document.querySelectorAll('.alert-dismissible');
    autoAlerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 8000);
    });
});
