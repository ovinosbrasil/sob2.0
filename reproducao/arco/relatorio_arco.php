<?php
require_once __DIR__ . '/_codigo_nascimento.php';
function arcoH($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function arcoData($valor) {
    $valor=trim((string)$valor);
    if($valor==='') return null;
    $data=DateTimeImmutable::createFromFormat('!d/m/Y',$valor);
    return $data && $data->format('d/m/Y')===$valor ? $data : null;
}
function arcoRegistroAnimal($id,$terceiro) {
    $id=(int)$id; if(!$id)return null;
    $dados=DBRead($terceiro?'terceiros':'animais',"WHERE id = '$id' LIMIT 1")?:array();
    return $dados[0]??null;
}

$entrada=array_merge($_GET,$_POST);
$numeracao=trim((string)($entrada['numeracao']??''));
$tipo=isset($entrada['tipo'])&&in_array($entrada['tipo'],array('Monta natural','Inseminação artificial','Trans. de embriões'),true)?$entrada['tipo']:'';
$dataInicialTexto=trim((string)($entrada['data_inicial']??''));
$dataFinalTexto=trim((string)($entrada['data_final']??''));
$dataInicial=arcoData($dataInicialTexto); $dataFinal=arcoData($dataFinalTexto);
$raca=trim((string)($entrada['raca']??($user[0]['raca']??'')));
$pesquisou=isset($entrada['pesquisar'])||$tipo!=='';
$mapaTipos=array('Monta natural'=>'Monta Natural','Inseminação artificial'=>'Inseminação Artificial','Trans. de embriões'=>'Embrionagem');
$filtroTipo=$mapaTipos[$tipo]??'';
$condicoes=array();
if($filtroTipo!=='')$condicoes[]="tipo_reproducao = '".DBEscape($filtroTipo)."'";
if($dataInicial)$condicoes[]="data_de_nascimento >= '".$dataInicial->format('Y-m-d')."'";
if($dataFinal)$condicoes[]="data_de_nascimento <= '".$dataFinal->format('Y-m-d')."'";
$where=$condicoes?'WHERE '.implode(' AND ',$condicoes):'';

$porPagina=filter_var($entrada['por_pagina']??10,FILTER_VALIDATE_INT);
if(!in_array($porPagina,array(10,20,50,100),true))$porPagina=10;
$totalAnimais=0; $animais=array();
if($pesquisou&&$filtroTipo!==''){
    $contagem=DBRead('animais',$where,'COUNT(*) total')?:array(); $totalAnimais=(int)($contagem[0]['total']??0);
}
$totalPaginas=max(1,(int)ceil($totalAnimais/$porPagina));
$paginaInformada=filter_var($entrada['pag']??1,FILTER_VALIDATE_INT);
$pagina=min(max(1,$paginaInformada===false?1:$paginaInformada),$totalPaginas);
$inicio=($pagina-1)*$porPagina;
if($totalAnimais)$animais=DBRead('animais',"$where ORDER BY data_de_nascimento ASC, id ASC LIMIT $inicio, $porPagina")?:array();

$parametros=array('pg'=>'relatorio_arco','pesquisar'=>1,'numeracao'=>$numeracao,'tipo'=>$tipo,'data_inicial'=>$dataInicialTexto,'data_final'=>$dataFinalTexto,'raca'=>$raca,'por_pagina'=>$porPagina);
$urlPagina='geral.php?'.arcoH(http_build_query($parametros));
$impressoes=array('Monta natural'=>'_imprimir_monta.php','Inseminação artificial'=>'_imprimir_inseminacao.php','Trans. de embriões'=>'_imprimir_te.php');
$urlImpressao='';
if(isset($impressoes[$tipo])&&$dataInicial&&$dataFinal){
    $urlImpressao='reproducao/arco/'.$impressoes[$tipo].'?'.http_build_query(array('numeracao'=>$numeracao,'raca'=>$raca,'data_inicial'=>$dataInicial->format('Y-m-d'),'data_final'=>$dataFinal->format('Y-m-d')));
}
$racas=DBRead('raca','ORDER BY nome ASC')?:array();
?>
<section class="content-header">
  <h1>Relatório ARCO</h1>
  <ol class="breadcrumb"><li><i class="fa fa-venus-mars"></i> Reprodução</li><li class="active">Relatório ARCO</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;"><div class="box-body">
    <form action="geral.php" method="get">
      <input type="hidden" name="pg" value="relatorio_arco"><input type="hidden" name="pesquisar" value="1"><input type="hidden" name="por_pagina" value="<?=$porPagina?>">
      <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group col-sm-6 col-md-2"><label for="numeracao-arco">Numeração do lote</label><input type="text" class="form-control" id="numeracao-arco" name="numeracao" value="<?=arcoH($numeracao)?>"></div>
        <div class="form-group col-sm-6 col-md-2"><label for="tipo-arco">Tipo de reprodução</label><select class="form-control" id="tipo-arco" name="tipo"><option value="">Selecionar</option><?php foreach($mapaTipos as $valor=>$ignorado): ?><option value="<?=arcoH($valor)?>" <?=$tipo===$valor?'selected':''?>><?=arcoH($valor)?></option><?php endforeach; ?></select></div>
        <div class="form-group col-sm-6 col-md-2"><label for="data-inicial-arco">Data inicial</label><div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control" id="data-inicial-arco" name="data_inicial" value="<?=arcoH($dataInicialTexto)?>"></div></div>
        <div class="form-group col-sm-6 col-md-2"><label for="data-final-arco">Data final</label><div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control" id="data-final-arco" name="data_final" value="<?=arcoH($dataFinalTexto)?>"></div></div>
        <div class="form-group col-sm-6 col-md-2"><label for="raca-arco">Raça<span class="text-danger">*</span></label><select class="form-control" id="raca-arco" name="raca" required><option value="">Selecionar</option><?php foreach($racas as $item): ?><option value="<?=arcoH($item['nome'])?>" <?=$raca===$item['nome']?'selected':''?>><?=arcoH($item['nome'])?></option><?php endforeach; ?></select></div>
        <div class="form-group col-sm-6 col-md-2"><button type="submit" class="btn btn-primary">Pesquisar</button> <a class="btn btn-default" href="geral.php?pg=relatorio_arco">Limpar</a></div>
      </div>
    </form>
  </div></div>

  <div class="box" style="border-top:0;"><div class="box-body">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;">
      <h3 class="box-title" style="font-size:16px; margin:0;"><?=$pesquisou?'Resultado da pesquisa':'Lotes de nascimento'?></h3>
      <?php if($urlImpressao!==''): ?><a class="btn btn-success" href="<?=arcoH($urlImpressao)?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Gerar PDF</a><?php endif; ?>
    </div>
    <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Fbb</th><th>Nome</th><th>Tat.</th><th>Sexo</th><th>Nascimento</th><th>COD.IRREG(*)</th><th>Pai</th><th>Fbb</th><th>Mãe</th><th>Fbb</th></tr></thead><tbody>
      <?php if(!$animais): ?><tr><td colspan="10" class="text-center"><?=$pesquisou?'Nenhum animal encontrado.':'Informe os filtros para pesquisar.'?></td></tr><?php endif; ?>
      <?php foreach($animais as $animal): $pai=arcoRegistroAnimal($animal['pai']??0,!empty($animal['terceiro_pai'])); $mae=arcoRegistroAnimal($animal['mae']??0,!empty($animal['terceiro_mae'])); ?>
      <tr><td><?=arcoH($animal['fbb']??'')?></td><td onclick="abrir_animal(<?=(int)$animal['id']?>)" style="cursor:pointer;"><?=arcoH($animal['nome']??'')?></td><td><?=arcoH($animal['tatuagem']??'')?></td><td><?=arcoH($animal['sexo']??'')?></td><td><?=arcoH(($data=DateTimeImmutable::createFromFormat('!Y-m-d',substr((string)($animal['data_de_nascimento']??''),0,10)))?$data->format('d/m/Y'):'--')?></td><td><?=arcoH(codigoNascimentoArco($animal))?></td><td><?=arcoH($pai['nome']??'--')?></td><td><?=arcoH($pai['fbb']??'')?></td><td><?=arcoH($mae['nome']??'--')?></td><td><?=arcoH($mae['fbb']??'')?></td></tr>
      <?php endforeach; ?>
    </tbody></table></div>
    <?php if($pesquisou): ?><div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
      <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;"><?php foreach($parametros as $nome=>$valor): if($nome==='por_pagina')continue; ?><input type="hidden" name="<?=arcoH($nome)?>" value="<?=arcoH($valor)?>"><?php endforeach; ?><label for="por-pagina-arco" style="margin:0; font-weight:normal;">Por página</label><select id="por-pagina-arco" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()"><?php foreach(array(10,20,50,100) as $qtd): ?><option value="<?=$qtd?>" <?=$porPagina===$qtd?'selected':''?>><?=$qtd?></option><?php endforeach; ?></select></form>
      <span class="text-muted">Exibindo <?=$totalAnimais?$inicio+1:0?> a <?=min($inicio+$porPagina,$totalAnimais)?> de <?=$totalAnimais?> registros</span>
      <?php if($totalPaginas>1): $visiveis=array(1,$totalPaginas); for($n=max(1,$pagina-1);$n<=min($totalPaginas,$pagina+1);$n++)$visiveis[]=$n; $visiveis=array_values(array_unique($visiveis));sort($visiveis);$anterior=0; ?><nav aria-label="Paginação do relatório ARCO"><ul class="pagination pagination-sm no-margin"><li class="<?=$pagina===1?'disabled':''?>"><a href="<?=$pagina===1?'#':$urlPagina.'&amp;pag='.($pagina-1)?>">&laquo;</a></li><?php foreach($visiveis as $n): ?><?php if($anterior&&$n>$anterior+1): ?><li class="disabled"><span>&hellip;</span></li><?php endif; ?><li class="<?=$n===$pagina?'active':''?>"><a href="<?=$urlPagina?>&amp;pag=<?=$n?>"><?=$n?></a></li><?php $anterior=$n;endforeach;?><li class="<?=$pagina===$totalPaginas?'disabled':''?>"><a href="<?=$pagina===$totalPaginas?'#':$urlPagina.'&amp;pag='.($pagina+1)?>">&raquo;</a></li></ul></nav><?php endif; ?>
    </div><?php endif; ?>
  </div></div>
</section>
