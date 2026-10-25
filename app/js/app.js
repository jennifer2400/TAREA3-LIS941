'use strict';

// Interacciones del formulario de inicio de sesión.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            var targetId = button.getAttribute('data-target');
            var input = targetId ? document.getElementById(targetId) : null;
            if (!input) return;

            var visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            button.setAttribute('title', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            var icon = button.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', !visible);
                icon.classList.toggle('bi-eye-slash', visible);
            }
        });
    });

    document.querySelectorAll('.btn-fill-demo').forEach(function (button) {
        button.addEventListener('click', function () {
            var form = document.getElementById('formLogin');
            if (!form) return;
            var userInput = form.querySelector('#identifier');
            var passInput = form.querySelector('#password');
            if (!userInput || !passInput) return;

            userInput.value = button.getAttribute('data-user') || '';
            passInput.value = button.getAttribute('data-pass') || '';
            userInput.dispatchEvent(new Event('input', { bubbles: true }));
            passInput.dispatchEvent(new Event('input', { bubbles: true }));
            passInput.focus();
        });
    });
});
