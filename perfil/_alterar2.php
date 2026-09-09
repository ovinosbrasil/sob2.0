<?php
require dirname(__DIR__) . "/mysqli/environment.php";

require '../mysqli/_conexao.php';
require '../mysqli/_database.php';


$login = $_GET['login'];
$senha = $_GET['senha'];

$dados = array(
	'senha'		=> $senha
);
DBUpdate('user', $dados, "login = '$login'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=perfil'>";
?>
