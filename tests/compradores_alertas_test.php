<?php
if(isset($argv[1])) {
 $caso=json_decode(base64_decode($argv[1]),true);
 $_POST=array_merge(array('nome'=>'Cliente','email'=>'cliente@example.com','celular'=>'123','cpf'=>'','cod'=>'','cidade'=>'Cidade','estado'=>'BA','id_comprador'=>'7'),$caso['post']??array());
 $_GET=array_merge(array('id_comprador'=>7),$caso['get']??array());$_SESSION=array();$gravacoes=array();
 set_error_handler(function($n,$m){throw new RuntimeException($m);});
 function DBEscape($v){return addslashes($v);}
 function DBRead($tabela,$params=''){
  global $caso;$modo=$caso['modo']??'';
  if($tabela==='mercado'){
   if(strpos($params,'WHERE nome')!==false)return $modo==='duplicado'?array(array('id'=>8)):false;
   return $modo==='ausente'?false:array(array('id'=>7,'nome'=>'Cliente'));
  }
  return $modo===$tabela?array(array('id'=>1)):false;
 }
 function DBCreate($tabela,$dados,$id=false){global $gravacoes,$caso;$gravacoes[]='create';return ($caso['modo']??'')==='falha'?false:9;}
 function DBUpdate($tabela,$dados,$where){global $gravacoes,$caso;$gravacoes[]='update';return ($caso['modo']??'')!=='falha';}
 function DBDelete($tabela,$where){global $gravacoes,$caso;$gravacoes[]='delete';return ($caso['modo']??'')!=='falha';}
 ob_start();register_shutdown_function(function(){global $gravacoes;$body=ob_get_clean();echo json_encode(array('json'=>json_decode($body,true),'status'=>http_response_code(),'sessao'=>$_SESSION,'gravacoes'=>$gravacoes));});
 $codigo=file_get_contents(__DIR__.'/../vendas/'.$caso['arquivo']);
 $codigo=str_replace("require_once __DIR__ . '/../_config.php';",'',$codigo);
 $codigo=str_replace('__DIR__',var_export(realpath(__DIR__.'/../vendas'),true),$codigo);eval('?>'.$codigo);exit;
}
$casos=array(
 array('arquivo'=>'_comprador.php','tipo'=>'success'),
 array('arquivo'=>'_comprador.php','tipo'=>'warning','post'=>array('nome'=>'')),
 array('arquivo'=>'_comprador.php','tipo'=>'warning','post'=>array('email'=>'invalido')),
 array('arquivo'=>'_comprador.php','tipo'=>'warning','modo'=>'duplicado'),
 array('arquivo'=>'_comprador.php','tipo'=>'danger','modo'=>'falha'),
 array('arquivo'=>'_comprador.php','tipo'=>'success','post'=>array('formato'=>'json')),
 array('arquivo'=>'_comprador.php','tipo'=>'warning','modo'=>'duplicado','post'=>array('formato'=>'json')),
 array('arquivo'=>'_comprador.php','tipo'=>'warning','post'=>array('formato'=>'json','nome'=>'')),
 array('arquivo'=>'_alterar_comprador.php','tipo'=>'success'),
 array('arquivo'=>'_alterar_comprador.php','tipo'=>'warning','modo'=>'ausente'),
 array('arquivo'=>'_alterar_comprador.php','tipo'=>'warning','get'=>array('id_comprador'=>0)),
 array('arquivo'=>'_alterar_comprador.php','tipo'=>'warning','post'=>array('nome'=>'')),
 array('arquivo'=>'_alterar_comprador.php','tipo'=>'warning','modo'=>'duplicado'),
 array('arquivo'=>'_alterar_comprador.php','tipo'=>'danger','modo'=>'falha'),
 array('arquivo'=>'_excluir_comprador.php','tipo'=>'warning','post'=>array('id_comprador'=>0)),
 array('arquivo'=>'_excluir_comprador.php','tipo'=>'warning','modo'=>'ausente'),
 array('arquivo'=>'_excluir_comprador.php','tipo'=>'warning','modo'=>'vendas'),
 array('arquivo'=>'_excluir_comprador.php','tipo'=>'warning','modo'=>'venda_semen'),
 array('arquivo'=>'_excluir_comprador.php','tipo'=>'warning','modo'=>'venda_embriao'),
 array('arquivo'=>'_excluir_comprador.php','tipo'=>'warning','modo'=>'controle_financeiro'),
 array('arquivo'=>'_excluir_comprador.php','tipo'=>'danger','modo'=>'falha'),
 array('arquivo'=>'_excluir_comprador.php','tipo'=>'success')
);
foreach($casos as $caso){
 $saida=shell_exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(__FILE__).' '.escapeshellarg(base64_encode(json_encode($caso))));$r=json_decode($saida,true);
 $alerta=$r['json']??($r['sessao']['alerta_compradores']??null);
 if(($alerta['tipo']??null)!==$caso['tipo'])throw new RuntimeException('Alerta incorreto: '.json_encode($caso).' '.$saida);
 if($caso['tipo']==='warning'&&$r['gravacoes'])throw new RuntimeException('Validação inválida gravou');
 if($caso['tipo']==='success'&&count($r['gravacoes'])!==1)throw new RuntimeException('Gravação ausente');
 if(($caso['post']['formato']??'')==='json'&&$caso['tipo']==='success'&&($r['json']['comprador']['id']??0)!==9)throw new RuntimeException('Contrato da modal alterado');
 if($caso['arquivo']==='_comprador.php'&&$caso['tipo']==='warning'&&($caso['post']['formato']??'')!=='json'&&!isset($r['sessao']['campos_comprador']['campos']))throw new RuntimeException('Campos não preservados');
}
$_SESSION=array('alerta_compradores'=>array('tipo'=>'warning','mensagem'=>'</script><script>teste</script>'),'campos_comprador'=>array('id'=>0,'campos'=>array('nome'=>'Teste')));$pg='compradores';
ob_start();require __DIR__.'/../includes/alertas_vendas.php';$html=ob_get_clean();
if(isset($_SESSION['alerta_compradores'])||isset($_SESSION['campos_comprador'])||strpos($html,'</script><script>teste')!==false||strpos($html,'dist/js/alertas.js')===false)throw new RuntimeException('Flash inválido');
echo count($casos)." cenários de compradores: cadastro, edição, exclusão, vínculos, validação, falhas, modal JSON e flash seguro: OK\n";
