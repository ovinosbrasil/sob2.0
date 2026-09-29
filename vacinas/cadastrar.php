<?php
require_once __DIR__ . '/../includes/cadastro_vacina_modal.php';
$vacinas=DBRead('vacina','ORDER BY nome ASC')?:array();
$nomes=isset($_GET['animal'])?(array)$_GET['animal']:array('');$ids=isset($_GET['animal_id'])?(array)$_GET['animal_id']:array(0);$origens=isset($_GET['animal_origem'])?(array)$_GET['animal_origem']:array('');
$quantidade=max(1,count($nomes),count($ids));
$vacinaId=filter_var($_GET['vacina']??null,FILTER_VALIDATE_INT);$vacinaId=$vacinaId?(int)$vacinaId:0;
$dataInformada=trim((string)($_GET['data']??date('d/m/Y')));$observacao=trim((string)($_GET['observacao']??''));$erro=trim((string)($_GET['erro']??''));
function campoAnimalVacina($indice,$nome='',$id=0,$origem='',$removivel=false){ ?>
<div class="animal-vacina-item" style="display:flex;align-items:flex-end;gap:8px;margin-bottom:10px;">
  <div style="flex:1;"><?php renderBuscaAnimais(array('id'=>'animal-vacina-'.$indice,'name'=>'animal[]','name_id'=>'animal_id[]','name_origem'=>'animal_origem[]','label'=>$indice===0?'Animal':'Outro animal','tipo'=>'rebanho','required'=>true,'value'=>$nome,'value_id'=>$id,'value_origem'=>$origem==='rebanho'?'rebanho':'','limite_origem'=>10));?></div>
  <?php if($removivel):?><button type="button" class="btn btn-danger" onclick="removerAnimalVacina(this)" title="Remover animal" style="margin-bottom:0;"><i class="fa fa-minus"></i></button><?php else:?><button type="button" class="btn btn-success" onclick="adicionarAnimalVacina()" title="Adicionar outro animal"><i class="fa fa-plus"></i></button><?php endif;?>
</div><?php }
?>
<script>
var proximoAnimalVacina=<?=$quantidade?>;
function adicionarAnimalVacina(){
  var modelo=document.getElementById('modelo-animal-vacina').innerHTML.replace(/__INDICE__/g,String(proximoAnimalVacina++));
  var area=document.getElementById('outros-animais-vacina'),envoltorio=document.createElement('div');envoltorio.innerHTML=modelo;
  var item=envoltorio.firstElementChild;area.appendChild(item);
  if(window.BuscaAnimais)window.BuscaAnimais.iniciar(item.querySelector('[data-busca-animais]'));
  item.querySelector('.sob-busca-animais__input').focus();
}
function removerAnimalVacina(botao){botao.closest('.animal-vacina-item').remove();}
</script>
<section class="content-header"><h1>Cadastrar vacina</h1><ol class="breadcrumb"><li><a href="geral.php?pg=vacinas"><i class="fa fa-eyedropper"></i> Vacinas</a></li><li class="active">Cadastrar</li></ol></section>
<section class="content">
<?php if($erro!==''):?><div class="alert alert-danger"><?=htmlspecialchars($erro,ENT_QUOTES,'UTF-8')?></div><?php endif;?>
<div class="box" style="border-top:0;"><div class="box-body">
<form method="post" action="vacinas/_cadastrar.php">
  <div class="row"><div class="col-sm-12 col-md-6"><?php campoAnimalVacina(0,$nomes[0]??'',(int)($ids[0]??0),$origens[0]??'',false);?><div id="outros-animais-vacina"><?php for($i=1;$i<$quantidade;$i++)campoAnimalVacina($i,$nomes[$i]??'',(int)($ids[$i]??0),$origens[$i]??'',true);?></div></div></div>
  <div class="row" style="display:flex;flex-wrap:wrap;align-items:flex-end;">
    <div class="form-group col-sm-6 col-md-4"><label for="vacina">Vacina <span class="text-danger">*</span></label><div class="input-group"><select class="form-control" id="vacina" name="vacina" required><option value="">Selecionar</option><?php foreach($vacinas as $v):?><option value="<?=(int)$v['id']?>" <?=$vacinaId===(int)$v['id']?'selected':''?>><?=htmlspecialchars($v['nome'],ENT_QUOTES,'UTF-8')?></option><?php endforeach;?></select><span class="input-group-btn"><button type="button" class="btn btn-success" data-toggle="modal" data-target="#cadastro-nova-vacina">Novo</button></span></div></div>
    <div class="form-group col-sm-6 col-md-4"><label for="data_vacina">Data <span class="text-danger">*</span></label><div class="input-group date"><span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="data_vacina" name="data" value="<?=htmlspecialchars($dataInformada,ENT_QUOTES,'UTF-8')?>" placeholder="dd/mm/aaaa" required></div></div>
    <div class="form-group col-sm-12"><label for="observacao-vacina">Observações</label><textarea class="form-control" id="observacao-vacina" name="observacao" rows="3" maxlength="300"><?=htmlspecialchars($observacao,ENT_QUOTES,'UTF-8')?></textarea></div>
    <div class="col-sm-12 text-right"><a href="geral.php?pg=vacinas" class="btn btn-default">Cancelar</a> <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Cadastrar vacina</button></div>
  </div>
</form>
</div></div>
</section>
<template id="modelo-animal-vacina"><div class="animal-vacina-item" style="display:flex;align-items:flex-end;gap:8px;margin-bottom:10px;"><div style="flex:1;"><?php renderBuscaAnimais(array('id'=>'animal-vacina-__INDICE__','name'=>'animal[]','name_id'=>'animal_id[]','name_origem'=>'animal_origem[]','label'=>'Outro animal','tipo'=>'rebanho','required'=>true,'limite_origem'=>10));?></div><button type="button" class="btn btn-danger" onclick="removerAnimalVacina(this)" title="Remover animal"><i class="fa fa-minus"></i></button></div></template>
<?php renderCadastroVacinaModal('vacina');?>
