(function (window, document) {
    'use strict';
    window.validarTarefa = function () {
        var titulo = document.getElementById('titulo');
        var data = document.getElementById('data');
        var faltantes = [];
        [titulo, data].forEach(function (campo) {
            var vazio = !campo.value.trim();
            campo.setAttribute('aria-invalid', vazio ? 'true' : 'false');
            campo.style.borderColor = vazio ? '#dd4b39' : '';
            if (vazio) faltantes.push(campo);
        });
        if (faltantes.length) {
            SobAlertas.mostrar({tipo: 'warning', mensagem: 'Preencha os campos obrigatórios: ' + faltantes.map(function (campo) {
                return campo.id === 'titulo' ? 'Título' : 'Data';
            }).join(' e ') + '.'});
            faltantes[0].focus();
            return false;
        }
        var partes = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(data.value.trim());
        var valida = false;
        if (partes) {
            var dia = Number(partes[1]), mes = Number(partes[2]), ano = Number(partes[3]);
            var teste = new Date(0);
            teste.setFullYear(ano, mes - 1, dia);
            valida = teste.getFullYear() === ano && teste.getMonth() === mes - 1 && teste.getDate() === dia;
        }
        if (!valida) {
            data.setAttribute('aria-invalid', 'true');
            data.style.borderColor = '#dd4b39';
            SobAlertas.mostrar({tipo: 'warning', mensagem: 'Informe uma data válida no formato dia/mês/ano.'});
            data.focus();
            return false;
        }
        return true;
    };
})(window, document);
