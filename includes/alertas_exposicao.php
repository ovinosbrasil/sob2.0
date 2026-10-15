<?php
$alertaExposicao = $_SESSION['alerta_exposicao'] ?? null;
$camposExposicao = $_SESSION['campos_exposicao'] ?? array();
unset($_SESSION['alerta_exposicao'], $_SESSION['campos_exposicao']);
if (in_array($pg, array('cadastrar_exposicao', 'pesquisar_exposicao', 'exposicao', 'premiacao', 'venda_exposicao', 'vender_animal_exposicao', 'julgamento', 'relatorio_vendas_exposicao'), true) || $alertaExposicao): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<script>
(function () {
    'use strict';
    function formularioDoCampo(campo) {
        return campo && campo.form && campo.form.hasAttribute('data-alertas-exposicao') ? campo.form : null;
    }
    var aguardando = false;
    document.addEventListener('invalid', function (evento) {
        var formulario = formularioDoCampo(evento.target);
        if (!formulario) return;
        evento.preventDefault();
        if (aguardando) return;
        aguardando = true;
        var campos = Array.prototype.filter.call(formulario.elements, function (campo) {
            return campo.willValidate && !campo.validity.valid;
        });
        var nomes = campos.map(function (campo) {
            var label = campo.labels && campo.labels[0];
            return label ? label.textContent.replace(/\*/g, '').trim() : campo.name;
        });
        SobAlertas.camposObrigatorios(nomes);
        if (campos[0]) campos[0].focus();
        window.setTimeout(function () { aguardando = false; }, 0);
    }, true);
    document.addEventListener('submit', function (evento) {
        var formulario = evento.target;
        if (!formulario.hasAttribute('data-alertas-exposicao')) return;
        var campo = formulario.elements.data;
        if (campo) {
            var partes = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(campo.value);
            var data = partes ? new Date(Number(partes[3]), Number(partes[2]) - 1, Number(partes[1])) : null;
            if (!data || data.getFullYear() !== Number(partes[3]) || data.getMonth() !== Number(partes[2]) - 1 || data.getDate() !== Number(partes[1])) {
                evento.preventDefault();
                SobAlertas.mostrar({tipo: 'warning', titulo: 'Atenção!', mensagem: 'Informe uma data válida no formato dd/mm/aaaa.'});
                campo.focus();
            }
        }
    });
}());
</script>
<?php if ($alertaExposicao): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaExposicao, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
