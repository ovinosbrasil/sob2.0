<?php
require_once __DIR__ . '/../../_config.php';
function inteiroUltrassom($nome) { $valor=filter_input(INPUT_GET,$nome,FILTER_VALIDATE_INT); return $valor===false||$valor===null?0:(int)$valor; }
$controleId=inteiroUltrassom('id_lote'); $loteId=inteiroUltrassom('id_lote2'); $reproducao=inteiroUltrassom('reproducao'); $status=inteiroUltrassom('status');
$tipo=isset($_GET['tipo'])&&(string)$_GET['tipo']==='1'?1:0; $idAnimal=inteiroUltrassom('id_animal'); $terceiro=isset($_GET['terceiro'])&&(string)$_GET['terceiro']==='1'?1:0;
$mapa=array(0=>array('tabela'=>'monta_controle','fk'=>'id_monta'),1=>array('tabela'=>'inseminacao_controle','fk'=>'id_lote'),2=>array('tabela'=>'transplante_controle','fk'=>'id_lote'));
$_SESSION['alerta_ultrassom'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Registro ou status de ultrassom inválido. Atualize a página e tente novamente.');
if($controleId>0&&$loteId>0&&isset($mapa[$reproducao])&&in_array($status,array(1,2),true)){
  $config=$mapa[$reproducao];
  $controle = DBRead($config['tabela'], "WHERE id = '{$controleId}' AND {$config['fk']} = '{$loteId}'");
  if ($controle) {
  DBUpdate($config['tabela'],array('ultrassom'=>$status,'data_ultrassom'=>(new DateTimeImmutable('now',new DateTimeZone('America/Bahia')))->format('Y-m-d')),"id = '{$controleId}' AND {$config['fk']} = '{$loteId}'");
  $todos=DBRead($config['tabela'],"WHERE {$config['fk']} = '{$loteId}'"); $todos=is_array($todos)?$todos:array(); $positivos=0;
  foreach($todos as $registro) if((int)($registro['ultrassom']??0)===1)$positivos++;
  $percentual=count($todos)?($positivos*100)/count($todos):0;
  DBUpdate('lotes_reproducao',array('ultrassom'=>$percentual),"id_lote = '{$loteId}' AND tipo = '{$reproducao}'");
  $_SESSION['alerta_ultrassom'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Ultrassom alterado para ' . ($status === 1 ? 'positivo' : 'negativo') . ' com sucesso.');
  }
}
$parametros=array('pg'=>'lista_ultrassom','tipo'=>$tipo);
if($tipo===1){$parametros['id_lote']=$loteId;$parametros['reproducao']=$reproducao;}else{$parametros['id_animal']=$idAnimal;$parametros['terceiro']=$terceiro;}
header('Location: ../../geral.php?'.http_build_query($parametros),true,303); exit;
