(function () {
  var layout = document.querySelector('#avaliacoes-animal .gmd-layout');
  if (!layout) return;
  var data = document.getElementById('gmd_data');
  var peso = document.getElementById('gmd_peso');
  var dicas = document.getElementById('gmd_dicas');
  var formato = new Intl.NumberFormat('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  function numero(valor) {
    valor = valor.trim();
    if (valor.includes(',')) valor = valor.replace(/\./g, '').replace(',', '.');
    return valor === '' ? NaN : Number(valor);
  }
  function atualizar() {
    var partes = document.getElementById('data_nascimento').value.split('/');
    var inicio = partes.length === 3 ? Date.UTC(+partes[2], +partes[1] - 1, +partes[0]) : NaN;
    var fim = data.value ? Date.parse(data.value + 'T00:00:00Z') : NaN;
    var dias = (fim - inicio) / 86400000;
    var inicial = numero(document.getElementById('peso_inicial').value);
    var final = numero(peso.value);
    var dataValida = Number.isFinite(dias) && dias > 0;
    var pesoValido = Number.isFinite(final) && final > 0;
    data.setCustomValidity(data.value && !dataValida ? 'A data da avaliação deve ser posterior ao nascimento.' : '');
    peso.setCustomValidity(peso.value && !pesoValido ? 'Informe um peso maior que zero.' : '');
    var valido = dataValida && pesoValido && Number.isFinite(inicial) && inicial > 0;
    document.getElementById('gmd_diferenca').textContent = valido ? formato.format(final - inicial) + ' kg' : '—';
    document.getElementById('gmd_periodo').textContent = dataValida ? dias + ' dias' : '—';
    document.getElementById('gmd_resultado').textContent = valido ? formato.format((final - inicial) / dias * 1000) + ' g/dia' : '—';
    var sexo = layout.dataset.sexo;
    dicas.hidden = !dataValida || !['Macho', 'Fêmea'].includes(sexo);
    if (dicas.hidden) return;
    var titulo = document.getElementById('gmd_dicas_titulo');
    var linhas = document.getElementById('gmd_dicas_linhas');
    if (dias > 365) {
      titulo.textContent = 'Animais com idade maior que 365 dias deverão ser pontuados com Tipo 4.';
      linhas.parentElement.hidden = true;
      return;
    }
    linhas.parentElement.hidden = false;
    var faixa = dias <= 90 ? 0 : dias <= 210 ? 1 : 2;
    var limites = sexo === 'Macho' ? [[276,243,210,176], [236,203,170,136], [216,183,150,116]][faixa] : [[266,233,200,166], [226,193,160,126], [206,173,140,106]][faixa];
    titulo.textContent = 'Dicas de GMD para ' + (sexo === 'Macho' ? 'machos' : 'fêmeas') + ' ' + ['com até 90 dias', 'entre 90 e 210 dias', 'entre 210 e 365 dias'][faixa] + ':';
    // Preserva as faixas de pontuação exibidas anteriormente.
    var topoTipo3 = sexo === 'Macho' && faixa === 2 ? 163 : limites[1];
    var valores = ['Acima de ' + formato.format(limites[0]) + ' g/dia', 'Entre ' + formato.format(limites[1]) + ' e ' + formato.format(limites[0]) + ' g/dia', 'Entre ' + formato.format(limites[2]) + ' e ' + formato.format(topoTipo3) + ' g/dia', 'Entre ' + formato.format(limites[3]) + ' e ' + formato.format(limites[2]) + ' g/dia'];
    linhas.replaceChildren();
    valores.forEach(function (valor, indice) {
      var linha = document.createElement('tr');
      ['Tipo ' + (5 - indice), valor].forEach(function (texto) {
        var celula = document.createElement('td');
        celula.textContent = texto;
        linha.appendChild(celula);
      });
      linhas.appendChild(linha);
    });
  }
  [data, peso].forEach(function (campo) {
    campo.addEventListener('input', atualizar);
    campo.addEventListener('change', atualizar);
  });
  atualizar();
})();
