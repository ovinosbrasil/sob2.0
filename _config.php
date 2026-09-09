<?php 
ob_start();  
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['banco']) || empty($_SESSION['login'])) {
    header('Location: /index.php');
    exit;
}
$banco = $_SESSION['banco'];
$login = $_SESSION['login'];


$databaseName = (getenv("DB_PREFIX") !== false ? getenv("DB_PREFIX") : "siste870_") . $banco;
require __DIR__ . "/mysqli/environment.php";

require 'mysqli/_conexao.php';
require 'mysqli/_database.php';


?>
