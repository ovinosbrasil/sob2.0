<?php
session_start();

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


//DADOS DE LOGIN
$login = $_POST['login'];
$senha = $_POST['senha'];
//FIM DADOS DE LOGIN



$sql = DBRead('user', "WHERE login = '$login'");
	if($sql){
		if($sql[0]['senha'] == $senha){
	 		$_SESSION['banco'] = $sql[0]['cookie'];
			$_SESSION['login'] = $sql[0]['login'];
			$_SESSION['data_expira'] = $sql[0]['log_fim'];
			echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=geral.php'>";
		}else{
			echo "<script type=\"text/javascript\">
			 alert(\"Senha incorreta, tente novamente.\");
			 window.history.go(-1);
			 </script>";
		}
    }else{
       echo " <script type=\"text/javascript\">
       alert(\"Login incorreto, tente novamentee.\");
       window.history.go(-1);
       </script>";
  }
?>
