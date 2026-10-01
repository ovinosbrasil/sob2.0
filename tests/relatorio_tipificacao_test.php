<?php
set_error_handler(function($n,$m){throw new RuntimeException($m);});
$avaliacoesTeste = array(); $animaisTeste = array();
function DBRead($tabela,$params='',$fields='*') {
    global $avaliacoesTeste,$animaisTeste;
    if ($tabela === 'avaliacao e') return $avaliacoesTeste;
    if ($tabela === 'animais') return $animaisTeste;
    throw new RuntimeException('Consulta inesperada: '.$tabela);
}
function renderTip($filtros) { $_GET=$filtros; ob_start(); include __DIR__.'/../relatorios/relatorio_tipificacao.php'; return ob_get_clean(); }
function exigirTip($ok) { if(!$ok)throw new RuntimeException('Falha no relatório de tipificação.'); }
$html=renderTip(array()); exigirTip(strpos($html,'Nenhum animal encontrado')!==false);
for($i=1;$i<=22;$i++) {
    $animaisTeste[]=array('id'=>$i,'nome'=>'<b>Animal '.$i.'</b>','status'=>$i===1?1:0);
    $avaliacoesTeste[]=array('id_macho'=>$i,'id_femea'=>$i,'qtd_avaliadas'=>$i===22?2:3,'tamanho'=>$i);
}
$html=renderTip(array('pag'=>3)); exigirTip(strpos($html,'21 a 21 de 21')!==false && strpos($html,'&lt;b&gt;Animal 21&lt;/b&gt;')!==false);
$html=renderTip(array('animal'=>'animal 1','animal_id'=>'1','animal_origem'=>'rebanho'));
exigirTip(strpos($html,'1 a 1 de 1')!==false && strpos($html,'animal_id=1')!==false);
$html=renderTip(array('filtro'=>'Matrizes','situacao'=>'Rebanho','tipo'=>2));
exigirTip(strpos($html,'1 a 10 de 20')!==false && strpos($html,'data-tipo="femeas"')!==false && strpos($html,'filtro=Matrizes')!==false);
$html=renderTip(array('animal_id'=>'1','animal_origem'=>'terceiros')); exigirTip(strpos($html,'Nenhum animal encontrado')!==false);
echo "Tipificação: vazio, mínimo de crias, filtros, busca, ordenação e paginação: OK\n";
