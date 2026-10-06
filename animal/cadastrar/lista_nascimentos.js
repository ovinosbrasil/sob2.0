document.addEventListener('DOMContentLoaded', function () {
    var lista = document.getElementById('lista-ultimos-nascimentos');
    if (!lista) return;
    var controlador;
    var versao = 0;
    async function carregar(pagina, limite) {
        var atual = ++versao;
        if (controlador) controlador.abort();
        controlador = new AbortController();
        lista.setAttribute('aria-busy', 'true');
        var erro = document.getElementById('erro-lista-nascimentos');
        erro.textContent = '';
        var parametros = new URLSearchParams({pagina_nascimentos: pagina, por_pagina_nascimentos: limite});
        try {
            var resposta = await fetch('animal/cadastrar/_buscar_nascimentos.php?' + parametros, {signal: controlador.signal});
            if (!resposta.ok || resposta.redirected) throw new Error('Lista indisponível');
            var html = await resposta.text();
            if (atual !== versao) return;
            lista.innerHTML = html;
        } catch (falha) {
            if (falha.name !== 'AbortError' && atual === versao) erro.textContent = 'Não foi possível atualizar a lista. Tente novamente.';
        } finally {
            if (atual === versao) lista.removeAttribute('aria-busy');
        }
    }
    lista.addEventListener('click', function (evento) {
        var link = evento.target.closest('[data-pagina-nascimentos]');
        if (!link) return;
        evento.preventDefault();
        carregar(link.getAttribute('data-pagina-nascimentos'), document.getElementById('por-pagina-nascimentos').value);
    });
    lista.addEventListener('change', function (evento) {
        if (evento.target.id === 'por-pagina-nascimentos') carregar(1, evento.target.value);
    });
});
