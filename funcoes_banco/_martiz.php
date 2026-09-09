<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}


set_time_limit(9999999999999);
include "../_config.php";


include "atualizar_tipo.php";
include "atualizar_matriz.php";
include "atualizar_intervalo_matriz.php";
include "atualizar_mortes_matriz.php";
include "atualizar_pesagem_matriz.php";
include "atualizar_vendas_matriz.php";
var_dump('a');
?>
