<?php
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}


require __DIR__ . "/../../_config.php";
header("Content-Type: text/html; charset=UTF-8");
$receptora = '';
$nome = DBEscape($_POST['nome_animal']);
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

$tipo = $_GET['tipo'];
$id_lote = $_GET['id_lote'];


if($tipo == 1){
  $dados = array(
  	'status_nascimento'	=> 1
  );
  DBUpdate('monta_controle', $dados, "id = '$id_lote'");
  $tipo_reproducao = "Monta Natural";
}

if($tipo == 2){
  $dados = array(
  	'status_nascimento'	=> 1
  );
  DBUpdate('inseminacao_controle', $dados, "id = '$id_lote'");
  $tipo_reproducao = "Inseminação Artificial";
}

if($tipo == 3){
  $dados = array(
  	'status_nascimento'	=> 1
  );
  DBUpdate('transplante_controle', $dados, "id = '$id_lote'");
  $tipo_reproducao = "Embrionagem";
  $receptora = $_GET['receptora'];
}


$dados = array(
	'nome'	=> str_replace("'", '"',$_POST['nome_animal']),
	'tatuagem'	=> str_replace("'", '"',$_POST['tatuagem']),
	'data_de_entrada'	=> $data_de_nascimento,
	'data_de_nascimento'		=> $data_de_nascimento,
	'sexo'	=> $_POST['sexo'],
	'pai'			=> $id_pai,
	'mae'			=> $id_mae,
	'raca'		=> $_POST['raca'],
	'observacoes'    => str_replace("'", '"',$_POST['observacoes']),
  'terceiro_pai'    =>  $terceiro_pai,
  'terceiro_mae'    =>  $terceiro_mae,
  'entrada'   => 0,
  'peso_inicial'    => str_replace("," , "" , $_POST['peso']),
  'tipo_reproducao'   => $tipo_reproducao,
  'receptora' => $receptora,
  'link_fbb' => '', 'fbb_img' => '', 'peso2' => 0,
  'data2' => null, 'data3' => null,
  'parcelas' => 0, 'tipo_venda' => '', 'tipo' => 0,
  'confirmacao' => 0, 'prolapso' => '', 'criador' => '',
  'status' => 0, 'chip' => ''
);

$id_animal = DBCreate('animais', $dados, true);

//ANIMAL MORTO
if($_POST['status']){
  $dados = array(
  	'data_de_saida'	=> $data_de_nascimento,
  	'causa_da_perda'	=> 'Nascimento',
    'status'   => 1
  );
  DBUpdate('animais', $dados, "id = '$id_animal'");
}
//FIM ANIMAL MORTO

//CRIAS RANKING REPRODUTOR
$crias = DBRead('animais', "WHERE pai = '$id_pai' AND terceiro_pai = '0'");
$qtd_crias = count($crias ?: []);
$dados = array(
  'qtd_crias' => $qtd_crias,
  'id_macho'  => $id_pai
);
if($qtd_crias > 1){
  DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
}else{
  DBCreate('reprodutor', array_merge(['qtd_avaliadas' => 0, 'tipo2' => 0, 'tipo3' => 0, 'tipo4' => 0, 'tipo5' => 0, 'qtd_vendas' => 0, 'qtd_mortes' => 0, 'venda_macho' => 0, 'venda_femea' => 0, 'venda_geral' => 0, 'nota' => 0, 'pesagem' => 0, 'cabeca' => 0, 'pescoco' => 0, 'quarto_anterior' => 0, 'barril' => 0, 'quarto_posterior' => 0, 'comprimento' => 0, 'orgao' => 0, 'gordura' => 0, 'cobertura' => 0, 'cor' => 0, 'conformacao' => 0, 'gmd' => 0], $dados));
}
//FIM CRIAS RANKING REPRODUTOR


//RANKING MORTALIDADE REPRODUTOR
$morte = DBRead('animais', "WHERE pai = '$id_pai' AND status = '1' AND causa_da_perda = 'Nascimento'");
$qtd_mortes = count($morte ?: []);
$qtd_mortes = $qtd_crias ? ($qtd_mortes*100)/$qtd_crias : 0;
$dados = array(
  'qtd_mortes' => $qtd_mortes
);
DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
//FIM RANKING MORTALIDADE REPRODUTOR


//CRIAS RANKING MATRIZ
$crias = DBRead('animais', "WHERE mae = '$id_mae' AND terceiro_mae = '0'");
$qtd_crias = count($crias ?: []);
$dados = array(
  'qtd_crias' => $qtd_crias,
  'id_femea'  => $id_mae
);
if($qtd_crias > 1){
  DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
}else{
  DBCreate('matriz', array_merge(['qtd_avaliadas' => 0, 'tipo2' => 0, 'tipo3' => 0, 'tipo4' => 0, 'tipo5' => 0, 'qtd_vendas' => 0, 'qtd_mortes' => 0, 'venda_macho' => 0, 'venda_femea' => 0, 'venda_geral' => 0, 'nota' => 0, 'pesagem' => 0, 'cabeca' => 0, 'pescoco' => 0, 'quarto_anterior' => 0, 'barril' => 0, 'quarto_posterior' => 0, 'comprimento' => 0, 'orgao' => 0, 'gordura' => 0, 'cobertura' => 0, 'cor' => 0, 'conformacao' => 0, 'peso_apartacao' => 0, 'intervalo' => 0, 'prolificidade' => 0, 'qtd_partos' => 0], $dados));
}
//FIM CRIAS RANKING MATRIZ


//RANKING INTERVALO MATRIZ
$cria = DBRead('animais', "WHERE mae = '$id_mae' AND tipo_reproducao != 'Embrionagem' AND terceiro_mae = '0' AND data_de_nascimento > '2011-01-01' ORDER BY data_de_nascimento asc");
$total=$qtd=$dias_total=$qtd_=$qtd_partos=$qtd_crias_prolificidade=$qtd_partos_prolificidade=0;
$data_anterior = null;
$qtd_crias = count($cria ?: []);
foreach (($cria ?: []) as $cria_){
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
$prolificidade = $qtd_partos_prolificidade ? $qtd_crias_prolificidade/$qtd_partos_prolificidade : 0;
$media = $qtd_ ? $dias_total/$qtd_ : 0;
$dados = array(
    'intervalo' => $media,
    'prolificidade' => $prolificidade
  );
DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
//RANKING INTERVALO MATRIZ FIM


//RANKING LOTES REPRODUCAO MONTA
if($tipo == 1){
  $monta = DBRead('monta_controle', "WHERE id = '$id_lote'");
  $id_monta = $monta[0]['id_monta'];
  $lote_reproducao = DBRead('lotes_reproducao', "WHERE tipo = '0' AND id_lote = '$id_monta'");
  $qtd_crias = $lote_reproducao[0]['crias'];
  $vivos = $lote_reproducao[0]['vivos'];
  $mortos = $lote_reproducao[0]['mortes'];
  $monta = DBRead('monta_controle', "WHERE id = '$id_lote'");
  $id_monta = $monta[0]['id_monta'];
  $dados = DBRead('monta_controle', "WHERE id_monta = '$id_monta'");
  $qtd = count($dados ?: []);
  if($_POST['status'] == 0){
    $vivos++;
  }else{
    $mortos++;
  }
  $qtd_crias = $qtd_crias+1;
  $dados = array(
    'vivos'   =>  $vivos,
    'crias'   =>  $qtd_crias,
    'mortes'  =>  $mortos
  );
  DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_monta' AND tipo = '0'");
}
//FIM RANKING LOTES

//RANKING LOTES REPRODUCAO IA
if($tipo == 2){
  $ia = DBRead('inseminacao_controle', "WHERE id = '$id_lote'");
  $id_ia = $ia[0]['id_lote'];
  $lote_reproducao = DBRead('lotes_reproducao', "WHERE tipo = '1' AND id_lote = '$id_ia'");
  $qtd_crias = $lote_reproducao[0]['crias'];
  $vivos = $lote_reproducao[0]['vivos'];
  $mortos = $lote_reproducao[0]['mortes'];
  $dados = DBRead('inseminacao_controle', "WHERE id_lote = '$id_ia'");
  $nascimento = DBRead('inseminacao_controle', "WHERE id_lote = '$id_ia' AND status_nascimento = '1'");
  $qtd = count($dados ?: []);
  if($_POST['status'] == 0){
    $vivos++;
  }else{
    $mortos++;
  }
  $dados = array(
    'vivos'   =>  $vivos,
    'crias'   =>  $qtd_crias,
    'mortes'  =>  $mortos
  );
  DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_ia' AND tipo = '1'");

}
//FIM RANKING LOTES

//RANKING LOTES REPRODUCAO TE
if($tipo == 3){
  $te = DBRead('transplante_controle', "WHERE id = '$id_lote'");
  $id_te = $te[0]['id_lote'];
  $lote_reproducao = DBRead('lotes_reproducao', "WHERE tipo = '2' AND id_lote = '$id_te'");
  $qtd_crias = $lote_reproducao[0]['crias'];
  $vivos = $lote_reproducao[0]['vivos'];
  $mortos = $lote_reproducao[0]['mortes'];
  $dados = DBRead('transplante_controle', "WHERE id_lote = '$id_te'");
  $nascimento = DBRead('transplante_controle', "WHERE id_lote = '$id_te' AND status_nascimento = '1'");
  $qtd = count($dados ?: []);
  if($_POST['status'] == 0){
    $vivos++;
  }else{
    $mortos++;
  }
  $dados = array(
    'vivos'   =>  $vivos,
    'crias'   =>  $qtd_crias,
    'mortes'  =>  $mortos
  );
  DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_te' AND tipo = '2'");
}
//FIM RANKING LOTES


$qtd = $_GET['qtd'];
$y = $_GET['y'];
$teste = $qtd-$y;
if($teste == 0){
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=cadastrar_animal&tipo=3'>";
}else{
  $y++;
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=cadastrar_nascimento&x=$qtd&id_lote=$id_lote&tipo=$tipo&y=$y&data=$data_de_nascimento&receptora=$receptora'>";
}
}}}
?>
