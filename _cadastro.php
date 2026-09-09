<?
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'siste870_sob');
define('DB_PASSWORD', 'sob123');
define('DB_DATABASE', 'siste870_sob');
define('DB_CHARSET', 'latin1');

/*
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'sob');
define('DB_CHARSET', 'latin1');
*/
require 'mysqli/_conexao.php';
require 'mysqli/_database.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];

$dados = array(
	'nome'	=> $nome,
	'email'		=> $email,
	'celular'		=> $telefone,
);

DBCreate('user', $dados);
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=index.php'>";
?>
