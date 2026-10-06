<?php
require_once __DIR__ . "/../../_config.php";

$nome = $_POST['nome_animal'];
$sql = DBRead('terceiros', "WHERE nome = '$nome'");
if($sql[0]['id'] > 0){
          echo "<script type=\"text/javascript\"> alert(\"Nome do animal já existe, tente novamente.\"); </script>
			    <script language='javascript'>history.back()</script>";
}else{
$dados = array(
	'nome'	=> str_replace("'", '"',$_POST['nome_animal']),
	'tatuagem' => '',
	'fbb'		=> str_replace("'", '"',$_POST['fbb']),
	'sexo'	=> $_POST['sexo'],
	'pai'			=>  str_replace("'", '"',$_POST['pai']),
	'mae'			=>  str_replace("'", '"',$_POST['mae']),
	'raca'		=> $_POST['raca'],
);

DBCreate('terceiros', $dados);

$id_animal = DBRead('terceiros', "WHERE nome = '$nome'");
$id_animal = $id_animal[0]['id'];

$_SESSION['alerta_cadastro_animal'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Animal cadastrado com sucesso.');
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=terceiro&id_animal=$id_animal'>";
}
?>
