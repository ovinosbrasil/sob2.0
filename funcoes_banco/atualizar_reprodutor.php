<?

$macho = DBRead('animais',"WHERE sexo = 'Macho'");
foreach ($macho as $macho_){
  $id_macho = $macho_['id'];
  $crias = DBRead('animais', "WHERE pai = '$id_macho' AND terceiro_pai = '0'");
  if($crias[0]['id'] > 0){
    $tipo2=$tipo3=$tipo4=$tipo5=$qtd_avaliadas=0;
    $qtd_crias = count($crias);
    foreach ($crias as $crias_){
      if($crias_['tipo'] > 0){
        $qtd_avaliadas++;
        if($crias_['tipo'] == 2){ $tipo2++;}
        if($crias_['tipo'] == 3){ $tipo3++;}
        if($crias_['tipo'] == 4){ $tipo4++;}
        if($crias_['tipo'] == 5){ $tipo5++;}
      }
    }
    $nota = (($tipo5*10) + ($tipo4*7) + ($tipo3*4))/$qtd_avaliadas;
    $dados = array(
    	'id_macho'	=> $id_macho,
      'qtd_crias' => $qtd_crias,
      'qtd_avaliadas'  => $qtd_avaliadas,
      'tipo2' => $tipo2,
      'tipo3' => $tipo3,
      'tipo4' => $tipo4,
      'tipo5' => $tipo5,
      'nota'  => $nota
    );
    DBCreate('reprodutor', $dados);
  }
}
?>
