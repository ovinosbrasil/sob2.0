<?php
if (isset($argv[1])) {
 $caso=json_decode(base64_decode($argv[1]),true);
 $_SERVER['REQUEST_METHOD']=$caso['metodo']??'POST';
 $_SESSION=array('exposicao_progene_csrf'=>'token-teste');
 $_POST=array_merge(array('csrf_token'=>'token-teste','id_evento'=>'7','id_animal'=>'11','progene'=>'1','tipo'=>'pai'),$caso['post']??array());
 $chamadas=array();
 function definirProgeneExposicao($evento,$animal,$ativo,$tipo){global $caso,$chamadas;$chamadas[]=array($evento,$animal,$ativo,$tipo);if(($caso['modo']??'')==='ausente')throw new DomainException('Animal não encontrado.');if(($caso['modo']??'')==='falha')throw new RuntimeException('Falha simulada');return $ativo;}
 ob_start();
 register_shutdown_function(function(){global $chamadas;$body=ob_get_clean();echo json_encode(array('status'=>http_response_code(),'json'=>json_decode($body,true),'chamadas'=>$chamadas));});
 $codigo=file_get_contents(__DIR__.'/../exposicao/_progene.php');
 $codigo=str_replace(array("require_once __DIR__ . '/../_config.php';","require_once __DIR__ . '/participantes.php';"),'',$codigo);
 eval('?>'.$codigo);exit;
}
$casos=array(
 array('status'=>200,'estado'=>1),array('status'=>200,'estado'=>0,'post'=>array('progene'=>'0')),
 array('status'=>200,'estado'=>1,'post'=>array('tipo'=>'mae')),array('status'=>400,'post'=>array('tipo'=>'invalido')),array('status'=>400,'post'=>array('tipo'=>'')),
 array('status'=>405,'metodo'=>'GET'),array('status'=>403,'post'=>array('csrf_token'=>'invalido')),
 array('status'=>400,'post'=>array('progene'=>'2')),array('status'=>400,'post'=>array('id_animal'=>'0')),
 array('status'=>404,'modo'=>'ausente'),array('status'=>500,'modo'=>'falha')
);
foreach($casos as $caso){
 $saida=shell_exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(__FILE__).' '.escapeshellarg(base64_encode(json_encode($caso))));$res=json_decode($saida,true);
 if(($res['status']??null)!==$caso['status'])throw new RuntimeException('Status inesperado: '.$saida);
 if(isset($caso['estado']) && (($res['json']['progene']??null)!==$caso['estado'] || !$res['json']['sucesso']))throw new RuntimeException('Estado incorreto');
 if($caso['status']===200 && $res['chamadas'][0][3]!==($caso['post']['tipo']??'pai'))throw new RuntimeException('Tipo de progênie incorreto');
 if(in_array($caso['status'],array(400,403,405),true)&&$res['chamadas'])throw new RuntimeException('Requisição inválida alterou estado');
}
echo "Progênie: endpoint POST, CSRF, estado, validação, ausência e falha: OK\n";
