<?php
function vacinaListaH($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function vacinaListaData($v){$v=substr((string)$v,0,10);$d=DateTimeImmutable::createFromFormat('!Y-m-d',$v);return $d&&$d->format('Y-m-d')===$v?$d->format('d/m/Y'):'--';}
$animalId=filter_var($_GET['animal_id']??null,FILTER_VALIDATE_INT);$animalId=$animalId&&$animalId>0?(int)$animalId:0;
$animalNome=trim((string)($_GET['animal']??''));
$vacinaId=filter_var($_GET['vacina_id']??null,FILTER_VALIDATE_INT);$vacinaId=$vacinaId&&$vacinaId>0?(int)$vacinaId:0;
$porPagina=filter_var($_GET['por_pagina']??10,FILTER_VALIDATE_INT);if(!in_array($porPagina,array(10,20,50,100),true))$porPagina=10;
$condicoes=array();if($animalId)$condicoes[]="ap.id_animal='$animalId'";if($vacinaId)$condicoes[]="ap.id_vacina='$vacinaId'";$where=$condicoes?'WHERE '.implode(' AND ',$condicoes):'';
$tabela='vacinas ap INNER JOIN animais a ON a.id=ap.id_animal INNER JOIN vacina v ON v.id=ap.id_vacina';
$contagem=DBRead($tabela,$where,'COUNT(*) total')?:array();$totalRegistros=(int)($contagem[0]['total']??0);$totalPaginas=max(1,(int)ceil($totalRegistros/$porPagina));
$pagina=filter_var($_GET['pag']??1,FILTER_VALIDATE_INT);$pagina=min(max(1,$pagina===false?1:$pagina),$totalPaginas);$inicio=($pagina-1)*$porPagina;
$aplicacoes=DBRead($tabela,"$where ORDER BY ap.data DESC,ap.id DESC LIMIT $inicio,$porPagina","ap.id,ap.id_animal,a.nome animal_nome,v.nome vacina_nome,ap.data,ap.obs")?:array();
$vacinas=DBRead('vacina','ORDER BY nome ASC')?:array();
$params=array('pg'=>'vacinas','por_pagina'=>$porPagina);if($animalId){$params['animal_id']=$animalId;$params['animal']=$animalNome;$params['animal_origem']='rebanho';}if($vacinaId)$params['vacina_id']=$vacinaId;
function vacinaListaUrl($p){return 'geral.php?'.htmlspecialchars(http_build_query($p),ENT_QUOTES,'UTF-8');}
?>
<script>
function abrirAnimalVacinado(id){if(/^\d+$/.test(String(id))&&Number(id)>0)window.location.href='geral.php?pg=animal&id_animal='+encodeURIComponent(id)+'&aba=vacina';}
function pesquisarVacinasAnimais(){var form=document.getElementById('form-filtro-vacinas');if(form)form.submit();}
document.addEventListener('buscaanimais:selecionado',function(evento){if(evento.target.classList.contains('filtro-busca-animal-vacina'))pesquisarVacinasAnimais();});
function confirmarExclusaoVacinaAnimal(botao,evento){
  evento.stopPropagation();
  confirmarExclusao({titulo:'Excluir vacina do animal?',nome:botao.getAttribute('data-animal')+' — '+botao.getAttribute('data-vacina'),descricao:'O registro desta aplicação será removido.',aoConfirmar:function(){window.location.href='vacinas/_excluir_vacina.php?id_vacina='+encodeURIComponent(botao.getAttribute('data-id'))+'&retorno=lista';}});
}
</script>
<section class="content-header"><h1>Vacinas dos animais</h1><ol class="breadcrumb"><li><i class="fa fa-eyedropper"></i> Vacinas</li><li class="active">Aplicações</li></ol></section>
<section class="content">
<div class="box" style="border-top:0;"><div class="box-body"><form id="form-filtro-vacinas" action="geral.php" method="get"><input type="hidden" name="pg" value="vacinas"><div class="row" style="display:flex;flex-wrap:wrap;align-items:flex-end;">
<div class="form-group col-sm-6 col-md-3"><?php renderBuscaAnimais(array('id'=>'filtro-animal-vacina','name'=>'animal','name_id'=>'animal_id','name_origem'=>'animal_origem','label'=>'Animal','tipo'=>'rebanho','value'=>$animalNome,'value_id'=>$animalId,'value_origem'=>$animalId?'rebanho':'','limite_origem'=>10,'classe'=>'filtro-busca-animal-vacina')); ?></div>
<div class="form-group col-sm-6 col-md-3"><label for="filtro-vacina">Vacina</label><select class="form-control" id="filtro-vacina" name="vacina_id" onchange="pesquisarVacinasAnimais()"><option value="">Todas</option><?php foreach($vacinas as $v): ?><option value="<?=(int)$v['id']?>" <?=$vacinaId===(int)$v['id']?'selected':''?>><?=vacinaListaH($v['nome'])?></option><?php endforeach; ?></select></div>
<div class="form-group col-sm-6 col-md-3"><button type="submit" class="btn btn-primary">Pesquisar</button> <a href="geral.php?pg=vacinas" class="btn btn-default">Limpar</a></div>
</div></form></div></div>
<div class="box" style="border-top:0;"><div class="box-body">
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:15px;"><h3 class="box-title" style="font-size:16px;margin:0;">Vacinas cadastradas nos animais</h3><a href="geral.php?pg=cadastrar_vacina" class="btn btn-success"><i class="fa fa-plus"></i> Cadastrar vacina</a></div>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Animal</th><th>Vacina</th><th>Data</th><th>Observações</th><th style="width:1%;"><span class="sr-only">Ações</span></th></tr></thead><tbody>
<?php if(!$aplicacoes): ?><tr><td colspan="5" class="text-center">Nenhuma vacina cadastrada nos animais.</td></tr><?php endif; ?>
<?php foreach($aplicacoes as $a): ?><tr onclick="abrirAnimalVacinado(<?=(int)$a['id_animal']?>)" style="cursor:pointer;"><td><?=vacinaListaH($a['animal_nome'])?></td><td><?=vacinaListaH($a['vacina_nome'])?></td><td><?=vacinaListaH(vacinaListaData($a['data']))?></td><td><?=vacinaListaH($a['obs'])?></td><td><button type="button" class="text-danger" style="background:none;border:0;padding:0;" data-id="<?=(int)$a['id']?>" data-animal="<?=vacinaListaH($a['animal_nome'])?>" data-vacina="<?=vacinaListaH($a['vacina_nome'])?>" onclick="confirmarExclusaoVacinaAnimal(this,event)" title="Excluir"><i class="fa fa-trash-o"></i></button></td></tr><?php endforeach; ?>
</tbody></table></div>
<div class="box-footer" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:16px;">
<form action="geral.php" method="get" style="display:flex;align-items:center;gap:8px;margin:0;"><?php foreach($params as $nome=>$valor):if($nome==='por_pagina')continue;?><input type="hidden" name="<?=vacinaListaH($nome)?>" value="<?=vacinaListaH($valor)?>"><?php endforeach;?><label for="por-pagina-vacina" style="margin:0;font-weight:normal;">Por página</label><select id="por-pagina-vacina" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()"><?php foreach(array(10,20,50,100) as $qtd):?><option value="<?=$qtd?>" <?=$porPagina===$qtd?'selected':''?>><?=$qtd?></option><?php endforeach;?></select></form>
<span class="text-muted">Exibindo <?=$totalRegistros?$inicio+1:0?> a <?=min($inicio+$porPagina,$totalRegistros)?> de <?=$totalRegistros?> registros</span>
<?php if($totalPaginas>1):$visiveis=array(1,$totalPaginas);for($n=max(1,$pagina-1);$n<=min($totalPaginas,$pagina+1);$n++)$visiveis[]=$n;$visiveis=array_values(array_unique($visiveis));sort($visiveis);$anterior=0;?><nav aria-label="Paginação das vacinas"><ul class="pagination pagination-sm no-margin"><li class="<?=$pagina===1?'disabled':''?>"><a href="<?=$pagina===1?'#':vacinaListaUrl(array_merge($params,array('pag'=>$pagina-1)))?>">&laquo;</a></li><?php foreach($visiveis as $n):?><?php if($anterior&&$n>$anterior+1):?><li class="disabled"><span>&hellip;</span></li><?php endif;?><li class="<?=$n===$pagina?'active':''?>"><a href="<?=vacinaListaUrl(array_merge($params,array('pag'=>$n)))?>"><?=$n?></a></li><?php $anterior=$n;endforeach;?><li class="<?=$pagina===$totalPaginas?'disabled':''?>"><a href="<?=$pagina===$totalPaginas?'#':vacinaListaUrl(array_merge($params,array('pag'=>$pagina+1)))?>">&raquo;</a></li></ul></nav><?php endif;?>
</div></div></div>
</section>
