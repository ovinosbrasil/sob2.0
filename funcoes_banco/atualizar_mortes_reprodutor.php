<?

//$ano = date('Y')-5;
$data_final = "2016-01-01";

$reprodutor = DBRead('reprodutor');
foreach ($reprodutor as $reprodutor_){
  $id_macho = $reprodutor_['id_macho'];
  $morte = DBRead('animais', "WHERE pai = '$id_macho' AND status = '1' AND causa_da_perda = 'Nascimento' AND terceiro_pai = '0' AND data_de_nascimento >= '$data_final'");
  $qtd_mortes = count($morte);
  if($morte[0]['id'] > 0){
    $qtd_mortes = ($qtd_mortes*100)/$reprodutor_['qtd_crias'];
  }else{ $qtd_mortes=0; }
  $dados = array(
    'qtd_mortes' => $qtd_mortes
  );
    DBUpdate('reprodutor', $dados, "id_macho = '$id_macho'");
}
?>
