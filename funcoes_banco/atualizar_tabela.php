<?

define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'siste870_sob');
define('DB_PASSWORD', 'sob123');
define('DB_DATABASE', 'siste870_fg');
define('DB_CHARSET', 'latin1');


include "../_config.php";


$tabela = DBRead('animal');
foreach($tabela as $tabela_){
  if($tabela_['situacao'] == 'C'){ $confirmacao = 1; }else{ $confirmacao = 0; }
  if($tabela_['sexo'] == 'Macho'){ $sexo = 'Macho'; }else{ $sexo = 'Fêmea'; }
  $data_atual = $tabela_['nascimento'];
  $data = '0';
  $data['0'] = $data_atual['6'];
  $data['1'] = $data_atual['7'];
  $data['2'] = $data_atual['8'];
  $data['3'] = $data_atual['9'];
  $data['4'] = "-";
  $data['5'] = $data_atual['3'];
  $data['6'] = $data_atual['4'];
  $data['7'] = "-";
  $data['8'] = $data_atual['0'];
  $data['9'] = $data_atual['1'];
  $dados = array(
    'nome'	=> $tabela_['nome'],
    'pai'	=> $tabela_['pai'],
    'mae'	=> $tabela_['mae'],
    'sexo'	=> $sexo,
    'fbb'	=> $tabela_['fbb'],
    'data_de_nascimento'	=> $data,
    'raca'  => 'Dorper'
  );
  if($tabela_['nome']){
      DBcreate('animais', $dados);
  }
}

$animal = DBRead('animais');
foreach($animal as $animal_){
$id_animal = $animal_['id'];
$mae = $animal_['mae'];
$mae_fbb = $animal_['fbb'];
$mae_tatuagem = $animal_['tatuagem'];
$mae_ = DBRead('animais',"WHERE nome = '$mae'");
$id_mae = $mae_[0]['id'];


  if($id_mae){
    $dados = array(
      'mae'	=> $id_mae
    );
    DBUpdate('animais', $dados, "id = '$id_animal'");

  }else{
      $dados = array(
        'nome'	=> $mae,
        'raca'	=> 'Droper',
        'sexo'	=> 'Fêmea',
      );
      $terceiro = DBRead('terceiros', "WHERE nome = '$mae'");
      $id_terceiro = $terceiro[0]['id'];
      if(!$id_terceiro){
      DBCreate('terceiros', $dados);
      }
      $terceiro = DBRead('terceiros', "WHERE nome = '$mae'");
      $id_terceiro = $terceiro[0]['id'];
      $dados = array(
        'mae'	=> $id_terceiro,
        'terceiro_mae'  => 1
      );
      DBUpdate('animais', $dados, "id = '$id_animal'");
    }

      $pai = $animal_['pai'];
      $pai_ = DBRead('animais', "WHERE nome = '$pai'");
      $id_pai = $pai_[0]['id'];
      $pai_fbb = $pai_[0]['fbb'];
      $pai_tatuagem = $pai_[0]['tatuagem'];
      if($id_pai){
        $dados = array(
          'pai'	=> $id_pai
        );
        DBUpdate('animais', $dados, "id = '$id_animal'");

      }else{
          $dados = array(
            'nome'	=> $pai,
            'raca'	=> 'Droper',
            'sexo'	=> 'Macho',
          );
          $terceiro = DBRead('terceiros', "WHERE nome = '$pai'");
          $id_terceiro = $terceiro[0]['id'];
          if(!$id_terceiro){
          DBCreate('terceiros', $dados);
          }
          $terceiro = DBRead('terceiros', "WHERE nome = '$pai'");
          $id_terceiro = $terceiro[0]['id'];
          $dados = array(
            'pai'	=> $id_terceiro,
            'terceiro_pai'  => 1
          );
          DBUpdate('animais', $dados, "id = '$id_animal'");
}


  $nome = $animal_['nome'];
  $nome = strtoupper($nome);
  echo $nome;
  $id_animal = $animal_['id'];
  $dados = array(
    'nome'	=> $nome,
  );
  DBUpdate('animais', $dados, "id = '$id_animal'");
}
?>
