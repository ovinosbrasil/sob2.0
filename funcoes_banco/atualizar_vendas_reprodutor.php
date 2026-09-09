<?


$reprodutor = DBRead('reprodutor');
foreach ($reprodutor as $reprodutor_){
  $id_macho = $reprodutor_['id_macho'];
  $vendas = DBRead('animais', "WHERE status = '2' AND pai = '$id_macho' AND terceiro_pai = '0'");
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
    DBUpdate('reprodutor', $dados, "id_macho = '$id_macho'");
  }
?>
