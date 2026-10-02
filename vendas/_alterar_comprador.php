<?php
require_once __DIR__ . "/../_config.php";
header("Content-Type: text/html; charset=UTF-8");

$nome = DBEscape($_POST['nome'] ?? '');
$id_comprador = (int)($_GET['id_comprador'] ?? 0);
$comprador = DBRead('mercado', "WHERE nome = '$nome' AND id != '$id_comprador'");

// TESTE EVENTO
if(($comprador[0]['id'] ?? 0) > 0){
  echo "<script type=\"text/javascript\"> alert(\"Comprador já existe.Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$dados = array(
	'nome'	=> $_POST['nome'],
	'celular1'	=> $_POST['celular'],
  'email'	=> $_POST['email'],
  'cpf'  => $_POST['cpf'],
  'cod_criador'  => $_POST['cod'],
  'cidade'  => $_POST['cidade'],
  'estado'  => $_POST['estado']
);

DBUpdate('mercado', $dados, "id = '$id_comprador'");
header("Location: ../geral.php?pg=comprador&id_comprador=$id_comprador", true, 303);
exit;
}
