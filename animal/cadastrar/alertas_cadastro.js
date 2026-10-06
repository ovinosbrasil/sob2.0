(function () {
    'use strict';
    var nomes = {nome_animal: 'Nome', tatuagem: 'Tatuagem', sexo: 'Sexo', raca: 'Raça', data_nascimento: 'Data de nascimento', data_entrada: 'Data de entrada no rebanho', pai: 'Pai', mae: 'Mãe', valor: 'Preço de compra', peso: 'Peso', status: 'Situação'};
    function validar(ids) {
        var faltantes = [], primeiro;
        ids.forEach(function (id) {
            var campo = document.getElementById(id);
            if (!campo) return;
            var vazio = !campo.value.trim();
            campo.style.borderColor = vazio ? '#dd4b39' : '';
            campo.setAttribute('aria-invalid', String(vazio));
            if (vazio) { faltantes.push(nomes[id]); primeiro = primeiro || campo; }
        });
        if (faltantes.length) { SobAlertas.camposObrigatorios(faltantes); primeiro.focus(); return false; }
        return true;
    }
    var comuns = ['nome_animal', 'tatuagem', 'sexo', 'raca', 'data_nascimento', 'pai', 'mae'];
    window.ativar_compra = function () { return validar(comuns.concat(['data_entrada', 'valor'])); };
    window.ativar_rebanho = function () { return validar(comuns); };
    window.ativar_terceiros = function () { return validar(['nome_animal', 'sexo', 'raca']); };
    window.ativar_nascimento = function () { return validar(comuns.concat(['peso', 'status'])); };
    document.querySelectorAll('form[onsubmit*="ativar_"]').forEach(function (formulario) { formulario.noValidate = true; });
})();
