<?

function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

set_time_limit(9999999999999);
include "../_config.php";

$matriz = DBRead('matriz');
foreach ($matriz as $matriz_){
  $id_femea = $matriz_['id_femea'];
  $vendas = DBRead('animais', "WHERE status = '2' AND mae = '$id_femea' AND terceiro_mae = '0'  AND data_de_nascimento > '2011-01-01'");
  $qtd_macho=$qtd_femea=$total_macho=$total_femea=$valor_total=0;
  foreach ($vendas as $vendas_) {
    $id_cria = $vendas_['id'];
    $valor = DBRead('vendas', "WHERE id_animal = '$id_cria' AND data > '2011-01-01'");
    if($valor[0]['id'] > 0){
    if($vendas_['sexo'] == 'Macho'){ $total_macho = $total_macho+$valor[0]['preco_de_venda']; $qtd_macho++;}
    if($vendas_['sexo'] == 'Fêmea'){ $total_femea = $total_femea+$valor[0]['preco_de_venda']; $qtd_femea++;}
    $valor_total = $valor_total+$valor[0]['preco_de_venda'];
  }}
    $media_macho = $total_macho/$qtd_macho;
    $media_femea = $total_femea/$qtd_femea;
    $media_total = $valor_total/($qtd_macho+$qtd_femea);
    $qtd_total = $qtd_macho+$qtd_femea;
    $dados = array(
    	'qtd_vendas'	=> $qtd_total,
      'venda_macho' => $media_macho,
      'venda_femea'  => $media_femea,
      'venda_geral' => $media_total
    );
    DBUpdate('matriz', $dados, "id_femea = '$id_femea'");
  }
?>
