<?
include "../_config.php";
foreach (array('nome', 'cpf', 'celular', 'fazenda') as $campo) {
    if (!isset($_POST[$campo]) || !is_string($_POST[$campo]) || trim($_POST[$campo]) === '') {
        $_SESSION['alerta_perfil'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Preencha os campos obrigatórios.');
        header('Location: ../geral.php?pg=perfil');
        exit;
    }
}
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
if (!DBUpdate('admin', $dados)) {
    $_SESSION['alerta_perfil'] = array('tipo'=>'danger', 'titulo'=>'Erro!', 'mensagem'=>'Não foi possível alterar o registro.');
    header('Location: ../geral.php?pg=perfil');
    exit;
}
$_SESSION['alerta_perfil'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Registro alterado com sucesso.');
header('Location: ../geral.php?pg=perfil');
exit;
?>
