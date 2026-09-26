/* Confirmação compartilhada. A ação só é executada após o clique em Excluir. */
(function (window, $) {
    'use strict';
    var acao = null;
    var origem = null;
    var modal = $('#confirmacao-exclusao');
    var confirmar = document.getElementById('confirmacao-exclusao-prosseguir');

    window.confirmarExclusao = function (opcoes) {
        if (typeof opcoes.aoConfirmar !== 'function') { return; }
        origem = document.activeElement;
        acao = opcoes.aoConfirmar;
        document.getElementById('confirmacao-exclusao-titulo').textContent = opcoes.titulo || 'Excluir registro?';
        document.getElementById('confirmacao-exclusao-descricao').textContent = opcoes.descricao || 'Confirme se deseja excluir este registro. Esta ação não pode ser desfeita.';
        document.getElementById('confirmacao-exclusao-nome').textContent = opcoes.nome || '';
        confirmar.disabled = false;
        modal.modal('show');
    };
    confirmar.onclick = function () {
        if (!acao || confirmar.disabled) { return; }
        confirmar.disabled = true;
        var executar = acao;
        acao = null;
        executar();
    };
    modal.on('shown.bs.modal', function () {
        document.getElementById('confirmacao-exclusao-cancelar').focus();
    }).on('hidden.bs.modal', function () {
        acao = null;
        if (origem && document.documentElement.contains(origem)) { origem.focus(); }
    });

    function excluirAnimal(id, nome, terceiro) {
        if (!/^\d+$/.test(String(id)) || Number(id) < 1) { return; }
        window.confirmarExclusao({
            titulo: terceiro ? 'Excluir animal de terceiros?' : 'Excluir animal?',
            nome: nome,
            descricao: 'Confirme se deseja excluir o cadastro deste animal. Esta ação não pode ser desfeita e todas as referências desse animal serão removidas.',
            aoConfirmar: function () {
                window.location.href = (terceiro ? 'animal/_excluir_terceiro.php' : 'animal/_excluir_animal.php') + '?id_animal=' + encodeURIComponent(id);
            }
        });
    }
    window.confirmarExclusaoRebanho = function (botao) {
        excluirAnimal(botao.getAttribute('data-id'), botao.getAttribute('data-nome'), botao.getAttribute('data-origem') === 'Terceiros');
    };
    window.excluir_terceiro = function (id) {
        if (!/^\d+$/.test(String(id)) || Number(id) < 1) { return; }
        $.getJSON('animal/palco_excluir_terceiro.php', {id_animal: id})
            .done(function (dados) { excluirAnimal(id, dados.nome, true); })
            .fail(function () { window.alert('Não foi possível carregar o animal. Atualize a página e tente novamente.'); });
    };
})(window, jQuery);
