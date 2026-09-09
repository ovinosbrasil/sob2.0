<?php
require __DIR__ . "/mysqli/environment.php";

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
