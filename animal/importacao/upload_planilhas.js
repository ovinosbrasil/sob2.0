(function () {
  'use strict';
  var vivos = document.getElementById('planilha-vivos');
  var mortos = document.getElementById('planilha-mortos');
  var verificar = document.getElementById('verificar-planilhas');
  if (!vivos || !mortos || !verificar) return;
  function atualizar() {
    var completos = true;
    [vivos, mortos].forEach(function (campo) {
      var arquivo = campo.files && campo.files[0];
      var erro = arquivo && arquivo.size > 5 * 1024 * 1024 ? 'O arquivo deve ter até 5 MB.' : '';
      if (arquivo && !/\.xls$/i.test(arquivo.name)) erro = 'Selecione uma exportação .xls.';
      campo.setCustomValidity(erro);
      if (!arquivo || erro) completos = false;
    });
    verificar.disabled = !completos;
  }
  vivos.addEventListener('change', atualizar);
  mortos.addEventListener('change', atualizar);
  window.addEventListener('pageshow', atualizar);
  atualizar();
})();
