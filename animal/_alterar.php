<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

set_time_limit(9999999999999);
include "../_config.php";
$id_animal = $_GET['id_animal'];
$nome = $_POST['nome_animal'];


//PAI E MAE ANTIGOS
$animal = DBRead('animais', "WHERE id = '$id_animal'");
$id_pai_antigo = $animal[0]['pai'];
$id_mae_antigo = $animal[0]['mae'];
//FIM PAI E MAE ANTIGOS


//ATAULIZAR ANIMAL
$sql = DBRead('animais', "WHERE nome = '$nome' AND id != '$id_animal'");
if($sql[0]['id'] > 0){
          echo "<script type=\"text/javascript\"> alert(\"Nome do animal já existe, tente novamente.\"); </script>
			    <script language='javascript'>history.back()</script>";
}else{
$pai = $_POST['pai'];
$verifica_pai = DBRead('animais', "WHERE nome = '$pai' AND sexo = 'Macho'");
$verifica_pai_terceiro = DBRead('terceiros', "WHERE nome = '$pai' AND sexo = 'Macho'");

$mae = $_POST['mae'];
$verifica_mae = DBRead('animais', "WHERE nome = '$mae' AND sexo = 'Fêmea'");
$verifica_mae_terceiro = DBRead('terceiros', "WHERE nome = '$mae' AND sexo = 'Fêmea'");

if($verifica_pai[0]['id'] > 0){ $pai = $verifica_pai[0]['id']; $terceiro_pai = 0;}
if($verifica_pai_terceiro[0]['id'] > 0){ $pai = $verifica_pai_terceiro[0]['id']; $terceiro_pai = 1;}
if($verifica_mae[0]['id'] > 0){ $mae = $verifica_mae[0]['id']; $terceiro_mae = 0;}
if($verifica_mae_terceiro[0]['id'] > 0){ $mae = $verifica_mae_terceiro[0]['id']; $terceiro_mae = 1;}

$data = $_POST['data_de_nascimento'];
include "../funcoes_data/data.php";
$data_de_nascimento = $data;

$data = $_POST['data_de_entrada'];
include "../funcoes_data/data.php";
$data_de_entrada = $data;

$dados = array(
	'nome'	=> $_POST['nome_animal'],
	'tatuagem'	=> $_POST['tatuagem'],
	'fbb'		=> $_POST['fbb'],
	'data_de_entrada'	=> $data_de_entrada,
	'data_de_nascimento'		=> $data_de_nascimento,
	'sexo'	=> $_POST['sexo'],
	'pai'			=> $pai,
	'mae'			=> $mae,
	'raca'		=> $_POST['raca'],
	'observacoes'    => $_POST['observacoes'],
  'terceiro_pai'    =>  $terceiro_pai,
  'terceiro_mae'    =>  $terceiro_mae,
  'preco_de_compra'  => str_replace("," , "" , $_POST['preco_de_compra'])
);
DBUpdate('animais', $dados, "id = '$id_animal'");
$dados = array(
	'pai'	=> $pai
);
DBUpdate('avaliacao', $dados, "id_animal = '$id_animal'");
//FIM ATUALIZAR ANIMAL

//RANKING REPRODUTOR
$id_pai = $id_pai_antigo;
include "_ranking_reprodutor.php";
echo'a-';
$id_pai = $pai;
include "_ranking_reprodutor.php";
echo 'b';
$novo_mae = $mae;
$id_mae = $id_mae_antigo;
include "_ranking_matriz.php";
$id_mae = $novo_mae;
include "_ranking_matriz.php";
//FIM RANKING REPRODUTOR
echo 'c';
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=animal&id_animal=$id_animal'>";
}
?>
