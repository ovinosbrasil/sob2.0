<?
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'siste870_sob');
define('DB_PASSWORD', 'sob123');
define('DB_DATABASE', 'siste870_sob');
define('DB_CHARSET', 'latin1');

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
