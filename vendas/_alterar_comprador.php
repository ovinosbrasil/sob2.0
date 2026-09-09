<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../_config.php";

$nome = $_POST['nome'];
$id_comprador = $_GET['id_comprador'];
$comprador = DBRead('mercado', "WHERE nome = '$nome' AND id != '$id_comprador'");

// TESTE EVENTO
if($comprador[0]['id'] > 0){
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
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=comprador&id_comprador=$id_comprador'>";
}
?>
