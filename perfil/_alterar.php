<?
include "../_config.php";
$login = $_POST['login'];
$senha = $_POST['senha'];
$responsavel = str_replace("'", '"',$_POST['nome']);
$email = str_replace("'", '"',$_POST['email']);
$fazenda = str_replace("'", '"',$_POST['fazenda']);
$prefixo = str_replace("'", '"',$_POST['prefixo']);
$tecnico = str_replace("'", '"',$_POST['tecnico']);
$end = str_replace("'", '"',$_POST['end']);
$tecnico = str_replace("'", '"',$_POST['tecnico']);
$cidade = str_replace("'", '"',$_POST['cidade']);
$cod_tecnico = str_replace("'", '"',$_POST['cod_tecnico']);


$dados = array(
	'responsavel'		=> $responsavel,
	'email'				=> $email,
	'telefone'			=> $_POST['telefone'],
	'celular'			=> $_POST['celular'],
	'cpf'		=> $_POST['cpf'],
	'senha'	=> $_POST['senha'],
	'fazenda'				=> $fazenda,
	'prefixo'				=> $prefixo,
	'raca'				=> $_POST['raca'],
	'cod_rebanho'				=> $_POST['cod_rebanho'],
	'cod'				=> $_POST['cod'],
	'tecnico'				=> $tecnico,
	'end'	=> $end,
	'num'	=> $_POST['num'],
	'cidade'	=> $cidade,
	'estado'	=> $_POST['estado'],
	'cep'	=> $_POST['cep'],
	'cod_tecnico'	=> $cod_tecnico

);
DBUpdate('admin', $dados);
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=_alterar2.php?login=$login&senha=$senha'>";
?>
