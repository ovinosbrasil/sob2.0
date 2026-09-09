<?
set_time_limit(9999999999999);
include "../_config.php";

/*
$monta = DBRead('monta');
foreach ($monta as $monta_) {
  $qtd=$ultrassom=$nascimento=0;
  $id_monta = $monta_['id'];
  $dados = DBRead('monta_controle', "WHERE id_monta = '$id_monta'");
  $ultrassom = DBRead('monta_controle', "WHERE id_monta = '$id_monta' AND ultrassom = '1'");
  $nascimento = DBRead('monta_controle', "WHERE id_monta = '$id_monta' AND status_nascimento = '1'");
  $qtd = count($dados);
  if($ultrassom[0]['id'] > 0){
    $ultrassom = count($ultrassom);
    $ultrassom = ($ultrassom*100)/$qtd;
  }else{
    $ultrassom = 0;
  }
  if($nascimento[0]['id'] > 0){
    $nascimento = count($nascimento);
    $nascimento = ($nascimento*100)/$qtd;
  }else{
    $nascimento = 0;
  }
  $dados = array(
    'id_lote' => $id_monta,
    'femeas' => $qtd,
    'ultrassom' => $ultrassom,
    'crias'   =>  $nascimento,
    'tipo'   => 0
  );
  DBCreate('lotes_reproducao', $dados);
}
*/
/*
$ia = DBRead('inseminacao');
foreach ($ia as $ia_) {
  $qtd=$ultrassom=$nascimento=0;
  $id_ia = $ia_['id'];
  $dados = DBRead('inseminacao_controle', "WHERE id_lote = '$id_ia'");
  $ultrassom = DBRead('inseminacao_controle', "WHERE id_lote = '$id_ia' AND ultrassom = '1'");
  $nascimento = DBRead('inseminacao_controle', "WHERE id_lote = '$id_ia' AND status_nascimento = '1'");
  $qtd = count($dados);
  if($ultrassom[0]['id'] > 0){
    $ultrassom = count($ultrassom);
    $ultrassom = ($ultrassom*100)/$qtd;
  }else{
    $ultrassom = 0;
  }
  if($nascimento[0]['id'] > 0){
    $nascimento = count($nascimento);
    $nascimento = ($nascimento*100)/$qtd;
  }else{
    $nascimento = 0;
  }
  $dados = array(
    'id_lote' => $id_ia,
    'femeas' => $qtd,
    'ultrassom' => $ultrassom,
    'crias'   =>  $nascimento,
    'tipo'   => 1
  );
  DBCreate('lotes_reproducao', $dados);
}
*/

$te = DBRead('transplante');
foreach ($te as $te_) {
  $qtd=$ultrassom=$nascimento=0;
  $id_te = $te_['id'];
  $dados = DBRead('transplante_controle', "WHERE id_lote = '$id_te'");
  $ultrassom = DBRead('transplante_controle', "WHERE id_lote = '$id_te' AND ultrassom = '1'");
  $nascimento = DBRead('transplante_controle', "WHERE id_lote = '$id_te' AND status_nascimento = '1'");
  $qtd = count($dados);
  if($ultrassom[0]['id'] > 0){
    $ultrassom = count($ultrassom);
    $ultrassom = ($ultrassom*100)/$qtd;
  }else{
    $ultrassom = 0;
  }
  if($nascimento[0]['id'] > 0){
    $nascimento = count($nascimento);
    $nascimento = ($nascimento*100)/$qtd;
  }else{
    $nascimento = 0;
  }
  $dados = array(
    'id_lote' => $id_te,
    'femeas' => $qtd,
    'ultrassom' => $ultrassom,
    'crias'   =>  $nascimento,
    'tipo'   => 2
  );
  DBCreate('lotes_reproducao', $dados);
}
?>
