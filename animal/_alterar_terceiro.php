<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../_config.php";
$nome = $_POST['nome_animal'];
$id_animal = $_GET['id_animal'];
$sql = DBRead('terceiros', "WHERE nome = '$nome' AND id != '$id_animal'");
if($sql[0]['id'] > 0){
          echo "<script type=\"text/javascript\"> alert(\"Nome do animal já existe, tente novamente.\"); </script>
			    <script language='javascript'>history.back()</script>";
}else{
$dados = array(
	'nome'	=> str_replace("'", '"',$_POST['nome_animal']),
	'fbb'	=> str_replace("'", '"',$_POST['fbb']),
	'sexo'	=> $_POST['sexo'],
	'pai'	=>  str_replace("'", '"',$_POST['pai']),
	'mae'	=>  str_replace("'", '"',$_POST['mae']),
	'raca'	=> $_POST['raca'],
);

DBUpdate('terceiros', $dados, "id = '$id_animal'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=terceiro&id_animal=$id_animal'>";
}
?>
