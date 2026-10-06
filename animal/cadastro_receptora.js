(function (window) {
    'use strict';
    window.validarCadastroReceptora = function (formulario) {
        var nome = formulario.elements.nome;
        var valor = nome.value.trim();
        var mensagem = '';
        if (!valor) {
            SobAlertas.camposObrigatorios(['Nome']);
        } else if (Array.from(valor).length > 50) {
            mensagem = 'O nome deve conter até 50 caracteres válidos.';
            SobAlertas.mostrar({tipo: 'warning', mensagem: mensagem});
        } else {
            nome.setAttribute('aria-invalid', 'false');
            nome.style.borderColor = '';
            return true;
        }
        nome.setAttribute('aria-invalid', 'true');
        nome.style.borderColor = '#dd4b39';
        nome.focus();
        return false;
    };
})(window);
