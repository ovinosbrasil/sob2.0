<?php
require_once __DIR__ . '/../_config.php';
$idAplicacao=filter_var($_GET['id_vacina']??null,FILTER_VALIDATE_INT);
if($idAplicacao)DBDelete('vacinas',"id='".(int)$idAplicacao."'");
header('Location: ../geral.php?pg=vacinas',true,303);
exit;
