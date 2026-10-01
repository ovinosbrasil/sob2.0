<style>
#tabela-previa-possiveis_atualizacoes > tbody[data-registro-previa] > tr > td {
  background-color: #fff;
}
#tabela-previa-possiveis_atualizacoes > tbody.previa-faixa-escura > tr > td {
  background-color: #ededed;
}
#tabela-previa-possiveis_atualizacoes .dado-divergente {
  color: #b52b27;
  font-weight: 600;
}
</style>
<div class="table-responsive">
  <table class="table table-bordered" id="tabela-previa-possiveis_atualizacoes" data-tabela-previa>
    <caption class="sr-only">Possíveis animais para atualização: comparação entre planilha e banco</caption>
    <thead>
      <tr><th colspan="4" scope="colgroup" class="text-center">Animal da planilha</th><th colspan="4" scope="colgroup" class="text-center">Animal encontrado no banco</th><th rowspan="2" scope="col">Ação</th></tr>
      <tr><?php for ($cabecalho = 0; $cabecalho < 2; $cabecalho++): ?><th scope="col">FBB</th><th scope="col">Nome</th><th scope="col">Tatuagem</th><th scope="col">Nascimento</th><?php endfor; ?></tr>
    </thead>
    <?php foreach ($gruposPrevia['possiveis_atualizacoes'] as $indiceRegistro => $registro):
      $candidatos = $registro['animais_banco'];
      $pesquisaRegistro = $registro['dados']['Nome'] . ' ' . $registro['dados']['FBB/FBE'] . ' ' . $registro['dados']['Tat.'];
      foreach ($candidatos as $candidato) {
          $pesquisaRegistro .= ' ' . ($candidato['nome'] ?? '') . ' ' . ($candidato['fbb'] ?? '') . ' ' . ($candidato['tatuagem'] ?? '');
      }
    ?>
    <tbody class="<?=$indiceRegistro % 2 ? 'previa-faixa-escura' : ''?>" data-registro-previa data-pesquisa="<?=hPreviaRebanho($pesquisaRegistro)?>">
      <?php foreach ($candidatos as $indiceCandidato => $candidato):
        $divergencias = divergenciasAnimalPrevia($registro['dados'], $candidato);
        $nascimentoBanco = (string)($candidato['data_de_nascimento'] ?? '');
        $dataBanco = DateTimeImmutable::createFromFormat('!Y-m-d', $nascimentoBanco);
        $nascimentoBanco = $dataBanco && $dataBanco->format('Y-m-d') === $nascimentoBanco
            ? $dataBanco->format('d/m/Y') : ($nascimentoBanco !== '' ? $nascimentoBanco : 'Não informado');
      ?>
      <tr>
        <?php if ($indiceCandidato === 0): ?>
          <?php foreach (array('FBB/FBE', 'Nome', 'Tat.', 'Nasc.') as $campo): ?>
          <td rowspan="<?=count($candidatos)?>"><?=hPreviaRebanho($registro['dados'][$campo] ?? '')?></td>
          <?php endforeach; ?>
        <?php endif; ?>
        <?php foreach (array('fbb', 'nome', 'tatuagem', 'data_de_nascimento') as $campoBanco): ?>
        <td class="<?=$divergencias[$campoBanco] ? 'dado-divergente' : ''?>">
          <?php if ($divergencias[$campoBanco]): ?><span class="sr-only">Diferente da planilha: </span><?php endif; ?>
          <?=hPreviaRebanho($campoBanco === 'status' ? situacaoAnimalPrevia($candidato['status'] ?? null) : ($campoBanco === 'data_de_nascimento' ? $nascimentoBanco : (($candidato[$campoBanco] ?? '') !== '' ? $candidato[$campoBanco] : 'Não informado')))?>
        </td>
        <?php endforeach; ?>
        <td>
          <?php
          $mudancasAtualizacao = array();
          foreach (array('nome' => 'Nome', 'fbb' => 'FBB/FBE', 'tatuagem' => 'Tat.', 'data_de_nascimento' => 'Nasc.') as $campo => $coluna) {
              $mudancasAtualizacao[] = array(
                  'chave' => $campo,
                  'campo' => array('nome' => 'Nome', 'fbb' => 'FBB', 'tatuagem' => 'Tatuagem', 'data_de_nascimento' => 'Nascimento')[$campo],
                  'atual' => $campo === 'status' ? situacaoAnimalPrevia($candidato['status'] ?? null) : ($campo === 'data_de_nascimento' ? $nascimentoBanco : (string)($candidato[$campo] ?? '')),
                  'novo' => $registro['dados'][$coluna] ?? ''
              );
          }
          if (morteNascimentoNomePrevia($registro['dados']['Nome'] ?? '')) {
              $saidaBanco = $candidato['data_de_saida'] ?? '';
              $dataSaidaBanco = DateTimeImmutable::createFromFormat('!Y-m-d', $saidaBanco);
              $mudancasAtualizacao[] = array('chave' => 'causa_da_perda', 'campo' => 'Causa da morte', 'atual' => $candidato['causa_da_perda'] ?? '', 'novo' => 'Nascimento');
              $mudancasAtualizacao[] = array('chave' => 'data_de_saida', 'campo' => 'Data da morte', 'atual' => $dataSaidaBanco && $dataSaidaBanco->format('Y-m-d') === $saidaBanco ? $dataSaidaBanco->format('d/m/Y') : $saidaBanco, 'novo' => $registro['dados']['Nasc.']);
          }
          $erroAtualizacao = '';
          try {
              dadosAtualizacaoPrevia($registro['dados']);
              if (isset($_SESSION['previa_rebanho']['atualizados'][$registro['linha']])) {
                  throw new RuntimeException('Esta linha já foi atualizada. Envie uma nova planilha para revisar novamente.');
              }
          }
          catch (RuntimeException $erro) { $erroAtualizacao = $erro->getMessage(); }
          ?>
          <form action="animal/_atualizar_importacao.php" method="post" data-mudancas="<?=hPreviaRebanho(json_encode($mudancasAtualizacao, JSON_INVALID_UTF8_SUBSTITUTE))?>" onsubmit="return confirmarAtualizacaoRebanho(this);">
            <input type="hidden" name="token_previa" value="<?=hPreviaRebanho($_SESSION['token_previa_rebanho'] ?? '')?>">
            <input type="hidden" name="envio" value="<?=hPreviaRebanho($envioPrevia ?? '')?>">
            <input type="hidden" name="linha" value="<?=(int)($registro['linha'] ?? 0)?>">
            <input type="hidden" name="id_animal" value="<?=(int)$candidato['id']?>">
            <button type="submit" class="btn btn-success btn-sm" <?=$erroAtualizacao !== '' ? 'disabled' : ''?>>Atualizar</button>
          </form>
          <?php if ($erroAtualizacao !== ''): ?><small class="text-danger"><?=hPreviaRebanho($erroAtualizacao)?></small><?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <?php endforeach; ?>
    <tbody><tr data-sem-resultados <?=$gruposPrevia['possiveis_atualizacoes'] ? 'hidden' : ''?>><td colspan="9" class="text-center">Nenhum animal nesta tabela.</td></tr></tbody>
  </table>
</div>

<div class="modal fade" id="confirmar-atualizacao-rebanho" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="titulo-atualizacao-rebanho" aria-describedby="descricao-atualizacao-rebanho">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulo-atualizacao-rebanho">Confirmar atualização do animal</h4>
      </div>
      <div class="modal-body">
        <p id="descricao-atualizacao-rebanho">Selecione os campos que deseja atualizar. Os campos desmarcados serão mantidos no banco. As diferenças estão destacadas.</p>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead><tr><th scope="col">Atualizar</th><th scope="col">Campo</th><th scope="col">Atual no banco</th><th scope="col">Novo valor da planilha</th></tr></thead>
            <tbody id="mudancas-atualizacao-rebanho"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelar-atualizacao-rebanho">Cancelar</button>
        <button type="button" class="btn btn-success" id="salvar-atualizacao-rebanho">Confirmar atualização</button>
      </div>
    </div>
  </div>
</div>
<script>
(function () {
  var formularioPendente = null;
  var botaoOrigem = null;
  var enviando = false;
  var confirmar = document.getElementById('salvar-atualizacao-rebanho');
  window.confirmarAtualizacaoRebanho = function (formulario) {
    if (enviando || formulario.querySelector('button').disabled) return false;
    formularioPendente = formulario;
    botaoOrigem = formulario.querySelector('button');
    var corpo = document.getElementById('mudancas-atualizacao-rebanho');
    corpo.textContent = '';
    JSON.parse(formulario.getAttribute('data-mudancas')).forEach(function (mudanca) {
      var linha = document.createElement('tr');
      var alterado = mudanca.atual !== mudanca.novo;
      if (alterado) linha.className = 'warning';
      var selecao = document.createElement('input');
      selecao.type = 'checkbox';
      selecao.checked = true;
      selecao.value = mudanca.chave;
      selecao.setAttribute('aria-label', 'Atualizar ' + mudanca.campo);
      selecao.addEventListener('change', function () {
        linha.classList.toggle('warning', alterado && selecao.checked);
        linha.classList.toggle('text-muted', !selecao.checked);
        confirmar.disabled = corpo.querySelectorAll('input:checked').length === 0;
      });
      var colunaSelecao = document.createElement('td');
      if (alterado) colunaSelecao.appendChild(selecao);
      else colunaSelecao.textContent = '—';
      linha.appendChild(colunaSelecao);
      [mudanca.campo, mudanca.atual || 'Não informado', mudanca.novo || 'Não informado'].forEach(function (valor, indice) {
        var celula = document.createElement(indice === 0 ? 'th' : 'td');
        if (indice === 0) celula.scope = 'row';
        celula.textContent = valor;
        if (indice === 2 && alterado) {
          var aviso = document.createElement('span');
          aviso.className = 'sr-only';
          aviso.textContent = ' (diferente do atual)';
          celula.appendChild(aviso);
        }
        linha.appendChild(celula);
      });
      corpo.appendChild(linha);
    });
    confirmar.disabled = corpo.querySelectorAll('input:checked').length === 0;
    $('#confirmar-atualizacao-rebanho').modal('show');
    return false;
  };
  confirmar.addEventListener('click', function () {
    if (!formularioPendente || enviando) return;
    var campos = document.querySelectorAll('#mudancas-atualizacao-rebanho input:checked');
    if (!campos.length) return;
    formularioPendente.querySelectorAll('[data-campo-selecionado]').forEach(function (campo) { campo.remove(); });
    function adicionarCampo(nome, valor) {
      var campo = document.createElement('input');
      campo.type = 'hidden'; campo.name = nome; campo.value = valor;
      campo.setAttribute('data-campo-selecionado', '');
      formularioPendente.appendChild(campo);
    }
    adicionarCampo('selecionar_campos', '1');
    campos.forEach(function (campo) { adicionarCampo('campos[]', campo.value); });
    enviando = true;
    confirmar.disabled = true;
    confirmar.textContent = 'Salvando…';
    botaoOrigem.disabled = true;
    formularioPendente.submit();
  });
  document.addEventListener('DOMContentLoaded', function () {
    $('#confirmar-atualizacao-rebanho').on('shown.bs.modal', function () {
      document.getElementById('cancelar-atualizacao-rebanho').focus();
    }).on('hidden.bs.modal', function () {
      formularioPendente = null;
      if (botaoOrigem && !enviando) botaoOrigem.focus();
    });
  });
})();
</script>
