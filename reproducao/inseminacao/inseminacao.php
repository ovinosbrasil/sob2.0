<?php
$id_lote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$id_lote = $id_lote && $id_lote > 0 ? $id_lote : 0;
$inseminacoes = $id_lote ? (DBRead('inseminacao', "WHERE id = '$id_lote'") ?: array()) : array();
$inseminacao = $inseminacoes[0] ?? null;

if (!$inseminacao):
?>
<section class="content-header"><h1>Exibir Inseminação artificial</h1></section>
<section class="content"><div class="alert alert-warning">Lote de inseminação não encontrado.</div></section>
<?php
else:
$idMacho = (int)$inseminacao['id_macho'];
$cadastroMacho = DBRead(!empty($inseminacao['terceiro']) ? 'terceiros' : 'animais', "WHERE id = '$idMacho'") ?: array();
$macho = $cadastroMacho[0] ?? array();
$dataInseminacao = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$inseminacao['data'], 0, 10));
$dataFormatada = $dataInseminacao ? $dataInseminacao->format('d/m/Y') : '';
?>
<script>
function validar_inseminacao() {
  var campos = {lote:'Lote', data_inicial:'Data', pai:'Macho', raca:'Raça', semen:'Tipo de sêmen', notificacao:'Notificação'};
  var faltantes = [], primeiro;
  Object.keys(campos).forEach(function (id) {
    var campo = document.getElementById(id);
    var vazio = !campo.value.trim();
    campo.style.borderColor = vazio ? '#dd4b39' : '';
    campo.setAttribute('aria-invalid', String(vazio));
    if (vazio) { faltantes.push(campos[id]); primeiro = primeiro || campo; }
  });
  if (faltantes.length) { SobAlertas.camposObrigatorios(faltantes); primeiro.focus(); return false; }
  return true;
}


function ativar_femea() {
  var campo = document.getElementById('mae-inseminacao');
  var vazio = !campo.value.trim();
  campo.style.borderColor = vazio ? '#dd4b39' : '';
  campo.setAttribute('aria-invalid', String(vazio));
  if (vazio) { SobAlertas.camposObrigatorios(['Adicionar fêmea']); campo.focus(); return false; }
  return true;
}

function cadastrar_inseminacao(quantidade, idControle, tipo) {
  if (quantidade) {
    window.location.href = 'geral.php?pg=cadastrar_nascimento&x=' + encodeURIComponent(quantidade) +
      '&id_lote=' + encodeURIComponent(idControle) + '&tipo=' + encodeURIComponent(tipo);
  }
}

function cadastrar_ultrassom(status, idControle) {
  window.location.href = 'reproducao/inseminacao/_cadastrar_ultrassom.php?id_lote=' +
    encodeURIComponent(idControle) + '&status=' + encodeURIComponent(status);
}

document.addEventListener('buscaanimais:selecionado', function (evento) {
  if (!evento.target.classList.contains('busca-femea-inseminacao')) { return; }
  var formulario = document.getElementById('form-adicionar-femea-inseminacao');
  if (!formulario) { return; }
  if (typeof formulario.requestSubmit === 'function') {
    formulario.requestSubmit();
  } else if (ativar_femea()) {
    formulario.submit();
  }
});

function abrirFemeaInseminacao(id, terceiro) {
  var pagina = terceiro ? 'terceiro' : 'animal';
  window.location.href = 'geral.php?pg=' + pagina + '&id_animal=' + encodeURIComponent(id);
}

function confirmarExclusaoFemeaInseminacao(botao) {
  confirmarExclusao({
    titulo: 'Excluir fêmea do lote?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'A fêmea será removida deste lote de inseminação artificial.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/inseminacao/_excluir_femea.php?id_mae=' +
        encodeURIComponent(botao.getAttribute('data-id-animal')) + '&id_lote=<?=$id_lote?>';
    }
  });
}

function confirmarExclusaoLoteInseminacaoDetalhe(botao) {
  confirmarExclusao({
    titulo: 'Excluir lote de inseminação artificial?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'Confirme se deseja excluir este lote. Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/inseminacao/_excluir_lote.php?id_lote=<?=$id_lote?>';
    }
  });
}
</script>

<section class="content-header">
  <h1>Exibir Inseminação artificial</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li class="active">Exibir Inseminação artificial</li>
  </ol>
</section>

<section class="content">
  <div class="box" style="border-top:0;">
    <form method="post" action="reproducao/inseminacao/_alterar.php?id_lote=<?=$id_lote?>" onsubmit="return validar_inseminacao()" novalidate>
      <div class="box-body">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="lote">Lote<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="lote" name="lote" value="<?=htmlspecialchars($inseminacao['codigo'], ENT_QUOTES, 'UTF-8')?>">
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="data_inicial">Data inicial<span class="text-danger">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$dataFormatada?>">
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group" style="position:relative;">
              <label for="pai">Macho<span class="text-danger">*</span>
                <a href="geral.php?pg=cadastrar_animal&amp;tipo=2" target="_blank" rel="noopener"><span style="font-size:11px; color:green;">Novo</span></a>
              </label>
              <input type="text" class="form-control" id="pai" name="macho" onkeyup="pesquisar_pai(this.value)" value="<?=htmlspecialchars($macho['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>">
              <div id="lista_pai" style="border:1px solid #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;"></div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="raca">Raça<span class="text-danger">*</span></label>
              <select class="form-control select" id="raca" name="raca">
                <option value="<?=htmlspecialchars($inseminacao['raca'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($inseminacao['raca'], ENT_QUOTES, 'UTF-8')?></option>
                <?php $racas = DBRead('raca', 'ORDER BY nome ASC') ?: array(); foreach ($racas as $raca): ?>
                <option value="<?=htmlspecialchars($raca['nome'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($raca['nome'], ENT_QUOTES, 'UTF-8')?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="semen">Sêmen<span class="text-danger">*</span></label>
              <select class="form-control select" id="semen" name="semen">
                <option value="<?=htmlspecialchars($inseminacao['semen'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($inseminacao['semen'], ENT_QUOTES, 'UTF-8')?></option>
                <option value="A fresco">A fresco</option>
                <option value="Congelado">Congelado</option>
                <option value="Refrigerado">Refrigerado</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="notificacao">Notificação<span class="text-danger">*</span></label>
              <select class="form-control select" id="notificacao" name="notificacao">
                <option value="<?=htmlspecialchars($inseminacao['notificacao'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($inseminacao['notificacao'], ENT_QUOTES, 'UTF-8')?></option>
                <option value="PO">PO</option>
                <option value="PC">PC</option>
              </select>
            </div>
          </div>
          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-warning">Alterar lote</button>
            <button type="button" class="btn btn-danger"
                    data-nome="<?=htmlspecialchars('Lote ' . $inseminacao['codigo'], ENT_QUOTES, 'UTF-8')?>"
                    onclick="confirmarExclusaoLoteInseminacaoDetalhe(this)">
              <i class="fa fa-trash-o" aria-hidden="true"></i> Excluir lote
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-body">
      <form id="form-adicionar-femea-inseminacao" method="post"
            action="reproducao/inseminacao/_cadastrar_femea.php?id_lote=<?=$id_lote?>"
            onsubmit="return ativar_femea()" novalidate>
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="col-sm-12 col-md-6">
            <div class="form-group">
              <?php renderBuscaAnimais(array(
                  'id' => 'mae-inseminacao',
                  'name' => 'mae',
                  'label' => 'Adicionar fêmea',
                  'tipo' => 'femeas',
                  'required' => true,
                  'limite_origem' => 5,
                  'classe' => 'busca-femea-inseminacao'
              )); ?>
            </div>
          </div>
        </div>
      </form>

      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin:5px 0 15px;">
        <h3 class="box-title" style="font-size:16px; margin:0;">Fêmeas do lote</h3>
        <a href="reproducao/inseminacao/_imprimir.php?id_lote=<?=$id_lote?>" target="_blank" rel="noopener" class="btn btn-primary">
          <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Gerar PDF
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr>
            <th style="width:45px;">Nº</th>
            <th>Fêmea</th>
            <th>Tipo</th>
            <th style="width:20%;">Ultrassom</th>
            <th style="width:20%;">Nascimento</th>
            <th style="width:1%;"><span class="sr-only">Ações</span></th>
          </tr></thead>
          <tbody>
          <?php
          $controles = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote' ORDER BY id ASC") ?: array();
          if (!$controles): ?>
            <tr><td colspan="6" class="text-center">Nenhuma fêmea adicionada ao lote.</td></tr>
          <?php endif; ?>
          <?php foreach ($controles as $indice => $controle):
            $idAnimal = (int)$controle['id_femea'];
            $ehTerceiro = !empty($controle['terceiro']);
            $cadastroFemea = DBRead($ehTerceiro ? 'terceiros' : 'animais', "WHERE id = '$idAnimal'") ?: array();
            if (!$cadastroFemea) { continue; }
            $femea = $cadastroFemea[0];
            $ultrassomPositivo = (int)$controle['ultrassom'] === 1;
            $ultrassomNegativo = (int)$controle['ultrassom'] === 2;
            $proximoUltrassom = $ultrassomPositivo ? 2 : 1;
            $textoUltrassom = $ultrassomPositivo ? 'Positivo' : ($ultrassomNegativo ? 'Negativo' : 'Não informado');
            $iconeUltrassom = $ultrassomPositivo ? 'fa-check' : ($ultrassomNegativo ? 'fa-times' : 'fa-minus');
            $corUltrassom = $ultrassomPositivo ? '#008d4c' : ($ultrassomNegativo ? '#dd4b39' : '#777');
          ?>
            <tr>
              <td><?=$indice + 1?></td>
              <td onclick="abrirFemeaInseminacao(<?=$idAnimal?>, <?=$ehTerceiro ? 1 : 0?>)"
                  style="cursor:pointer;" title="Abrir animal">
                <?=htmlspecialchars($femea['nome'], ENT_QUOTES, 'UTF-8')?><?=$ehTerceiro ? ' (Terceiro)' : ''?>
              </td>
              <td><?=htmlspecialchars($femea['tipo'] ?? '--', ENT_QUOTES, 'UTF-8')?></td>
              <td>
                <div class="sob-controle-status">
                  <button type="button"
                          class="sob-interruptor"
                          role="switch"
                          aria-checked="<?=$ultrassomPositivo ? 'true' : 'false'?>"
                          aria-label="Ultrassom de <?=htmlspecialchars($femea['nome'], ENT_QUOTES, 'UTF-8')?>: <?=$textoUltrassom?>"
                          title="Alterar para <?=$proximoUltrassom === 1 ? 'positivo' : 'negativo'?>"
                          onclick="cadastrar_ultrassom(<?=$proximoUltrassom?>, <?=(int)$controle['id']?>)">
                    <span class="sob-interruptor__indicador"><i class="fa <?=$iconeUltrassom?>" aria-hidden="true"></i></span>
                  </button>
                  <span class="sob-controle-status__texto" style="color:<?=$corUltrassom?>;"><?=$textoUltrassom?></span>
                </div>
              </td>
              <td>
                <?php if ($ultrassomNegativo): ?>
                  <span class="text-muted">--</span>
                <?php else: ?>
                  <select class="form-control"
                          onchange="cadastrar_inseminacao(this.value, <?=(int)$controle['id']?>, '2')"
                          style="<?=!empty($controle['status_nascimento']) ? 'color:green;' : ''?>">
                    <option value=""><?=!empty($controle['status_nascimento']) ? 'Cria cadastrada' : 'Selecionar'?></option>
                    <option value="1">Parto simples</option>
                    <option value="2">Parto duplo</option>
                    <option value="3">Parto triplo</option>
                  </select>
                <?php endif; ?>
              </td>
              <td style="white-space:nowrap;">
                <button type="button" class="text-danger" style="background:none; border:0; padding:0;"
                        data-id-animal="<?=$idAnimal?>"
                        data-nome="<?=htmlspecialchars($femea['nome'], ENT_QUOTES, 'UTF-8')?>"
                        onclick="confirmarExclusaoFemeaInseminacao(this)"
                        title="Excluir fêmea" aria-label="Excluir fêmea">
                  <i class="fa fa-trash-o" aria-hidden="true"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
