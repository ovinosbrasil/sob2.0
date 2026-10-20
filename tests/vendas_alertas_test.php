<?php
if (isset($argv[1])) {
    $caso=json_decode(base64_decode($argv[1]),true);
    $_POST=array_merge(array('animal_1'=>'Primeiro','animal_1_id'=>'1','animal_1_origem'=>'rebanho','animal_2_id'=>'2','animal_2_origem'=>'rebanho','preco_conjunto_1'=>'100.00','comprador'=>'Cliente','data'=>'31/01/2026','parcelas'=>'2','tipo_venda'=>'Fazenda','forma'=>'Dinheiro','observacoes'=>'Teste','valor_total'=>'1'),$caso['post']??array());
    $_GET=array_merge(array('id_animal'=>1,'tipo'=>1),$caso['get']??array());
    $_SESSION=array();$gravacoes=array();
    set_error_handler(function($n,$m){throw new RuntimeException($m);});
    function DBEscape($v){return addslashes($v);}
    function DBRead($tabela,$params='',$fields='*'){
        global $caso;
        $modo=$caso['modo']??'';
        if($tabela==='mercado')return $modo==='comprador_ausente'?false:array(array('id'=>7));
        if($tabela==='animais'){
            if(strpos($params,'status =')!==false)return false;
            if(strpos($params,"id = '404'")!==false)return false;
            return array(array('id'=>strpos($params,"id = '2'")!==false?2:1,'nome'=>'Animal','status'=>$modo==='animal_saiu'?2:0,'pai'=>9,'mae'=>10,'terceiro_pai'=>0,'terceiro_mae'=>0));
        }
        if($tabela==='vendas')return $modo==='vendido'?array(array('id'=>8)):false;
        return false;
    }
    function DBCreate($tabela,$dados,$id=false){global $gravacoes,$caso;$gravacoes[]=array('operacao'=>'create','tabela'=>$tabela,'dados'=>$dados);return ($caso['modo']??'')!=='falha';}
    function DBUpdate($tabela,$dados,$where){global $gravacoes;$gravacoes[]=array('operacao'=>'update','tabela'=>$tabela,'dados'=>$dados);return true;}
    function DBDelete($tabela,$where){global $gravacoes;$gravacoes[]=array('operacao'=>'delete','tabela'=>$tabela);return true;}
    register_shutdown_function(function(){global $gravacoes;echo json_encode(array('alerta'=>$_SESSION['alerta_vendas']??null,'campos'=>$_SESSION['campos_venda']??null,'gravacoes'=>$gravacoes));});
    $codigo=file_get_contents(__DIR__.'/../vendas/'.($caso['arquivo']??'_venda_conjunta.php'));
    $codigo=str_replace("require_once __DIR__ . '/../_config.php';",'',$codigo);
    $codigo=str_replace('__DIR__',var_export(realpath(__DIR__.'/../vendas'),true),$codigo);
    eval('?>'.$codigo);exit;
}
$casos=array(
 array('tipo'=>'success','financeiro'=>100),
 array('tipo'=>'success','post'=>array('animal_2'=>'Segundo','preco_conjunto_1'=>'33.33','preco_conjunto_2'=>'66.67','parcelas'=>'3'),'financeiro'=>100,'pacote'=>true),
 array('tipo'=>'success','post'=>array('animal_2'=>'Segundo','preco_conjunto_2'=>'200'),'financeiro'=>300),
 array('tipo'=>'warning','post'=>array('comprador'=>'')),
 array('tipo'=>'warning','modo'=>'comprador_ausente'),
 array('tipo'=>'warning','post'=>array('data'=>'31/02/2026')),
 array('tipo'=>'warning','post'=>array('parcelas'=>'0')),
 array('tipo'=>'warning','post'=>array('parcelas'=>'25')),
 array('tipo'=>'warning','post'=>array('tipo_venda'=>'Inválido')),
 array('tipo'=>'warning','post'=>array('animal_1'=>'','preco_conjunto_1'=>'')),
 array('tipo'=>'warning','post'=>array('animal_1'=>'','preco_conjunto_1'=>'100')),
 array('tipo'=>'warning','post'=>array('preco_conjunto_1'=>'0')),
 array('tipo'=>'warning','post'=>array('preco_conjunto_1'=>'abc')),
 array('tipo'=>'warning','post'=>array('animal_1'=>'Inexistente','animal_1_id'=>'404')),
 array('tipo'=>'warning','modo'=>'animal_saiu'),
 array('tipo'=>'warning','post'=>array('animal_2'=>'Primeiro','animal_2_id'=>'1','preco_conjunto_2'=>'100')),
 array('tipo'=>'warning','post'=>array('animal_2'=>'Inexistente','animal_2_id'=>'404','preco_conjunto_2'=>'100')),
 array('tipo'=>'warning','post'=>array('animal_1_id'=>'')),
 array('tipo'=>'warning','post'=>array('animal_1_origem'=>'terceiros')),
 array('tipo'=>'danger','modo'=>'falha'),
 array('arquivo'=>'_excluir_venda.php','tipo'=>'success','modo'=>'vendido'),
 array('arquivo'=>'_excluir_venda.php','tipo'=>'warning'),
 array('arquivo'=>'_excluir_venda.php','tipo'=>'warning','get'=>array('id_animal'=>0))
);
foreach($casos as $caso){
 $comando=escapeshellarg(PHP_BINARY).' '.escapeshellarg(__FILE__).' '.escapeshellarg(base64_encode(json_encode($caso)));
 $resultado=json_decode(shell_exec($comando),true);
 if(($resultado['alerta']['tipo']??null)!==$caso['tipo'])throw new RuntimeException('Alerta incorreto: '.json_encode($caso).' '.json_encode($resultado));
 if($caso['tipo']==='warning' && $resultado['gravacoes'])throw new RuntimeException('Gravou venda ou financeiro após validação inválida.');
 if($caso['tipo']==='warning' && !isset($caso['arquivo']) && !$resultado['campos'])throw new RuntimeException('Não preservou formulário.');
 if(isset($caso['financeiro'])){
  $lancamentos=array_values(array_filter($resultado['gravacoes'],function($g){return $g['tabela']==='controle_financeiro' && $g['operacao']==='create';}));
  if(array_sum(array_column(array_column($lancamentos,'dados'),'valor'))!=$caso['financeiro'])throw new RuntimeException('Total financeiro incorreto.');
  if(array_column(array_column($lancamentos,'dados'),'data')!==(!empty($caso['pacote']) ? array('2026-01-31','2026-02-28','2026-03-31') : array('2026-01-31','2026-02-28')))throw new RuntimeException('Datas das parcelas inválidas.');
  if (!empty($caso['pacote'])) {
   $dadosPacote = array_column($lancamentos, 'dados');
   if (array_column($dadosPacote, 'valor') !== array('33.34','33.33','33.33')) throw new RuntimeException('Divisão do pacote incorreta.');
   if (array_column($dadosPacote, 'titulo') !== array('Pacote de vendas 1/3','Pacote de vendas 2/3','Pacote de vendas 3/3')) throw new RuntimeException('Descrição do pacote incorreta.');
  }
  if($resultado['campos']!==null)throw new RuntimeException('Formulário não limpo após sucesso.');
 }
}
$_SESSION=array('alerta_vendas'=>array('tipo'=>'warning','mensagem'=>'</script><script>teste</script>'),'campos_venda'=>array('animal_1'=>'Teste'));$pg='vender';
ob_start();require __DIR__.'/../includes/alertas_vendas.php';$html=ob_get_clean();
if(isset($_SESSION['alerta_vendas'])||isset($_SESSION['campos_venda'])||strpos($html,'</script><script>teste')!==false||$camposVenda['animal_1']!=='Teste')throw new RuntimeException('Flash inseguro ou não consumido.');
echo count($casos)." cenários de vendas: validação antes de gravar, sucesso, falha, exclusão, total financeiro, parcelas e flash: OK\n";
