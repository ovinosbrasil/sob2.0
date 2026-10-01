<?php
require __DIR__ . '/../animal/importacao/paginar_previa.php';
require __DIR__ . '/../animal/importacao/atualizar_animal.php';
function verificarPagina($ok) { if (!$ok) throw new RuntimeException('Falha na paginação/seleção'); }
$registros = array();
for ($i=1;$i<=9167;$i++) $registros[] = array('linha'=>$i,'dados'=>array('Nome'=>'Animal '.$i,'FBB/FBE'=>(string)$i,'Tat.'=>'T'.$i));
$p = paginarRegistrosPrevia($registros, '', 2, 10);
verificarPagina(count($p['registros'])===10 && $p['total']===9167 && $p['registros'][0]['linha']===11);
$p = paginarRegistrosPrevia($registros, '', 99999, 10);
verificarPagina(count($p['registros'])===7 && $p['pagina']===917);
$p = paginarRegistrosPrevia($registros, 'não existe', -1, 99999);
verificarPagina($p['total']===0 && $p['pagina']===1 && $p['limite']===10);
$registros[0]['animais_banco'] = array(array('nome'=>'ÁGUA','fbb'=>'AB123','tatuagem'=>'XYZ'));
verificarPagina(paginarRegistrosPrevia($registros, 'agua',1,10)['total']===1);
verificarPagina(paginarRegistrosPrevia($registros, 'AB123',1,10)['total']===1);
$lote = array(array('linha'=>1,'novos'=>array('nome'=>'A','fbb'=>'1')),array('linha'=>20,'novos'=>array('nome'=>'B','fbb'=>'2')));
$r = excluirCamposLotePrevia($lote,array(1=>array('nome')));
verificarPagina(count($r)===2 && $r[0]['novos']===array('fbb'=>'1') && !$r[0]['completa'] && $r[1]['completa']);
foreach (array(array(999=>array()),array(1=>array('status')),array(1=>array('nome','fbb'),20=>array('nome','fbb'))) as $invalido) {
    $falhou=false; try { excluirCamposLotePrevia($lote,$invalido); } catch(RuntimeException $e) { $falhou=true; } verificarPagina($falhou);
}
echo "Paginação de 9167 registros, busca e seleção do lote completo: OK\n";
