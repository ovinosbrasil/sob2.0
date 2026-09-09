<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../_config.php";
$id_animal = $_GET['id_animal'];
$id_evento = $_GET['id_evento'];

    $dados = array(
    	'leilao'	=> 0
    );
    DBUpdate('animais_evento', $dados, "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
    echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";

?>
