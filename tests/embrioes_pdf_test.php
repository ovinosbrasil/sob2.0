<?php
if (($argv[1] ?? '') === '--render') {
    $totalTeste = (int)$argv[2];
    set_error_handler(function ($n,$m) { throw new RuntimeException($m); });
    function DBRead($tabela,$params,$fields) {
        global $totalTeste;
        if (strpos($tabela,'embriao s')!==0 || strpos($tabela,'LEFT JOIN terceiros mt ON mt.id = s.mae')===false || strpos($params,'WHERE s.qtd > 0 ORDER BY s.data DESC, s.id DESC')===false || !preg_match('/LIMIT (\d+),200$/',$params,$m)) throw new RuntimeException('Consulta de exportação incorreta.');
        $dados=array();
        for($i=(int)$m[1]+1;$i<=min($totalTeste,(int)$m[1]+200);$i++) $dados[]=array('data'=>'2026-10-15','nome_femea'=>'Fêmea '.$i, 'terceiro_mae'=>($i+1)%2, 'nome_macho'=>'Macho '.$i.($i===1?' '.str_repeat('Nome longo ',12):''),'qtd'=>10,'botijao'=>'Botijão A','palheta'=>'ID '.$i,'qualidade'=>'Ótima','terceiro'=>$i%2);
        return $dados;
    }
    $codigo=file_get_contents(__DIR__.'/../reproducao/embrioes/_imprimir.php');
    $codigo=str_replace("require_once __DIR__ . '/../../_config.php';",'',$codigo);
    $codigo=str_replace('__DIR__',var_export(realpath(__DIR__.'/../reproducao/embrioes'),true),$codigo);
    eval('?>'.$codigo);exit;
}
foreach(array(0,405) as $total) {
    $arquivo=tempnam(sys_get_temp_dir(),'embrioes_pdf_');
    try {
        $pipes=array();
        $processo=proc_open(array(PHP_BINARY,__FILE__,'--render',(string)$total),array(0=>array('pipe','r'),1=>array('file',$arquivo,'w'),2=>array('pipe','w')),$pipes);
        fclose($pipes[0]);$erro=stream_get_contents($pipes[2]);fclose($pipes[2]);
        if(proc_close($processo)!==0)throw new RuntimeException($erro);
        if(strpos(file_get_contents($arquivo),'%PDF-')!==0)throw new RuntimeException('PDF inválido');
        $texto=shell_exec('pdftotext -layout '.escapeshellarg($arquivo).' -');
        if(strpos($texto,'Banco de embriões')===false)throw new RuntimeException('Cabeçalho ausente');
        if($total===0 && strpos($texto,'Nenhum embrião disponível')===false)throw new RuntimeException('PDF vazio incorreto');
        if($total>0 && (strpos($texto,'Macho 405')===false || strpos($texto,'Fêmea 405')===false || strpos($texto,'(Terceiro)')===false || strpos($texto,'Botijão A')===false || strpos($texto,'ID 405')===false))throw new RuntimeException('Exportação incompleta ou caracteres incorretos');
    } finally {unlink($arquivo);}
}
echo "PDF de embriões: estoque vazio, 405 registros em lotes, múltiplas páginas, nomes longos e acentos: OK\n";
