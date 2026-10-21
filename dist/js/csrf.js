(function (window, document) {
  'use strict';
  window.enviarPostCsrfSob = function (destino, dados) {
    var base = document.getElementById('sob-post-csrf');
    var formulario = document.createElement('form');
    formulario.method = 'post';
    formulario.action = destino;
    formulario.hidden = true;
    formulario.appendChild(base.elements._csrf_sob.cloneNode(true));
    Object.keys(dados).forEach(function (nome) {
      if (nome === '_csrf_sob') return;
      var campo = document.createElement('input');
      campo.type = 'hidden';
      campo.name = nome;
      campo.value = dados[nome];
      formulario.appendChild(campo);
    });
    document.body.appendChild(formulario);
    formulario.submit();
  };
}(window, document));
