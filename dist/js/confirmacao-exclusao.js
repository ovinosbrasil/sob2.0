/* Confirmação compartilhada. A ação só é executada após o clique em Excluir. */
(function (window, $) {
    'use strict';
    var acao = null;
    var origem = null;
    var modal = $('#confirmacao-exclusao');
    var confirmar = document.getElementById('confirmacao-exclusao-prosseguir');
    var inativar = document.getElementById('confirmacao-exclusao-inativar');

    window.confirmarExclusao = function (opcoes) {
        if (!opcoes.bloqueado && typeof opcoes.aoConfirmar !== 'function') { return; }
        origem = document.activeElement;
        acao = opcoes.aoConfirmar;
        document.getElementById('confirmacao-exclusao-titulo').textContent = opcoes.titulo || 'Excluir registro?';
        document.getElementById('confirmacao-exclusao-descricao').textContent = opcoes.descricao || 'Confirme se deseja excluir este registro. Esta ação não pode ser desfeita.';
        document.getElementById('confirmacao-exclusao-nome').textContent = opcoes.nome || '';
        confirmar.disabled = !!opcoes.bloqueado;
        confirmar.textContent = opcoes.textoConfirmar || 'Excluir';
        inativar.style.display = typeof opcoes.aoInativar === 'function' ? '' : 'none';
        inativar.disabled = false;
        inativar.onclick = function () {
            inativar.disabled = true;
            confirmar.disabled = true;
            opcoes.aoInativar();
        };
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
        inativar.onclick = null;
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
        if (botao.getAttribute('data-origem') === 'Terceiros') {
            window.excluir_terceiro(botao.getAttribute('data-id'));
            return;
        }
        excluirAnimal(botao.getAttribute('data-id'), botao.getAttribute('data-nome'), botao.getAttribute('data-origem') === 'Terceiros');
    };
    window.excluir_terceiro = function (id) {
        if (!/^\d+$/.test(String(id)) || Number(id) < 1) { return; }
        $.getJSON('animal/palco_excluir_terceiro.php', {id_animal: id})
            .done(function (dados) {
                var resumo = function (vinculos) {
                    return vinculos.map(function (item) { return item.tipo + ': ' + item.quantidade; }).join('; ');
                };
                window.confirmarExclusao({
                    titulo: dados.pode_excluir ? 'Excluir animal de terceiros?' : 'Exclusão bloqueada',
                    nome: dados.nome,
                    descricao: dados.pode_excluir
                        ? 'Este cadastro não possui vínculos identificados. Confirme a exclusão definitiva. Esta ação não pode ser desfeita.'
                        : 'Este animal possui histórico: ' + resumo(dados.vinculos) + (dados.ativo ? '. Você pode inativar o cadastro para impedir novos usos e preservar o histórico.' : '. Este cadastro já está inativo e seu histórico está preservado.'),
                    bloqueado: !dados.pode_excluir,
                    aoInativar: !dados.pode_excluir && dados.ativo ? function () { alterarEstadoTerceiro(id, 'inativar', dados.csrf_token); } : null,
                    aoConfirmar: function () {
                        $.ajax({
                            url: 'animal/_excluir_terceiro.php',
                            method: 'POST',
                            dataType: 'json',
                            data: {id_animal: id, csrf_token: dados.csrf_token}
                        }).done(function () {
                            window.location.href = 'geral.php?pg=lista_terceiros';
                        }).fail(function (xhr) {
                            var resposta = xhr.responseJSON || {};
                            document.getElementById('confirmacao-exclusao-titulo').textContent = 'Cadastro preservado';
                            document.getElementById('confirmacao-exclusao-descricao').textContent =
                                (resposta.erro || 'Não foi possível concluir a exclusão. Atualize a página e tente novamente.') +
                                (resposta.vinculos ? ' ' + resumo(resposta.vinculos) : '');
                            if (resposta.vinculos && dados.ativo) {
                                inativar.style.display = '';
                                inativar.disabled = false;
                                inativar.onclick = function () { inativar.disabled = true; alterarEstadoTerceiro(id, 'inativar', dados.csrf_token); };
                            }
                        });
                    }
                });
            })
            .fail(function () { window.alert('Não foi possível carregar o animal. Atualize a página e tente novamente.'); });
    };
    function alterarEstadoTerceiro(id, acaoEstado, token) {
        $.ajax({url: 'animal/_estado_terceiro.php', method: 'POST', dataType: 'json',
            data: {id_animal: id, acao: acaoEstado, csrf_token: token}
        }).done(function () { window.location.reload(); }).fail(function (xhr) {
            document.getElementById('confirmacao-exclusao-descricao').textContent =
                (xhr.responseJSON || {}).erro || 'Não foi possível atualizar o cadastro. Atualize a página e tente novamente.';
        });
    }
    window.alternar_terceiro = function (botao) {
        if (botao.disabled) { return; }
        var id = botao.getAttribute('data-id');
        if (!/^\d+$/.test(String(id)) || Number(id) < 1) { return; }
        var acaoEstado = botao.getAttribute('aria-checked') === 'true' ? 'inativar' : 'ativar';
        botao.disabled = true;
        var falhou = function (xhr) {
            botao.disabled = false;
            window.alert((xhr.responseJSON || {}).erro || 'Não foi possível atualizar o cadastro. Tente novamente.');
        };
        $.getJSON('animal/palco_excluir_terceiro.php', {id_animal: id}).done(function (dados) {
            $.ajax({url: 'animal/_estado_terceiro.php', method: 'POST', dataType: 'json',
                data: {id_animal: id, acao: acaoEstado, csrf_token: dados.csrf_token}
            }).done(function () { window.location.reload(); }).fail(falhou);
        }).fail(falhou);
    };
    window.ativar_terceiro = function (id) {
        if (!/^\d+$/.test(String(id)) || Number(id) < 1) { return; }
        $.getJSON('animal/palco_excluir_terceiro.php', {id_animal: id}).done(function (dados) {
            window.confirmarExclusao({titulo: 'Ativar animal de terceiros?', nome: dados.nome,
                descricao: 'O animal voltará a aparecer nas pesquisas e poderá ser utilizado em novos vínculos.',
                textoConfirmar: 'Ativar', aoConfirmar: function () { alterarEstadoTerceiro(id, 'ativar', dados.csrf_token); }
            });
        }).fail(function () { window.alert('Não foi possível carregar o cadastro. Atualize a página.'); });
    };
})(window, jQuery);
