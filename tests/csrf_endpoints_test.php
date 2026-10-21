<?php
if (isset($argv[1])) {
    $caso=json_decode(base64_decode($argv[1]),true);
    $_SESSION=array('_csrf_sob'=>'token-da-sessao');
    $_POST=$caso['post'];
    $_GET=array('id_financeiro'=>7,'id_comprador'=>7,'id_animal'=>1);
    $_POST['id_animal']=1;
    $_SERVER['REQUEST_METHOD']=$caso['metodo'];
    $leituras=0;$gravacoes=0;
    function DBRead(...$args){$GLOBALS['leituras']++;throw new RuntimeException('Consulta antes de validar CSRF');}
    function DBCreate(...$args){$GLOBALS['gravacoes']++;throw new RuntimeException('Gravação indevida');}
    function DBUpdate(...$args){return DBCreate(...$args);}
    function DBDelete(...$args){return DBCreate(...$args);}
    ob_start();
    register_shutdown_function(function(){
        $body=ob_get_clean();
        echo json_encode(array('status'=>http_response_code(),'leituras'=>$GLOBALS['leituras'],'gravacoes'=>$GLOBALS['gravacoes'],'body'=>$body));
    });
    $arquivo=realpath(__DIR__.'/../'.$caso['arquivo']);
    $codigo=file_get_contents($arquivo);
    $codigo=str_replace(array('include "../_config.php";',"require_once __DIR__ . '/../_config.php';"),'',$codigo);
    $codigo=preg_replace('/(?:include|require|require_once)\s+(?:__DIR__\s*\.\s*)?[\"\'][^\"\']*_config.php[\"\'];/', '', $codigo);
    $codigo=str_replace('__DIR__',var_export(dirname($arquivo),true),$codigo);
    eval('?>'.$codigo);exit;
}
require __DIR__.'/../includes/csrf.php';
$arquivos=array('perfil/_alterar.php','vendas/_comprador.php','vendas/_alterar_comprador.php','vendas/_excluir_comprador.php','vendas/_venda_conjunta.php','vendas/_excluir_venda.php','financeiro/_cadastrar.php','financeiro/_alterar.php','financeiro/_excluir.php','financeiro/_confirmar.php','financeiro/_cancelar.php');
$arquivos=array_merge($arquivos,array('animal/_excluir_animal.php', 'animal/_alterar.php', 'animal/_alterar_terceiro.php', 'animal/saida/_saida.php', 'animal/saida/_desfazer_saida.php', 'animal/cadastrar/_rebanho.php', 'animal/cadastrar/_compra.php', 'animal/cadastrar/_terceiros.php', 'animal/cadastrar/_nascimento.php', 'animal/vender/_vender.php', 'animal/vender/_desfazer_venda.php'));
$casos=array(
    array('metodo'=>'GET','post'=>array('_csrf_sob'=>'token-da-sessao'),'status'=>405),
    array('metodo'=>'POST','post'=>array(),'status'=>403),
    array('metodo'=>'POST','post'=>array('_csrf_sob'=>''),'status'=>403),
    array('metodo'=>'POST','post'=>array('_csrf_sob'=>'outra-sessao'),'status'=>403),
    array('metodo'=>'POST','post'=>array('_csrf_sob'=>array('token-da-sessao')),'status'=>403),
    array('metodo'=>'POST','post'=>array('_csrf_sob'=>'incorreto','formato'=>'json'),'status'=>403),
);
foreach($arquivos as $arquivo)foreach($casos as $caso){
    $caso['arquivo']=$arquivo;
    $cmd=escapeshellarg(PHP_BINARY).' -d short_open_tag=1 '.escapeshellarg(__FILE__).' '.escapeshellarg(base64_encode(json_encode($caso)));
    $r=json_decode(shell_exec($cmd),true);
    if(!$r || $r['status']!==$caso['status'] || $r['leituras'] || $r['gravacoes'])throw new RuntimeException('CSRF falhou: '.json_encode($caso).' '.json_encode($r));
    if((strpos($arquivo,'vendas/')===0 && ($caso['post']['formato']??'')==='json') || $arquivo==='vendas/_excluir_comprador.php'){
        $json=json_decode($r['body'],true);
        if(!is_array($json)||$json['sucesso']!==false)throw new RuntimeException('Resposta JSON inválida');
    }
}
$_SESSION=array();$token=tokenCsrfSob();
if(strlen($token)!==64 || tokenCsrfSob()!==$token)throw new RuntimeException('Token instável');
if(statusPostCsrfSob('POST',array('_csrf_sob'=>$token),$_SESSION)!==200)throw new RuntimeException('Token válido recusado');
if(statusPostCsrfSob('POST',array('_csrf_sob'=>$token),array())!==403)throw new RuntimeException('Token aceito fora da sessão');
echo count($arquivos)*count($casos)." cenários: GET, token ausente, vazio, inválido, array e JSON recusados antes de consultar/gravar; token válido e sessão: OK\n";
