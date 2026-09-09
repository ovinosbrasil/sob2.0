<?php 
ob_start();  
session_start();
$banco = $_SESSION['banco'];
$login = $_SESSION['login'];


define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'siste870_sob');
define('DB_PASSWORD', 'sob123');
define('DB_DATABASE', 'siste870_'.$banco);
define('DB_CHARSET', 'latin1');

/*
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', $banco);
define('DB_CHARSET', 'latin1');
*/
require 'mysqli/_conexao.php';
require 'mysqli/_database.php';


?>
