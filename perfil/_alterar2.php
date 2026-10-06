<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['banco']) || empty($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
require dirname(__DIR__) . "/mysqli/environment.php";

require '../mysqli/_conexao.php';
require '../mysqli/_database.php';


$login = $_GET['login'];
$senha = $_GET['senha'];

$dados = array(
	'senha'		=> $senha
);
$alterado = DBUpdate('user', $dados, "login = '$login'");
$_SESSION['alerta_perfil'] = $alterado
    ? array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Registro alterado com sucesso.')
    : array('tipo'=>'danger', 'titulo'=>'Erro!', 'mensagem'=>'Não foi possível concluir a alteração do registro.');
header('Location: ../geral.php?pg=perfil');
exit;
?>
