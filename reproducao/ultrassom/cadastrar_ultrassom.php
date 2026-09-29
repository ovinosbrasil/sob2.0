<?php
require_once __DIR__ . '/../../includes/busca_animais.php';

function cadastroUltraLista($dados) { return is_array($dados) ? $dados : array(); }
function cadastroUltraH($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function cadastroUltraFormatarData($valor) {
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$valor, 0, 10));
    return $data ? $data->format('d/m/Y') : '--';
}
function cadastroUltraData($valor) {
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', trim((string)$valor));
    return $data && $data->format('d/m/Y') === trim((string)$valor) ? $data : null;
}
function cadastroUltraAnimal($id, $terceiro) {
    $dados = cadastroUltraLista(DBRead($terceiro ? 'terceiros' : 'animais', "WHERE id = '".(int)$id."' LIMIT 1"));
    return $dados[0] ?? null;
}
function cadastroUltraNomeMacho($lote, $tipo) {
    $campo = $tipo === 0 ? 'id_animal' : 'id_macho';
    $terceiro = !empty($lote['terceiro']);
    if ($tipo === 2) { $campo='id_pai'; $terceiro=!empty($lote['terceiro_pai']); }
    $animal = cadastroUltraAnimal($lote[$campo] ?? 0, $terceiro);
    return $animal['nome'] ?? '--';
}

$buscar = isset($_GET['buscar']) && (string)$_GET['buscar'] === '1';
$femea = isset($_GET['femea']) ? trim((string)$_GET['femea']) : '';
$femeaId = filter_var($_GET['femea_id'] ?? 0, FILTER_VALIDATE_INT);
$femeaId = $femeaId && $femeaId > 0 ? (int)$femeaId : 0;
$origem = isset($_GET['femea_origem']) && in_array($_GET['femea_origem'], array('rebanho','terceiros','receptora'), true) ? $_GET['femea_origem'] : '';
$situacao = isset($_GET['situacao']) && in_array((string)$_GET['situacao'], array('1','2'), true) ? (int)$_GET['situacao'] : 0;
$dataTexto = isset($_GET['data']) ? trim((string)$_GET['data']) : date('d/m/Y');
$data = cadastroUltraData($dataTexto);
$lotesDisponiveis = array();
$erro = isset($_GET['erro']) ? trim((string)$_GET['erro']) : '';

if ($buscar && (!$femeaId || !$origem || !$situacao || !$data)) {
    $erro = 'Selecione a fêmea e preencha corretamente a situação e a data.';
}
if ($buscar && $erro === '') {
    if ($origem === 'receptora') {
        $receptora = cadastroUltraLista(DBRead('receptora', "WHERE id = '{$femeaId}' AND ativo = 1 LIMIT 1"));
        if (!$receptora) {
            $erro = 'A receptora selecionada não foi encontrada.';
        } else {
            foreach (cadastroUltraLista(DBRead('transplante_controle', "WHERE id_receptora = '{$femeaId}' AND ultrassom = '0' ORDER BY id DESC")) as $controle) {
                $idLote=(int)$controle['id_lote']; $lote=cadastroUltraLista(DBRead('transplante', "WHERE id = '{$idLote}' LIMIT 1"));
                if (!$lote) continue; $lote=$lote[0];
                $lotesDisponiveis[]=array('tipo'=>2,'controle'=>(int)$controle['id'],'lote'=>$idLote,'codigo'=>$lote['codigo']??$idLote,'reproducao'=>'Transplante de embriões','macho'=>cadastroUltraNomeMacho($lote,2),'data'=>$lote['data']??'');
            }
        }
    } else {
        $terceiro = $origem === 'terceiros' ? 1 : 0;
        $animal = cadastroUltraAnimal($femeaId, $terceiro);
        if (!$animal || ($animal['sexo'] ?? '') !== 'Fêmea') {
            $erro = 'A fêmea selecionada não foi encontrada.';
        } else {
            foreach (cadastroUltraLista(DBRead('monta_controle', "WHERE id_animal = '{$femeaId}' AND terceiro = '{$terceiro}' AND ultrassom = '0' ORDER BY id DESC")) as $controle) {
                $idLote=(int)$controle['id_monta']; $lote=cadastroUltraLista(DBRead('monta', "WHERE id = '{$idLote}' LIMIT 1"));
                if (!$lote) continue; $lote=$lote[0];
                $lotesDisponiveis[]=array('tipo'=>0,'controle'=>(int)$controle['id'],'lote'=>$idLote,'codigo'=>$lote['codigo']??$idLote,'reproducao'=>'Monta natural','macho'=>cadastroUltraNomeMacho($lote,0),'data'=>$lote['data_inicio']??'');
            }
            foreach (cadastroUltraLista(DBRead('inseminacao_controle', "WHERE id_femea = '{$femeaId}' AND terceiro = '{$terceiro}' AND ultrassom = '0' ORDER BY id DESC")) as $controle) {
                $idLote=(int)$controle['id_lote']; $lote=cadastroUltraLista(DBRead('inseminacao', "WHERE id = '{$idLote}' LIMIT 1"));
                if (!$lote) continue; $lote=$lote[0];
                $lotesDisponiveis[]=array('tipo'=>1,'controle'=>(int)$controle['id'],'lote'=>$idLote,'codigo'=>$lote['codigo']??$idLote,'reproducao'=>'Inseminação artificial','macho'=>cadastroUltraNomeMacho($lote,1),'data'=>$lote['data']??'');
            }
        }
    }
}
?>
<script>
function selecionarLoteCadastroUltrassom(linha) {
  var tabela = linha.closest('table');
  if (!tabela) { return; }
  tabela.querySelectorAll('.linha-lote-ultrassom').forEach(function (item) {
    item.classList.remove('info');
    item.setAttribute('aria-selected', 'false');
  });
  var radio = linha.querySelector('input[type="radio"][name="vinculo"]');
  if (radio) { radio.checked = true; }
  linha.classList.add('info');
  linha.setAttribute('aria-selected', 'true');
}
document.addEventListener('DOMContentLoaded', function () {
  var marcado = document.querySelector('.linha-lote-ultrassom input[name="vinculo"]:checked');
  if (marcado) { selecionarLoteCadastroUltrassom(marcado.closest('tr')); }
});
</script>
<section class="content-header">
  <h1>Cadastrar ultrassom</h1>
  <ol class="breadcrumb"><li><i class="fa fa-venus-mars"></i> Reprodução</li><li><a href="geral.php?pg=lista_ultrassom">Ultrassom</a></li><li class="active">Cadastrar</li></ol>
</section>
<section class="content">
  <?php if ($erro !== ''): ?><div class="alert alert-danger"><?=cadastroUltraH($erro)?></div><?php endif; ?>
  <div class="box" style="border-top:0;"><div class="box-body">
    <form action="geral.php" method="get">
      <input type="hidden" name="pg" value="cadastrar_ultrassom"><input type="hidden" name="buscar" value="1">
      <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
        <div class="col-sm-6 col-md-4"><div class="form-group">
          <?php renderBuscaAnimais(array('id'=>'femea-cadastro-ultrassom','name'=>'femea','name_id'=>'femea_id','name_origem'=>'femea_origem','label'=>'Fêmea','tipo'=>'femeas_receptoras','required'=>true,'value'=>$femea,'value_id'=>$femeaId,'value_origem'=>$origem,'limite_origem'=>5)); ?>
        </div></div>
        <div class="col-sm-6 col-md-4"><div class="form-group"><label for="situacao-ultrassom">Situação<span class="text-danger">*</span></label>
          <select class="form-control" id="situacao-ultrassom" name="situacao" required><option value="">Selecionar</option><option value="1" <?=$situacao===1?'selected':''?>>Positivo</option><option value="2" <?=$situacao===2?'selected':''?>>Negativo</option></select>
        </div></div>
        <div class="col-sm-6 col-md-4"><div class="form-group"><label for="data-ultrassom">Data<span class="text-danger">*</span></label>
          <div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control" id="data-ultrassom" name="data" value="<?=cadastroUltraH($dataTexto)?>" required></div>
        </div></div>
        <div class="col-sm-12 text-right"><a class="btn btn-default" href="geral.php?pg=lista_ultrassom">Cancelar</a> <button type="submit" class="btn btn-success">Cadastrar</button></div>
      </div>
    </form>
  </div></div>

  <?php if ($buscar && $erro === ''): ?>
  <div class="box" style="border-top:0;"><div class="box-body">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;"><h3 class="box-title" style="font-size:16px; margin:0;">Lotes disponíveis para <?=cadastroUltraH($femea)?></h3></div>
    <form action="reproducao/ultrassom/_cadastrar_registro.php" method="post">
      <input type="hidden" name="femea_id" value="<?=$femeaId?>"><input type="hidden" name="femea" value="<?=cadastroUltraH($femea)?>"><input type="hidden" name="femea_origem" value="<?=cadastroUltraH($origem)?>"><input type="hidden" name="situacao" value="<?=$situacao?>"><input type="hidden" name="data" value="<?=cadastroUltraH($dataTexto)?>">
      <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th style="width:45px;"><span class="sr-only">Selecionar</span></th><th>Tipo de reprodução</th><th>Lote</th><th>Macho</th><th>Data do lote</th></tr></thead><tbody>
      <?php if (!$lotesDisponiveis): ?><tr><td colspan="5" class="text-center">Não há lotes pendentes de ultrassom para esta fêmea.</td></tr><?php endif; ?>
      <?php foreach($lotesDisponiveis as $indice=>$item): ?><tr class="linha-lote-ultrassom" style="cursor:pointer;" tabindex="0" aria-selected="false" onclick="selecionarLoteCadastroUltrassom(this)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selecionarLoteCadastroUltrassom(this);}"><td class="text-center"><input type="radio" name="vinculo" value="<?=$item['tipo']?>:<?=$item['controle']?>:<?=$item['lote']?>" <?=$indice===0?'checked':''?> aria-label="Selecionar lote <?=cadastroUltraH($item['codigo'])?>"></td><td><?=cadastroUltraH($item['reproducao'])?></td><td><?=cadastroUltraH($item['codigo'])?></td><td><?=cadastroUltraH($item['macho'])?></td><td><?=cadastroUltraH(cadastroUltraFormatarData($item['data']))?></td></tr><?php endforeach; ?>
      </tbody></table></div>
      <?php if ($lotesDisponiveis): ?><div class="text-right"><button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> Confirmar cadastro</button></div><?php endif; ?>
    </form>
  </div></div>
  <?php endif; ?>
</section>
