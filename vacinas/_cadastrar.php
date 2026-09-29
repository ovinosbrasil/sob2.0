<?php
require_once __DIR__ . '/../_config.php';
function voltarCadastroVacinaIndividual($mensagem,array $dados=array()){$dados['pg']='cadastrar_vacina';$dados['erro']=$mensagem;header('Location: ../geral.php?'.http_build_query($dados),true,303);exit;}
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: ../geral.php?pg=cadastrar_vacina');exit;}
$nomes=array_values(array_map(function($v){return trim((string)$v);},(array)($_POST['animal']??array())));
$idsRecebidos=array_values((array)($_POST['animal_id']??array()));$origens=array_values((array)($_POST['animal_origem']??array()));
$vacinaId=filter_var($_POST['vacina']??null,FILTER_VALIDATE_INT);$dataTexto=trim((string)($_POST['data']??''));$observacao=trim((string)($_POST['observacao']??''));
$retorno=array('animal'=>$nomes,'animal_id'=>$idsRecebidos,'animal_origem'=>$origens,'vacina'=>(int)$vacinaId,'data'=>$dataTexto,'observacao'=>$observacao);
if(!$idsRecebidos||count($idsRecebidos)!==count($nomes)||count($origens)!==count($nomes))voltarCadastroVacinaIndividual('Selecione pelo menos um animal na lista de pesquisa.',$retorno);
$animais=array();
foreach($idsRecebidos as $indice=>$valor){$id=filter_var($valor,FILTER_VALIDATE_INT);if(!$id||($origens[$indice]??'')!=='rebanho')voltarCadastroVacinaIndividual('Selecione todos os animais na lista de pesquisa.',$retorno);$animais[(int)$id]=(int)$id;}
$animais=array_values($animais);
if(!$vacinaId)voltarCadastroVacinaIndividual('Selecione uma vacina.',$retorno);
$data=DateTimeImmutable::createFromFormat('!d/m/Y',$dataTexto);$erros=DateTimeImmutable::getLastErrors();if(!$data||($erros!==false&&($erros['warning_count']||$erros['error_count'])))voltarCadastroVacinaIndividual('Informe uma data válida no formato dd/mm/aaaa.',$retorno);
if(mb_strlen($observacao,'UTF-8')>300)voltarCadastroVacinaIndividual('A observação deve ter no máximo 300 caracteres.',$retorno);
$idsSql=implode(',',array_map('intval',$animais));$existentes=DBRead('animais',"WHERE id IN ($idsSql)",'id')?:array();if(count($existentes)!==count($animais))voltarCadastroVacinaIndividual('Um ou mais animais não foram encontrados.',$retorno);
$vacina=DBRead('vacina',"WHERE id='".(int)$vacinaId."' LIMIT 1");if(!$vacina)voltarCadastroVacinaIndividual('Vacina não encontrada.',$retorno);
$link=DBConnect();mysqli_begin_transaction($link);
try{
    $sql='INSERT INTO vacinas (id_animal,id_vacina,data,obs,id_lote) VALUES (?,?,?,?,NULL)';
    $stmt=mysqli_prepare($link,$sql);if(!$stmt)throw new RuntimeException('Falha ao preparar o cadastro.');
    $dataBanco=$data->format('Y-m-d');$idVacina=(int)$vacinaId;
    foreach($animais as $idAnimal){mysqli_stmt_bind_param($stmt,'iiss',$idAnimal,$idVacina,$dataBanco,$observacao);if(!mysqli_stmt_execute($stmt))throw new RuntimeException('Falha ao cadastrar a vacina.');}
    mysqli_stmt_close($stmt);mysqli_commit($link);DBClose($link);
}catch(Throwable $erroCadastro){mysqli_rollback($link);DBClose($link);voltarCadastroVacinaIndividual('Não foi possível cadastrar as vacinas.',$retorno);}
header('Location: ../geral.php?pg=vacinas',true,303);exit;
