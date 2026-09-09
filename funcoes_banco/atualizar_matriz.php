<?

function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}


set_time_limit(9999999999999);
include "../_config.php";


$femea = DBRead('animais',"WHERE sexo = 'Fêmea'");
foreach ($femea as $femea_){
  $id_femea = $femea_['id'];
  $crias = DBRead('animais', "WHERE mae = '$id_femea' AND terceiro_mae = '0'");
  if($crias[0]['id'] > 0){
    $tipo2=$tipo3=$tipo4=$tipo5=$qtd_avaliadas=0;
    $qtd_crias = count($crias);
    foreach ($crias as $crias_){
      if($crias_['tipo'] > 0){
        $id_cria = $crias_['id'];
        $teste = DBRead('avaliacao', "WHERE id_animal = '$id_cria'");
        if($teste[0]['id'] > 0){
          $qtd_avaliadas++;
          if($crias_['tipo'] == 2){ $tipo2++;}
          if($crias_['tipo'] == 3){ $tipo3++;}
          if($crias_['tipo'] == 4){ $tipo4++;}
          if($crias_['tipo'] == 5){ $tipo5++;}
        }
    }
    }
    $nota = (($tipo5*10) + ($tipo4*7) + ($tipo3*4))/$qtd_avaliadas;
    $dados = array(
        'id_femea'	=> $id_femea,
        'qtd_crias' => $qtd_crias,
        'qtd_avaliadas'  => $qtd_avaliadas,
        'tipo2' => $tipo2,
        'tipo3' => $tipo3,
        'tipo4' => $tipo4,
        'tipo5' => $tipo5,
        'nota'  => $nota
    );
    DBCreate('matriz', $dados);
  }
}
?>
