<?php
require_once __DIR__ . '/../../_config.php';
$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);
$vacina=$id>0?DBRead('vacinas',"WHERE id='".(int)$id."' LIMIT 1"):null;
$destino='../../geral.php?pg=vacinas';
if($vacina){
    $destino='../../geral.php?pg=animal&id_animal='.(int)$vacina[0]['id_animal'].'&aba=vacina';
}
if($vacina && DBDelete('vacinas',"id='".(int)$id."'")){
    $_SESSION['alerta_vacina']=array('tipo'=>'success','titulo'=>'Sucesso!','mensagem'=>'Registro de vacina excluído com sucesso.');
}else{
    $_SESSION['alerta_vacina']=array('tipo'=>'warning','titulo'=>'Atenção!','mensagem'=>'Não foi possível excluir o registro de vacina. Atualize a página e tente novamente.');
}
header('Location: '.$destino,true,303);
exit;
