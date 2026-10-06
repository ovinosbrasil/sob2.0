(function (window, document) {
    'use strict';
    var tipos = {
        success: {titulo: 'Sucesso!', desenho: '<circle cx="18" cy="18" r="17" fill="white"/><path d="m10 18 5 5 11-11"/>'},
        warning: {titulo: 'Atenção!', desenho: '<path d="M15 4a3.5 3.5 0 0 1 6 0l14 26a3 3 0 0 1-3 4H4a3 3 0 0 1-3-4Z" fill="white" stroke="none"/><path d="M18 12v9m0 6v.1"/>'},
        danger: {titulo: 'Erro!', desenho: '<circle cx="18" cy="18" r="17" fill="white"/><path d="m12 12 12 12m0-12L12 24"/>'}
    };

    function mostrar(opcoes) {
        opcoes = opcoes || {};
        var tipo = opcoes.tipo || 'success';
        if (!Object.prototype.hasOwnProperty.call(tipos, tipo)) throw new TypeError('Tipo de alerta inválido. Use success, warning ou danger.');
        var duracao = opcoes.duracao === undefined ? 6000 : Number(opcoes.duracao);
        if (!Number.isFinite(duracao) || duracao < 0) throw new TypeError('Duração deve ser um número maior ou igual a zero.');
        var container = document.getElementById('sob-alertas');
        if (!container) {
            container = document.createElement('div');
            container.id = 'sob-alertas';
            container.className = 'sob-alertas';
            container.setAttribute('aria-label', 'Notificações');
            document.body.appendChild(container);
        }
        var alerta = document.createElement('div');
        alerta.className = 'sob-alerta sob-alerta--' + tipo;
        alerta.setAttribute('role', tipo === 'success' ? 'status' : 'alert');
        alerta.setAttribute('aria-atomic', 'true');
        var icone = document.createElement('span');
        icone.className = 'sob-alerta__icone';
        icone.setAttribute('aria-hidden', 'true');
        // Apenas SVGs constantes. Título e mensagem são inseridos como texto.
        icone.innerHTML = '<svg viewBox="0 0 36 36" fill="none" stroke="var(--sob-alerta-cor)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">' + tipos[tipo].desenho + '</svg>';
        var conteudo = document.createElement('div');
        var titulo = document.createElement('p');
        titulo.className = 'sob-alerta__titulo';
        titulo.textContent = opcoes.titulo === undefined ? tipos[tipo].titulo : String(opcoes.titulo);
        var mensagem = document.createElement('p');
        mensagem.className = 'sob-alerta__mensagem';
        mensagem.textContent = String(opcoes.mensagem || '');
        var botao = document.createElement('button');
        botao.type = 'button';
        botao.className = 'sob-alerta__fechar';
        botao.setAttribute('aria-label', 'Fechar notificação');
        botao.textContent = '×';
        conteudo.appendChild(titulo);
        conteudo.appendChild(mensagem);
        alerta.appendChild(icone);
        alerta.appendChild(conteudo);
        alerta.appendChild(botao);
        var progresso;
        if (duracao > 0) {
            var tempo = document.createElement('div');
            tempo.className = 'sob-alerta__tempo';
            tempo.setAttribute('aria-hidden', 'true');
            progresso = document.createElement('span');
            progresso.className = 'sob-alerta__progresso';
            progresso.style.animationDuration = duracao + 'ms';
            tempo.appendChild(progresso);
            conteudo.appendChild(tempo);
        }
        var restante = duracao;
        var inicio;
        var timer;
        var fechado = false;
        var pausas = new Set();
        function fechar() {
            if (fechado) return;
            fechado = true;
            clearTimeout(timer);
            document.removeEventListener('visibilitychange', visibilidade);
            alerta.remove();
        }
        function iniciar() {
            if (!duracao || fechado || pausas.size) return;
            inicio = performance.now();
            timer = setTimeout(fechar, restante);
            progresso.style.animationPlayState = 'running';
        }
        function pausar(motivo) {
            if (pausas.has(motivo)) return;
            if (!pausas.size && duracao) {
                restante = Math.max(0, restante - (performance.now() - inicio));
                clearTimeout(timer);
                progresso.style.animationPlayState = 'paused';
            }
            pausas.add(motivo);
        }
        function retomar(motivo) {
            if (pausas.delete(motivo)) iniciar();
        }
        function visibilidade() {
            if (document.hidden) pausar('oculto'); else retomar('oculto');
        }
        botao.addEventListener('click', fechar);
        alerta.addEventListener('mouseenter', function () { pausar('mouse'); });
        alerta.addEventListener('mouseleave', function () { retomar('mouse'); });
        alerta.addEventListener('focusin', function () { pausar('foco'); });
        alerta.addEventListener('focusout', function (evento) {
            if (!alerta.contains(evento.relatedTarget)) retomar('foco');
        });
        alerta.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') fechar();
        });
        document.addEventListener('visibilitychange', visibilidade);
        container.appendChild(alerta);
        iniciar();
        visibilidade();
        return {fechar: fechar};
    }
    function camposObrigatorios(campos, opcoes) {
        var nomes = Array.isArray(campos) ? campos : (campos ? [campos] : []);
        nomes = nomes.map(function (nome) { return String(nome).trim(); }).filter(Boolean);
        return mostrar(Object.assign({}, opcoes, {
            tipo: 'warning',
            titulo: 'Atenção!',
            mensagem: nomes.length ? 'Preencha os campos obrigatórios: ' + nomes.join(', ') + '.' : 'Preencha os campos obrigatórios.'
        }));
    }

    function cadastroRealizado(opcoes) {
        return mostrar(Object.assign({mensagem: 'Cadastro realizado com sucesso.'}, opcoes, {
            tipo: 'success', titulo: 'Sucesso!'
        }));
    }

    function erro(opcoes) {
        return mostrar(Object.assign({mensagem: 'Não foi possível concluir a operação. Tente novamente.'}, opcoes, {
            tipo: 'danger', titulo: 'Erro!'
        }));
    }

    window.SobAlertas = {
        mostrar: mostrar,
        camposObrigatorios: camposObrigatorios,
        cadastroRealizado: cadastroRealizado,
        erro: erro
    };
})(window, document);
