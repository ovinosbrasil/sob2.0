<?php 
ob_start();  
require_once __DIR__ . '/includes/sessao.php';
exigirSessaoSob();
$banco = $_SESSION['banco'];
$login = $_SESSION['login'];


$databaseName = (getenv("DB_PREFIX") !== false ? getenv("DB_PREFIX") : "siste870_") . $banco;
require __DIR__ . "/mysqli/environment.php";

require 'mysqli/_conexao.php';
require 'mysqli/_database.php';


?>
