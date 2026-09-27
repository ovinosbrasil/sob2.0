<?php
$id_lote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$id_lote = $id_lote && $id_lote > 0 ? (int)$id_lote : 0;
if (empty($_SESSION['receptora_csrf'])) {
    $_SESSION['receptora_csrf'] = bin2hex(random_bytes(32));
}
$teReceptoraFlash = $_SESSION['te_receptora_flash'] ?? null;
unset($_SESSION['te_receptora_flash']);
$teCadastroReceptoraFlash = $_SESSION['te_cadastro_receptora_flash'] ?? null;
unset($_SESSION['te_cadastro_receptora_flash']);

$lotesTe = $id_lote ? (DBRead('transplante', "WHERE id = '$id_lote'") ?: array()) : array();
$te = $lotesTe[0] ?? null;
if (!$te):
?>
<section class="content-header"><h1>Exibir Transplante de embriões</h1></section>
<section class="content"><div class="alert alert-warning">Lote de transplante não encontrado.</div></section>
<?php
else:
$idPai = (int)$te['id_pai'];
$pais = DBRead(!empty($te['terceiro_pai']) ? 'terceiros' : 'animais', "WHERE id = '$idPai'") ?: array();
$pai = $pais[0] ?? array();

$idPaiComplementar = (int)($te['id_pai_2'] ?? 0);
$paisComplementares = $idPaiComplementar
    ? (DBRead(!empty($te['terceiro_pai_2']) ? 'terceiros' : 'animais', "WHERE id = '$idPaiComplementar'") ?: array())
    : array();
$paiComplementar = $paisComplementares[0] ?? array();

$idMae = (int)$te['id_mae'];
$maes = DBRead(!empty($te['terceiro_mae']) ? 'terceiros' : 'animais', "WHERE id = '$idMae'") ?: array();
$mae = $maes[0] ?? array();

$dataTe = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$te['data'], 0, 10));
$dataTeFormatada = $dataTe ? $dataTe->format('d/m/Y') : '';
$dataColeta = null;
if (!empty($te['data_coleta']) && $te['data_coleta'] !== '0000-00-00') {
    $dataColeta = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$te['data_coleta'], 0, 10));
}
$dataColetaFormatada = $dataColeta ? $dataColeta->format('d/m/Y') : '';
?>
<script>
function validar_te() {
  var ids = ['lote', 'data_inicial', 'pai', 'mae', 'raca', 'tipo_semen', 'embrioes', 'congelados', 'usados'];
  var invalido = false;
  ids.forEach(function (id) {
    var campo = document.getElementById(id);
    if (!campo) { return; }
    var vazio = !campo.value.trim();
    campo.style.border = vazio ? '1px solid red' : '';
    if (vazio) { invalido = true; }
  });
  return !invalido;
}

function ativarReceptora() {
  var campo = document.getElementById('receptora-te');
  if (!campo || !campo.value.trim()) {
    if (campo) { campo.style.border = '1px solid red'; }
    return false;
  }
  campo.style.border = '';
  return true;
}

function cadastrarNascimentoTe(quantidade, idControle, tipo) {
  if (quantidade) {
    window.location.href = 'geral.php?pg=cadastrar_nascimento&x=' + encodeURIComponent(quantidade) +
      '&id_lote=' + encodeURIComponent(idControle) + '&tipo=' + encodeURIComponent(tipo);
  }
}

function cadastrarUltrassomTe(status, idControle) {
  window.location.href = 'reproducao/te/_cadastrar_ultrassom.php?id_lote=<?=$id_lote?>&status=' +
    encodeURIComponent(status) + '&id_controle=' + encodeURIComponent(idControle);
}

function atualizarNumeroEmbrioes(valor, idControle) {
  window.location.href = 'reproducao/te/_n_embrioes.php?id_lote=' + encodeURIComponent(idControle) +
    '&valor=' + encodeURIComponent(valor) + '&id_te=<?=$id_lote?>';
}

document.addEventListener('buscaanimais:selecionado', function (evento) {
  if (!evento.target.classList.contains('busca-receptora-te')) { return; }
  var formulario = document.getElementById('form-adicionar-receptora-te');
  if (!formulario) { return; }
  if (typeof formulario.requestSubmit === 'function') {
    formulario.requestSubmit();
  } else if (ativarReceptora()) {
    formulario.submit();
  }
});

function confirmarExclusaoReceptoraTe(botao) {
  confirmarExclusao({
    titulo: 'Excluir receptora do lote?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'A receptora será removida deste lote de transplante de embriões.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/te/_excluir_femea.php?id_controle=' +
        encodeURIComponent(botao.getAttribute('data-id-controle')) + '&id_lote=<?=$id_lote?>';
    }
  });
}

function confirmarExclusaoLoteTeDetalhe(botao) {
  confirmarExclusao({
    titulo: 'Excluir lote de transplante de embriões?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'Confirme se deseja excluir este lote. Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/te/_excluir_lote.php?id_lote=<?=$id_lote?>';
    }
  });
}
</script>

<section class="content-header">
  <h1>Exibir Transplante de embriões</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li class="active">Exibir Transplante de embriões</li>
  </ol>
</section>

<section class="content">
  <div class="box" style="border-top:0;">
    <form method="post" action="reproducao/te/_alterar.php?id_lote=<?=$id_lote?>" onsubmit="return validar_te()">
      <div class="box-body">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="lote">Lote<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="lote" name="lote" value="<?=htmlspecialchars($te['codigo'], ENT_QUOTES, 'UTF-8')?>">
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="data_inicial">Data<span class="text-danger">*</span></label>
            <div class="input-group date">
              <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
              <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$dataTeFormatada?>">
            </div>
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group" style="position:relative;">
            <label for="pai">Macho<span class="text-danger">*</span>
              <a href="geral.php?pg=cadastrar_animal&amp;tipo=2" target="_blank" rel="noopener"><span style="font-size:11px; color:green;">Novo</span></a>
            </label>
            <input type="text" class="form-control" id="pai" name="macho" onkeyup="pesquisar_pai(this.value)" value="<?=htmlspecialchars($pai['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>">
            <div id="lista_pai" style="border:1px solid #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;"></div>
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group" style="position:relative;">
            <label for="macho_complementar">Macho complementar</label>
            <input type="text" class="form-control" id="macho_complementar" name="macho_complementar" autocomplete="off" oninput="pesquisar_pai_2(this.value)" value="<?=htmlspecialchars($paiComplementar['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>">
            <input type="hidden" id="id_pai_2" name="id_pai_2" value="<?=$idPaiComplementar?>">
            <input type="hidden" id="terceiro_pai_2" name="terceiro_pai_2" value="<?=(int)($te['terceiro_pai_2'] ?? 0)?>">
            <div id="lista_pai_2" style="border:1px solid #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;"></div>
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group" style="position:relative;">
            <label for="mae">Fêmea<span class="text-danger">*</span>
              <a href="geral.php?pg=cadastrar_animal&amp;tipo=2" target="_blank" rel="noopener"><span style="font-size:11px; color:green;">Novo</span></a>
            </label>
            <input type="text" class="form-control" id="mae" name="femea" onkeyup="pesquisar_mae(this.value)" value="<?=htmlspecialchars($mae['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>">
            <div id="lista_mae" style="border:1px solid #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;"></div>
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="raca">Raça<span class="text-danger">*</span></label>
            <select class="form-control select" id="raca" name="raca">
              <option value="<?=htmlspecialchars($te['raca'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($te['raca'], ENT_QUOTES, 'UTF-8')?></option>
              <?php $racas = DBRead('raca', 'ORDER BY nome ASC') ?: array(); foreach ($racas as $raca): if ($raca['nome'] === $te['raca']) { continue; } ?>
              <option value="<?=htmlspecialchars($raca['nome'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($raca['nome'], ENT_QUOTES, 'UTF-8')?></option>
              <?php endforeach; ?>
            </select>
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="tipo_semen">Tipo de Sêmen<span class="text-danger">*</span></label>
            <select class="form-control select" id="tipo_semen" name="tipo_semen">
              <option value="<?=htmlspecialchars($te['tipo_semen'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($te['tipo_semen'] ?: 'Selecionar', ENT_QUOTES, 'UTF-8')?></option>
              <option value="A fresco">A fresco</option>
              <option value="Congelado">Congelado</option>
            </select>
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="embrioes">Embriões coletados<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="embrioes" name="embrioes" value="<?=htmlspecialchars($te['qtd'], ENT_QUOTES, 'UTF-8')?>">
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="congelados">Embriões congelados<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="congelados" name="congelados" value="<?=htmlspecialchars($te['congelados'], ENT_QUOTES, 'UTF-8')?>">
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="usados">Embriões usados<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="usados" name="usados" value="<?=htmlspecialchars($te['usados'], ENT_QUOTES, 'UTF-8')?>">
          </div></div>

          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="data">Data coleta</label>
            <div class="input-group date">
              <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
              <input type="text" class="form-control pull-right" id="data" name="data_coleta" value="<?=$dataColetaFormatada?>">
            </div>
          </div></div>

          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-warning">Alterar lote</button>
            <button type="button" class="btn btn-danger" data-nome="<?=htmlspecialchars('Lote ' . $te['codigo'], ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoLoteTeDetalhe(this)">
              <i class="fa fa-trash-o" aria-hidden="true"></i> Excluir lote
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-body">
      <?php if ($teReceptoraFlash !== null && !empty($teReceptoraFlash['erro'])): ?>
      <div class="alert alert-danger" role="alert"><?=htmlspecialchars($teReceptoraFlash['erro'], ENT_QUOTES, 'UTF-8')?></div>
      <?php endif; ?>

      <form id="form-adicionar-receptora-te" method="post" action="reproducao/te/_cadastrar_femea.php?id_lote=<?=$id_lote?>" onsubmit="return ativarReceptora()">
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['receptora_csrf'], ENT_QUOTES, 'UTF-8')?>">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <?php renderBuscaAnimais(array(
                'id' => 'receptora-te',
                'name' => 'receptora',
                'name_id' => 'receptora_id',
                'name_origem' => 'receptora_origem',
                'label' => 'Adicionar receptora',
                'tipo' => 'receptoras',
                'required' => true,
                'classe' => 'busca-receptora-te',
                'novo_modal' => '#cadastro-receptora-te',
                'novo_texto' => 'Novo'
            )); ?>
          </div></div>
        </div>
      </form>

      <?php
      require_once __DIR__ . '/_kg_apartacao.php';
      $apartacaoLote = kgApartacaoDoLote($id_lote);
      $controles = DBRead('transplante_controle', "WHERE id_lote = '$id_lote' ORDER BY id ASC") ?: array();
      ?>

      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin:5px 0 15px;">
        <h3 class="box-title" style="font-size:16px; margin:0;">Receptoras do lote</h3>
        <a href="reproducao/te/_imprimir.php?id_lote=<?=$id_lote?>" target="_blank" rel="noopener" class="btn btn-primary">
          <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Gerar PDF
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr>
            <th style="width:45px;">Nº</th>
            <th>Receptora</th>
            <th style="width:15%;">Nº de embriões</th>
            <th style="width:20%;">Ultrassom</th>
            <th style="width:20%;">Nascimento</th>
            <th style="width:12%;">kg/apartação</th>
            <th style="width:1%;"><span class="sr-only">Ações</span></th>
          </tr></thead>
          <tbody>
          <?php if (!$controles): ?><tr><td colspan="7" class="text-center">Nenhuma receptora adicionada ao lote.</td></tr><?php endif; ?>
          <?php foreach ($controles as $indice => $controle):
            $ultrassomPositivo = (int)$controle['ultrassom'] === 1;
            $ultrassomNegativo = (int)$controle['ultrassom'] === 2;
            $proximoUltrassom = $ultrassomPositivo ? 2 : 1;
            $textoUltrassom = $ultrassomPositivo ? 'Positivo' : ($ultrassomNegativo ? 'Negativo' : 'Não informado');
            $iconeUltrassom = $ultrassomPositivo ? 'fa-check' : ($ultrassomNegativo ? 'fa-times' : 'fa-minus');
            $corUltrassom = $ultrassomPositivo ? '#008d4c' : ($ultrassomNegativo ? '#dd4b39' : '#777');
            $kgApartacao = $apartacaoLote['somas'][(int)($controle['id_receptora'] ?? 0)] ?? null;
            $corApartacao = '#777';
            if ($kgApartacao !== null && $apartacaoLote['media'] !== null) {
                if ($kgApartacao > $apartacaoLote['media']) { $corApartacao = '#008d4c'; }
                elseif ($kgApartacao < $apartacaoLote['media']) { $corApartacao = '#dd4b39'; }
            }
          ?>
            <tr>
              <td><?=$indice + 1?></td>
              <td><?=htmlspecialchars($controle['receptora'], ENT_QUOTES, 'UTF-8')?></td>
              <td><input type="text" class="form-control" value="<?=htmlspecialchars($controle['n_embrioes'], ENT_QUOTES, 'UTF-8')?>" onblur="atualizarNumeroEmbrioes(this.value, <?=(int)$controle['id']?>)"></td>
              <td>
                <div class="sob-controle-status">
                  <button type="button" class="sob-interruptor" role="switch"
                          aria-checked="<?=$ultrassomPositivo ? 'true' : 'false'?>"
                          aria-label="Ultrassom da receptora <?=htmlspecialchars($controle['receptora'], ENT_QUOTES, 'UTF-8')?>: <?=$textoUltrassom?>"
                          title="Alterar para <?=$proximoUltrassom === 1 ? 'positivo' : 'negativo'?>"
                          onclick="cadastrarUltrassomTe(<?=$proximoUltrassom?>, <?=(int)$controle['id']?>)">
                    <span class="sob-interruptor__indicador"><i class="fa <?=$iconeUltrassom?>" aria-hidden="true"></i></span>
                  </button>
                  <span class="sob-controle-status__texto" style="color:<?=$corUltrassom?>;"><?=$textoUltrassom?></span>
                </div>
              </td>
              <td>
                <?php if ($ultrassomNegativo): ?>
                  <span class="text-muted">--</span>
                <?php else: ?>
                  <select class="form-control" onchange="cadastrarNascimentoTe(this.value, <?=(int)$controle['id']?>, '3')" style="<?=!empty($controle['status_nascimento']) ? 'color:green;' : ''?>">
                    <option value=""><?=!empty($controle['status_nascimento']) ? 'Cria cadastrada' : 'Selecionar'?></option>
                    <option value="1">Parto simples</option>
                    <option value="2">Parto duplo</option>
                    <option value="3">Parto triplo</option>
                  </select>
                <?php endif; ?>
              </td>
              <td style="color:<?=$corApartacao?>; white-space:nowrap;" title="Soma do peso de apartação dos animais da receptora na janela prevista do lote.">
                <?=$kgApartacao !== null ? number_format($kgApartacao, 2, ',', '.') . ' kg' : 'N/A'?>
              </td>
              <td style="white-space:nowrap;">
                <button type="button" class="text-danger" style="background:none; border:0; padding:0;"
                        data-id-controle="<?=(int)$controle['id']?>"
                        data-nome="<?=htmlspecialchars($controle['receptora'], ENT_QUOTES, 'UTF-8')?>"
                        onclick="confirmarExclusaoReceptoraTe(this)" title="Excluir receptora" aria-label="Excluir receptora">
                  <i class="fa fa-trash-o" aria-hidden="true"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="text-muted text-right" title="Média dos totais por receptora com peso de apartação informado na janela prevista do lote.">
        <strong>Média de kg/apartação do lote:</strong>
        <?=$apartacaoLote['media'] !== null ? number_format($apartacaoLote['media'], 2, ',', '.') . ' kg' : 'N/A'?>
      </p>
    </div>
  </div>
</section>

<div class="modal fade" id="cadastro-receptora-te" tabindex="-1" role="dialog" aria-labelledby="titulo-cadastro-receptora-te">
  <div class="modal-dialog" role="document" style="width:440px; max-width:calc(100vw - 32px); margin:10vh auto;">
    <div class="modal-content" style="border:0; border-radius:12px;">
      <form action="animal/_cadastrar_receptora.php" method="post">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">&times;</button>
          <h4 class="modal-title" id="titulo-cadastro-receptora-te">Cadastrar receptora</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['receptora_csrf'], ENT_QUOTES, 'UTF-8')?>">
          <input type="hidden" name="retorno" value="te">
          <input type="hidden" name="id_lote" value="<?=$id_lote?>">
          <?php if ($teCadastroReceptoraFlash !== null && !empty($teCadastroReceptoraFlash['erro'])): ?>
          <div class="alert alert-danger" role="alert"><?=htmlspecialchars($teCadastroReceptoraFlash['erro'], ENT_QUOTES, 'UTF-8')?></div>
          <?php endif; ?>
          <div class="form-group">
            <label for="nome-receptora-te">Nome <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nome-receptora-te" name="nome" maxlength="50" required placeholder="Nome da receptora" value="<?=htmlspecialchars($teCadastroReceptoraFlash['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="reproducao/te/macho_complementar.js"></script>
<script>
jQuery(function ($) {
  $('#cadastro-receptora-te').on('shown.bs.modal', function () {
    document.getElementById('nome-receptora-te').focus();
  });
  <?php if ($teCadastroReceptoraFlash !== null && !empty($teCadastroReceptoraFlash['erro'])): ?>
  $('#cadastro-receptora-te').modal('show');
  <?php endif; ?>
});
</script>
<?php endif; ?>
