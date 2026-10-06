<?php
require_once __DIR__ . "/../../_config.php";
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}



$nome = $_POST['nome_animal'];

$sql = DBRead('animais', "WHERE nome = '$nome'");
if($sql[0]['id'] > 0){
          echo "<script type=\"text/javascript\"> alert(\"Nome do animal já existe, tente novamente.\"); </script>
			    <script language='javascript'>history.back()</script>";
}else{
function localizarParenteCadastro($campo, $sexo)
{
  $id = filter_var($_POST[$campo . '_id'] ?? 0, FILTER_VALIDATE_INT);
  $origem = $_POST[$campo . '_origem'] ?? '';
  if ($id && in_array($origem, array('rebanho', 'terceiros'), true)) {
    $tabela = $origem === 'terceiros' ? 'terceiros' : 'animais';
    $registro = DBRead($tabela, "WHERE id = '" . (int)$id . "' AND sexo = '" . DBEscape($sexo) . "'" . ($tabela === 'terceiros' ? ' AND ativo = 1' : ''));
    if ($registro) {
      return array((int)$registro[0]['id'], $origem === 'terceiros' ? 1 : 0);
    }
  }

  $nome = DBEscape(trim((string)($_POST[$campo] ?? '')));
  if ($nome === '') {
    return array(0, 0);
  }
  $registro = DBRead('animais', "WHERE nome = '$nome' AND sexo = '" . DBEscape($sexo) . "'");
  if ($registro) {
    return array((int)$registro[0]['id'], 0);
  }
  $registro = DBRead('terceiros', "WHERE ativo = 1 AND nome = '$nome' AND sexo = '" . DBEscape($sexo) . "'");
  return $registro ? array((int)$registro[0]['id'], 1) : array(0, 0);
}

list($id_pai, $terceiro_pai) = localizarParenteCadastro('pai', 'Macho');
list($id_mae, $terceiro_mae) = localizarParenteCadastro('mae', 'Fêmea');

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
  'entrada'   => 1,
  'preco_de_compra'  => str_replace("," , "" , $_POST['valor']),
  'tipo_reproducao' => ''
);

DBCreate('animais', $dados);

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
}
//FIM CRIAS RANKING REPRODUTOR

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
}
//FIM CRIAS RANKING MATRIZ



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

$id_animal = DBRead('animais', "WHERE nome = '$nome'");
$id_animal = $id_animal[0]['id'];
$_SESSION['alerta_cadastro_animal'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Animal cadastrado com sucesso.');
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal'>";
}}}
?>
