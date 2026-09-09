<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

set_time_limit(9999999999999);
include "../_config.php";


//include "atualizar_reprodutor.php";
include "atualizar_pais_avaliacao.php";
include "atualizar_pesagem_reprodutor.php";
include "atualizar_vendas_reprodutor.php";
include "atualizar_mortes_reprodutor.php";
?>
