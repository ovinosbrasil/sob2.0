<?php
include __DIR__ . "/../_config.php";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
$id_animal = $_GET['id_animal'];
$id_evento = $_GET['id_evento'];


$teste = DBRead('animais_evento', "WHERE id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
if(empty($teste[0]['id'])){

  $dados = array(
    'id_animal'	=> $id_animal,
    'id_julgamento'	=> $id_evento,
    'leilao' => 0,
    'julgamento' => 0,
  );
  DBcreate('animais_evento', $dados);
}

  $teste = DBRead('julgamento_controle', "WHERE id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
  if(empty($teste[0]['id'])){
      $animal = DBRead('animais', "WHERE id = '$id_animal'");
      $id_pai = $animal[0]['pai'];
      $id_mae = $animal[0]['mae'];

      $evento = DBRead('julgamento', "WHERE id = '$id_evento'");
      $data = $evento[0]['data'];
      include __DIR__ . "/../funcoes_data/categorias.php";
      $idade = calcularIdadeMesesDias($animal[0]['data_de_nascimento'], $data);
      if (!$idade) {
        $meses = 0; $dias_final = 0; $categoria = 0;
      } else {
        $meses = $idade['meses'];
        $dias_final = $idade['dias'];
        $categoria = determinarCategoriaPorIdade($meses, $dias_final);
      }

      $dados = array(
       	'id_animal' 	=> $id_animal,
       	'id_julgamento' 	=> $id_evento,
        'categoria' 	=> $categoria,
        'meses'  => $meses,
        'pai'  => $id_pai,
        'mae'  => $id_mae
      );

      DBcreate('julgamento_controle', $dados);

      if($animal[0]['terceiro_pai'] == 0){
        $progenie_pai = DBRead('progene', "WHERE id_lote = '$id_evento' AND id_animal = '$id_pai'");
        if(!empty($progenie_pai[0]['id'])){
          $qtd = $progenie_pai[0]['qtd']+1;
          $dados = array(
          	'qtd'	=> $qtd
          );
          DBUpdate('progene', $dados, "id_lote = '$id_evento' AND id_animal = '$id_pai'");
        }else{
          $dados = array(
          	'qtd'	=> 1,
            'id_animal' => $id_pai,
            'sexo'  => "M",
            'id_lote' => $id_evento,
            'categoria' => 0,
            'classe' => 0
          );
          DBcreate('progene', $dados);
        }
      }

      if($animal[0]['terceiro_mae'] == 0){
        $progenie_mae = DBRead('progene', "WHERE id_lote = '$id_evento' AND id_animal = '$id_mae'");
        if(!empty($progenie_mae[0]['id'])){
          $qtd = $progenie_mae[0]['qtd']+1;
          $dados = array(
          	'qtd'	=> $qtd
          );
          DBUpdate('progene', $dados, "id_lote = '$id_evento' AND id_animal = '$id_mae'");
        }else{
          $dados = array(
          	'qtd'	=> 1,
            'id_animal' => $id_mae,
            'sexo'  => "F",
            'id_lote' => $id_evento,
            'categoria' => 0,
            'classe' => 0
          );
          DBcreate('progene', $dados);
        }
      }

  }

  DBUpdate('animais_evento', array('julgamento' => 1), "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=julgamento&id_exposicao=$id_evento'>";

?>
