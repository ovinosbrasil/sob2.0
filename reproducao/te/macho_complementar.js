var pesquisaPai2 = null;

function pesquisar_pai_2(nome) {
  document.getElementById('id_pai_2').value = '0';
  document.getElementById('terceiro_pai_2').value = '0';
  if (pesquisaPai2) pesquisaPai2.abort();
  fechar_lista_pai_2();
  if (!nome.trim()) return;

  var requisicao = new XMLHttpRequest();
  pesquisaPai2 = requisicao;
  requisicao.open('GET', 'animal/lista_pai.php?campo=pai_2&nome=' + encodeURIComponent(nome), true);
  requisicao.onload = function () {
    if (pesquisaPai2 !== requisicao || requisicao.status !== 200) return;
    var lista = document.getElementById('lista_pai_2');
    lista.innerHTML = requisicao.responseText;
    lista.style.display = 'block';
  };
  requisicao.send();
}

function fechar_lista_pai_2() {
  if (pesquisaPai2) pesquisaPai2.abort();
  pesquisaPai2 = null;
  document.getElementById('lista_pai_2').style.display = 'none';
}

function linkar_pai_2(nome, id, terceiro) {
  document.getElementById('macho_complementar').value = nome;
  document.getElementById('id_pai_2').value = id;
  document.getElementById('terceiro_pai_2').value = terceiro;
  fechar_lista_pai_2();
}
