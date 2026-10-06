<?php
require_once __DIR__ . '/../_config.php';
$idAplicacao=filter_var($_GET['id_vacina']??null,FILTER_VALIDATE_INT);
if($idAplicacao>0 && DBRead('vacinas',"WHERE id='".(int)$idAplicacao."'") && DBDelete('vacinas',"id='".(int)$idAplicacao."'")){
    $_SESSION['alerta_vacina']=array('tipo'=>'success','titulo'=>'Sucesso!','mensagem'=>'Registro de vacina excluído com sucesso.');
}else{
    $_SESSION['alerta_vacina']=array('tipo'=>'warning','titulo'=>'Atenção!','mensagem'=>'Não foi possível excluir o registro de vacina. Atualize a página e tente novamente.');
}
header('Location: ../geral.php?pg=vacinas',true,303);
exit;
