<?php
if (isset($argv[1])) {
    $caso=json_decode(base64_decode($argv[1]),true);
    $_SERVER['REQUEST_METHOD']=$caso['metodo']??'POST';
    $_POST=$caso['post'];
    $gravacoes=array();
    function DBCreate($tabela,$dados){$GLOBALS['gravacoes'][]=array('tabela'=>$tabela,'dados'=>$dados);return true;}
    ob_start();register_shutdown_function(function(){ob_end_clean();echo json_encode(array('status'=>http_response_code(),'gravacoes'=>$GLOBALS['gravacoes']));});
    $codigo=file_get_contents(__DIR__.'/../_cadastro.php');
    $codigo=preg_replace('/require __DIR__ \\. \'\\/mysqli\\/[^\']+\';/','',$codigo);
    eval('?>'.$codigo);exit;
}
$valido=array('nome'=>' Nome completo ','email'=>' pessoa@example.com ','telefone'=>' (71) 99999-9999 ');
$casos=array(array('post'=>$valido,'status'=>303),array('metodo'=>'GET','post'=>$valido,'status'=>405),array('post'=>array(),'status'=>422));
foreach(array('nome','email','telefone') as $campo){
    foreach(array('', '   ', array('valor')) as $valor){$post=$valido;$post[$campo]=$valor;$casos[]=array('post'=>$post,'status'=>422);}
}
$post=$valido;$post['email']='email-invalido';$casos[]=array('post'=>$post,'status'=>422);
foreach($casos as $caso){
    $r=json_decode(shell_exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(__FILE__).' '.escapeshellarg(base64_encode(json_encode($caso)))),true);
    if(!$r||$r['status']!==$caso['status']||count($r['gravacoes'])!==($caso['status']===303?1:0))throw new RuntimeException('Falha na validação do cadastro');
    if($caso['status']===303&&$r['gravacoes'][0]['dados']!==array('nome'=>'Nome completo','email'=>'pessoa@example.com','celular'=>'(71) 99999-9999'))throw new RuntimeException('Dados válidos alterados');
}
echo count($casos)." cenários: campos obrigatórios, espaços, arrays, e-mail e cadastro válido: OK\n";
