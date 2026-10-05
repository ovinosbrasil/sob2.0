<?php
require_once __DIR__ . '/../../_config.php';

function voltarCadastroUltrassom($mensagem, $dados=array())
{
    $parametros=array_merge(array('pg'=>'cadastrar_ultrassom','buscar'=>1,'erro'=>$mensagem),$dados);
    header('Location: ../../geral.php?'.http_build_query($parametros),true,303);
    exit;
}
function dataRegistroUltrassom($valor)
{
    $valor=trim((string)$valor);
    $data=DateTimeImmutable::createFromFormat('!d/m/Y',$valor);
    return $data && $data->format('d/m/Y')===$valor ? $data : null;
}

$femea=trim((string)($_POST['femea']??''));
$femeaId=filter_var($_POST['femea_id']??0,FILTER_VALIDATE_INT);
$origem=in_array($_POST['femea_origem']??'',array('rebanho','terceiros','receptora'),true)?$_POST['femea_origem']:'';
if ($origem === 'terceiros' && (!($femeaId > 0) || !DBRead('terceiros', "WHERE id = '" . (int)$femeaId . "' AND ativo = 1"))) {
    voltarCadastroUltrassom('Este terceiro está inativo ou não foi encontrado.');
}
$situacao=filter_var($_POST['situacao']??0,FILTER_VALIDATE_INT);
$data=dataRegistroUltrassom($_POST['data']??'');
$vinculo=isset($_POST['vinculo'])?(string)$_POST['vinculo']:'';
$retorno=array('femea'=>$femea,'femea_id'=>(int)$femeaId,'femea_origem'=>$origem,'situacao'=>(int)$situacao,'data'=>(string)($_POST['data']??''));

if(!$femeaId||!$origem||!in_array($situacao,array(1,2),true)||!$data||!preg_match('/^([012]):(\d+):(\d+)$/',$vinculo,$partes)) {
    voltarCadastroUltrassom('Preencha os dados e selecione um lote válido.',$retorno);
}
$tipo=(int)$partes[1]; $controleId=(int)$partes[2]; $loteId=(int)$partes[3];
$mapa=array(0=>array('tabela'=>'monta_controle','fk'=>'id_monta'),1=>array('tabela'=>'inseminacao_controle','fk'=>'id_lote'),2=>array('tabela'=>'transplante_controle','fk'=>'id_lote'));
$config=$mapa[$tipo];
if($tipo===0) $condicao="id = '$controleId' AND id_monta = '$loteId' AND id_animal = '".(int)$femeaId."' AND terceiro = '".($origem==='terceiros'?1:0)."' AND ultrassom = '0'";
elseif($tipo===1) $condicao="id = '$controleId' AND id_lote = '$loteId' AND id_femea = '".(int)$femeaId."' AND terceiro = '".($origem==='terceiros'?1:0)."' AND ultrassom = '0'";
else {
    if($origem!=='receptora') voltarCadastroUltrassom('A receptora selecionada não corresponde ao lote.',$retorno);
    $condicao="id = '$controleId' AND id_lote = '$loteId' AND id_receptora = '".(int)$femeaId."' AND ultrassom = '0'";
}
$controle=DBRead($config['tabela'],"WHERE $condicao LIMIT 1")?:array();
if(!$controle) voltarCadastroUltrassom('Este lote não está mais disponível para o cadastro do ultrassom.',$retorno);

DBUpdate($config['tabela'],array('ultrassom'=>(int)$situacao,'data_ultrassom'=>$data->format('Y-m-d')),"id = '$controleId'");
$todos=DBRead($config['tabela'],"WHERE {$config['fk']} = '$loteId'")?:array(); $positivos=0;
foreach($todos as $registro) if((int)($registro['ultrassom']??0)===1)$positivos++;
DBUpdate('lotes_reproducao',array('ultrassom'=>count($todos)?$positivos*100/count($todos):0),"id_lote = '$loteId' AND tipo = '$tipo'");
header('Location: ../../geral.php?'.http_build_query(array('pg'=>'lista_ultrassom','tipo'=>1,'reproducao'=>$tipo,'id_lote'=>$loteId)),true,303);
exit;
