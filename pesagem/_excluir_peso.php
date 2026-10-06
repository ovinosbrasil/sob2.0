<?php
require_once __DIR__ . '/../_config.php';
$idPeso=filter_var($_GET['id_peso']??null,FILTER_VALIDATE_INT);
$idAnimal=filter_var($_GET['id_animal']??null,FILTER_VALIDATE_INT);
$idAnimal=$idAnimal>0?(int)$idAnimal:0;
if($idPeso>0&&$idAnimal&&DBRead('pesagem',"WHERE id='".(int)$idPeso."' AND id_animal='$idAnimal' LIMIT 1")&&DBDelete('pesagem',"id='".(int)$idPeso."' AND id_animal='$idAnimal'")){
    $_SESSION['alerta_pesagem']=array('tipo'=>'success','titulo'=>'Sucesso!','mensagem'=>'Pesagem excluída com sucesso.');
}else{
    $_SESSION['alerta_pesagem']=array('tipo'=>'warning','titulo'=>'Atenção!','mensagem'=>'Não foi possível excluir a pesagem. Atualize a página e tente novamente.');
}
header('Location: ../geral.php?pg=pesagem&id_animal='.$idAnimal,true,303);
exit;
