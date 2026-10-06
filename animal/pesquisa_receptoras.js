document.addEventListener('DOMContentLoaded', function () {
    var campo = document.getElementById('busca_receptora');
    if (!campo) return;
    var formulario = campo.form;
    var temporizador;
    var controlador;
    var versao = 0;
    var aviso = document.createElement('div');
    aviso.className = 'text-danger';
    aviso.setAttribute('role', 'alert');
    formulario.parentNode.insertAdjacentElement('afterend', aviso);

    async function pesquisar() {
        var atual = ++versao;
        if (controlador) controlador.abort();
        controlador = new AbortController();
        var parametros = new URLSearchParams(new FormData(formulario));
        var url = new URL(formulario.action, window.location.href);
        url.search = parametros.toString();
        var resultados = document.getElementById('resultados-receptoras');
        resultados.setAttribute('aria-busy', 'true');
        aviso.textContent = '';
        try {
            var resposta = await fetch(url, { signal: controlador.signal });
            if (!resposta.ok) throw new Error('Pesquisa indisponível');
            var documento = new DOMParser().parseFromString(await resposta.text(), 'text/html');
            if (atual !== versao) return;
            var novosResultados = documento.getElementById('resultados-receptoras');
            var novosHistoricos = documento.getElementById('historicos-receptoras');
            if (!novosResultados || !novosHistoricos) throw new Error('Sessão indisponível');
            resultados.innerHTML = novosResultados.innerHTML;
            document.getElementById('historicos-receptoras').innerHTML = novosHistoricos.innerHTML;
            document.getElementById('pdf-receptoras').href = 'animal/_imprimir_receptoras.php?busca=' + encodeURIComponent(campo.value.trim());
            window.history.replaceState(null, '', url);
        } catch (erro) {
            if (erro.name !== 'AbortError' && atual === versao) {
                aviso.textContent = 'Não foi possível pesquisar. Tente novamente ou atualize a página.';
            }
        } finally {
            if (atual === versao) resultados.removeAttribute('aria-busy');
        }
    }

    campo.addEventListener('input', function (evento) {
        clearTimeout(temporizador);
        ++versao;
        if (controlador) controlador.abort();
        document.getElementById('pdf-receptoras').href = 'animal/_imprimir_receptoras.php?busca=' + encodeURIComponent(campo.value.trim());
        if (!evento.isComposing) temporizador = setTimeout(pesquisar, 300);
    });
    campo.addEventListener('compositionend', function () {
        clearTimeout(temporizador);
        temporizador = setTimeout(pesquisar, 300);
    });
    formulario.addEventListener('submit', function (evento) {
        evento.preventDefault();
        clearTimeout(temporizador);
        pesquisar();
    });
});
