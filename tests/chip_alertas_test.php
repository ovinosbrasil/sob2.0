<?php
if(isset($argv[1])) {
 $modo=$argv[1];$_GET=array('id_animal'=>$modo==='invalido'?0:7);$_SESSION=array();$gravacoes=array();
 function DBRead($t,$p){global $modo;return $modo==='ausente'?false:array(array('id'=>7,'chip'=>$modo==='sem_chip'?'0':'123'));}
 function DBUpdate($t,$d,$p){global $modo,$gravacoes;$gravacoes[]=array($t,$d,$p);if($modo==='excecao')throw new RuntimeException('Falha simulada');return $modo!=='falha';}
 register_shutdown_function(function(){global $gravacoes;echo json_encode(array('alerta'=>$_SESSION['alerta_chip']??null,'gravacoes'=>$gravacoes));});
 $codigo=file_get_contents(__DIR__.'/../chip/animal/_excluir.php');$codigo=str_replace("require_once __DIR__ . '/../../_config.php';",'',$codigo);eval('?>'.$codigo);exit;
}
foreach(array('sucesso'=>'success','invalido'=>'warning','ausente'=>'warning','sem_chip'=>'warning','falha'=>'danger','excecao'=>'danger') as $modo=>$tipo){
 $r=json_decode(shell_exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(__FILE__).' '.escapeshellarg($modo)),true);
 if(($r['alerta']['tipo']??null)!==$tipo)throw new RuntimeException('Alerta incorreto: '.$modo);
 if($tipo==='warning'&&$r['gravacoes'])throw new RuntimeException('Atualização indevida');
 if($tipo==='success'&&$r['gravacoes']!==array(array('animais',array('chip'=>0),"id = '7'")))throw new RuntimeException('Alteração indevida no animal');
}
$_SESSION=array('alerta_chip'=>array('tipo'=>'success','mensagem'=>'</script><script>teste</script>'));$pg='cadastrar_chip';ob_start();require __DIR__.'/../includes/alertas_chip.php';$html=ob_get_clean();
if(isset($_SESSION['alerta_chip'])||strpos($html,'</script><script>teste')!==false)throw new RuntimeException('Flash inseguro ou não consumido');
echo "Chip: sucesso, validação, ausência, sem chip, falha, exceção e flash seguro: OK\n";
