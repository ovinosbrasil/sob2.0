<?php
require_once __DIR__ . '/../_config.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('sucesso'=>false,'mensagem'=>'Método não permitido.'), JSON_UNESCAPED_UNICODE);
    exit;
}
$nome=trim((string)($_POST['nome']??''));
if($nome===''||mb_strlen($nome,'UTF-8')>100){
    http_response_code(422);
    echo json_encode(array('sucesso'=>false,'mensagem'=>'Informe um nome válido para a vacina.'),JSON_UNESCAPED_UNICODE);
    exit;
}
$nomeEscapado=DBEscape($nome);
$existente=DBRead('vacina',"WHERE nome = '$nomeEscapado' LIMIT 1");
if($existente){$id=(int)$existente[0]['id'];$nome=(string)$existente[0]['nome'];}
else{$id=(int)DBCreate('vacina',array('nome'=>$nome),true);}
if(!$id){http_response_code(500);echo json_encode(array('sucesso'=>false,'mensagem'=>'Não foi possível cadastrar a vacina.'),JSON_UNESCAPED_UNICODE);exit;}
echo json_encode(array('sucesso'=>true,'id'=>$id,'nome'=>$nome),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
