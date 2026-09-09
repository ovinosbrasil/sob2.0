<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

include "../../_config.php";

$nome = $_POST['nome_animal'];

$sql = DBRead('animais', "WHERE nome = '$nome'");
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

if($verifica_pai[0]['id'] > 0){ $id_pai = $verifica_pai[0]['id']; $terceiro_pai = 0;}
if($verifica_pai_terceiro[0]['id'] > 0){ $id_pai = $verifica_pai_terceiro[0]['id']; $terceiro_pai = 1;}
if($verifica_mae[0]['id'] > 0){ $id_mae = $verifica_mae[0]['id']; $terceiro_mae = 0;}
if($verifica_mae_terceiro[0]['id'] > 0){ $id_mae = $verifica_mae_terceiro[0]['id']; $terceiro_mae = 1;}

if(!$id_pai > 0){
  echo "<script type=\"text/javascript\"> alert(\"Pai não existe, tente novamente.\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

if(!$id_mae > 0){
  echo "<script type=\"text/javascript\"> alert(\"Mãe não existe, tente novamente.\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$data = $_POST['data_de_nascimento'];
include "../../funcoes_data/data.php";
$data_de_nascimento = $data;

$data = $_POST['data_de_entrada'];
include "../../funcoes_data/data.php";
$data_de_entrada = $data;


$dados = array(
	'nome'	=> str_replace("'", '"',$_POST['nome_animal']),
	'tatuagem'	=> str_replace("'", '"',$_POST['tatuagem']),
	'fbb'		=> str_replace("'", '"',$_POST['fbb']),
	'data_de_entrada'	=> $data_de_entrada,
	'data_de_nascimento'		=> $data_de_nascimento,
	'sexo'	=> $_POST['sexo'],
	'pai'			=> $id_pai,
	'mae'			=> $id_mae,
	'raca'		=> $_POST['raca'],
	'observacoes'    => str_replace("'", '"',$_POST['observacoes']),
  'terceiro_pai'    =>  $terceiro_pai,
  'terceiro_mae'    =>  $terceiro_mae,
  'entrada'   => 2,
  'tipo_reproducao' => ''
);

DBCreate('animais', $dados);


if(!$terceiro_pai){
//CRIAS RANKING REPRODUTOR
$crias = DBRead('animais', "WHERE pai = '$id_pai' AND terceiro_pai = '0'");
$qtd_crias = count($crias);
$dados = array(
  'qtd_crias' => $qtd_crias,
  'id_macho'  => $id_pai
);
if($qtd_crias > 1){
  DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
}else{
  DBCreate('reprodutor', $dados);
}}
//FIM CRIAS RANKING REPRODUTOR

if(!$terceiro_mae){
//CRIAS RANKING MATRIZ
$crias = DBRead('animais', "WHERE mae = '$id_mae' AND terceiro_mae = '0'");
$qtd_crias = count($crias);
$dados = array(
  'qtd_crias' => $qtd_crias,
  'id_femea'  => $id_mae
);
if($qtd_crias > 1){
  DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
}else{
  DBCreate('matriz', $dados);
}}
//FIM CRIAS RANKING MATRIZ

if(!$terceiro_mae){
//RANKING INTERVALO MATRIZ
$cria = DBRead('animais', "WHERE mae = '$id_mae' AND tipo_reproducao != 'Embrionagem' AND terceiro_mae = '0' AND data_de_nascimento > '2011-01-01' ORDER BY data_de_nascimento asc");
$total=$qtd=$dias_total=$qtd_=$qtd_partos=0;
$qtd_crias = count($cria);
foreach ($cria as $cria_){
$data_nova = $cria_['data_de_nascimento'];
    if($qtd != 0){
        $data = $cria_['data_de_nascimento'];
        $data_atual = $data;
        $data = '0';
        $data['0'] = $data_atual['8'];
        $data['1'] = $data_atual['9'];
        $data['2'] = "/";
        $data['3'] = $data_atual['5'];
        $data['4'] = $data_atual['6'];
        $data['5'] = "/";
        $data['6'] = $data_atual['0'];
        $data['7'] = $data_atual['1'];
        $data['8'] = $data_atual['2'];
        $data['9'] = $data_atual['3'];
        $data_nova = $data;

        //ANTERIOR
        $data = $data_anterior;
        $data_atual = $data;
        $data = '0';
        $data['0'] = $data_atual['8'];
        $data['1'] = $data_atual['9'];
        $data['2'] = "/";
        $data['3'] = $data_atual['5'];
        $data['4'] = $data_atual['6'];
        $data['5'] = "/";
        $data['6'] = $data_atual['0'];
        $data['7'] = $data_atual['1'];
        $data['8'] = $data_atual['2'];
        $data['9'] = $data_atual['3'];
        $data_anterior = $data;

        if($data_anterior != $data_nova){
          $time_inicial = geraTimestamp($data_anterior);
          $time_final = geraTimestamp($data_nova);
          $diferenca = $time_final - $time_inicial;
          $dias = (int)floor( $diferenca / (60 * 60 * 24));
          $dias_total = $dias_total+$dias;
          $qtd_++; $qtd_partos++;
        }
    }
    //TESTE 7 ANOS
    $mae = DBRead('animais', "WHERE id = '$id_mae'");
    $data = $cria_['data_de_nascimento'];
    $data_atual = $data;
    $data = '0';
    $data['0'] = $data_atual['8'];
    $data['1'] = $data_atual['9'];
    $data['2'] = "/";
    $data['3'] = $data_atual['5'];
    $data['4'] = $data_atual['6'];
    $data['5'] = "/";
    $data['6'] = $data_atual['0'];
    $data['7'] = $data_atual['1'];
    $data['8'] = $data_atual['2'];
    $data['9'] = $data_atual['3'];
    $data_nova = $data;

    $data = $mae[0]['data_de_nascimento'];
    $data_atual = $data;
    $data = '0';
    $data['0'] = $data_atual['8'];
    $data['1'] = $data_atual['9'];
    $data['2'] = "/";
    $data['3'] = $data_atual['5'];
    $data['4'] = $data_atual['6'];
    $data['5'] = "/";
    $data['6'] = $data_atual['0'];
    $data['7'] = $data_atual['1'];
    $data['8'] = $data_atual['2'];
    $data['9'] = $data_atual['3'];
    $data_mae = $data;

    $time_inicial = geraTimestamp($data_mae);
    $time_final = geraTimestamp($data_nova);
    $diferenca = $time_final - $time_inicial;
    $dias = (int)floor( $diferenca / (60 * 60 * 24));
    if($dias < 2555){
        if($data_anterior != $data_nova){  $qtd_partos_prolificidade++; }
        $qtd_crias_prolificidade++;
    }
    //FIM TESTE 7 ANOS
$data_anterior = $cria_['data_de_nascimento'];
$qtd++;
}
$prolificidade = $qtd_crias_prolificidade/$qtd_partos_prolificidade;
$media = $dias_total/$qtd_;
$dados = array(
    'intervalo' => $media,
    'prolificidade' => $prolificidade
  );

DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
//RANKING INTERVALO MATRIZ FIM
}

$id_animal = DBRead('animais', "WHERE nome = '$nome'");
$id_animal = $id_animal[0]['id'];
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal'>";
}}}
?>
