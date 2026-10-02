<?php
require_once __DIR__ . "/../_config.php";
header("Content-Type: text/html; charset=UTF-8");

$nomeInformado = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
if ($nomeInformado === '') {
  echo "<script>alert('Informe o nome completo do comprador.'); history.back();</script>";
  exit;
}
$nome = DBEscape($nomeInformado);
$comprador = DBRead('mercado', "WHERE nome = '$nome'");

// TESTE EVENTO
if(($comprador[0]['id'] ?? 0) > 0){
  echo "<script type=\"text/javascript\"> alert(\"Comprador já existe.Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$dados = array(
	'nome'	=> $nomeInformado,
	'celular1'	=> $_POST['celular'],
  'telefone2' => '',
  'email'	=> $_POST['email'],
  'cpf'  => $_POST['cpf'],
  'cod_criador'  => $_POST['cod'],
  'cidade'  => $_POST['cidade'],
  'estado'  => $_POST['estado']
);

DBcreate('mercado', $dados);
$comprador = DBRead('mercado', "WHERE nome = '$nome'");
$id_comprador = $comprador[0]['id'];

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=comprador&id_comprador=$id_comprador'>";
}
?>
