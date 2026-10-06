<?php
require_once __DIR__ . '/../_config.php';
$idAnimal=filter_var($_POST['animal_id']??null,FILTER_VALIDATE_INT);
$idAnimal=$idAnimal>0?(int)$idAnimal:0;
$data=trim((string)($_POST['data']??''));
$valor=trim((string)($_POST['valor']??''));
$nomeAnimal=trim((string)($_POST['animal']??''));
function voltarPesagem($mensagem){
    global $idAnimal,$data,$valor;
    $_SESSION['alerta_pesagem']=array('tipo'=>'warning','titulo'=>'Atenção!','mensagem'=>$mensagem);
    header('Location: ../geral.php?'.http_build_query(array('pg'=>'pesagem','id_animal'=>$idAnimal,'data'=>$data,'valor'=>$valor)),true,303);
    exit;
}
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: ../geral.php?pg=pesagem',true,303);exit;}
if(!$idAnimal&&$nomeAnimal!==''){
    $nomeEscapado=DBEscape($nomeAnimal);
    $encontrados=DBRead('animais',"WHERE nome='$nomeEscapado' LIMIT 2")?:array();
    if(count($encontrados)===1)$idAnimal=(int)$encontrados[0]['id'];
    else voltarPesagem('Selecione o animal nos resultados da pesquisa para identificar o cadastro correto.');
}
if(!$idAnimal||$data===''||$valor==='')voltarPesagem('Preencha os campos obrigatórios: Animal, Data e Peso (kg).');
if(!DBRead('animais',"WHERE id='$idAnimal' LIMIT 1"))voltarPesagem('Selecione um animal válido do rebanho.');
$dataPeso=DateTimeImmutable::createFromFormat('!d/m/Y',$data);
if(!$dataPeso||$dataPeso->format('d/m/Y')!==$data||(int)$dataPeso->format('Y')<1000)voltarPesagem('Informe uma data válida para a pesagem.');
$peso=str_replace(array('.',','),array('','.'),$valor);
if(!preg_match('/^\d+(?:\.\d{1,3})?$/',$peso)||!is_finite((float)$peso)||(float)$peso<=0)voltarPesagem('Informe um peso maior que zero, com até três casas decimais.');
if(!DBCreate('pesagem',array('id_animal'=>$idAnimal,'data'=>$dataPeso->format('Y-m-d'),'peso'=>$peso)))voltarPesagem('Não foi possível cadastrar a pesagem. Tente novamente.');
$_SESSION['alerta_pesagem']=array('tipo'=>'success','titulo'=>'Sucesso!','mensagem'=>'Pesagem cadastrada com sucesso.');
header('Location: ../geral.php?pg=pesagem&id_animal='.$idAnimal,true,303);
exit;
