<?php
require_once __DIR__ . '/../../_config.php';
require_once __DIR__ . '/_relatorio_arco_pdf.php';

$tipos=array('monta'=>array('banco'=>'Monta Natural','arquivo'=>'monta-natural'),'inseminacao'=>array('banco'=>'Inseminação Artificial','arquivo'=>'inseminacao-artificial'),'te'=>array('banco'=>'Embrionagem','arquivo'=>'transplante-embrioes'));
$modalidade=isset($_GET['modalidade'])&&isset($tipos[$_GET['modalidade']])?$_GET['modalidade']:'';
$inicio=DateTimeImmutable::createFromFormat('!Y-m-d',substr((string)($_GET['data_inicial']??''),0,10));
$fim=DateTimeImmutable::createFromFormat('!Y-m-d',substr((string)($_GET['data_final']??''),0,10));
if(!$modalidade||!$inicio||!$fim){ header('Location: ../../geral.php?pg=relatorio_arco'); exit; }
$tipo=$tipos[$modalidade]; $inicioSql=$inicio->format('Y-m-d'); $fimSql=$fim->format('Y-m-d');
$tipoEscapado=DBEscape($tipo['banco']);
$animais=DBRead('animais',"WHERE data_de_nascimento >= '$inicioSql' AND data_de_nascimento <= '$fimSql' AND tipo_reproducao = '$tipoEscapado' ORDER BY data_de_nascimento ASC, id ASC")?:array();
$linhas=array();
foreach($animais as $animal){
    $idPai=(int)($animal['pai']??0); $pais=$idPai?(DBRead(!empty($animal['terceiro_pai'])?'terceiros':'animais',"WHERE id = '$idPai' LIMIT 1")?:array()):array();
    $idMae=(int)($animal['mae']??0); $maes=$idMae?(DBRead(!empty($animal['terceiro_mae'])?'terceiros':'animais',"WHERE id = '$idMae' LIMIT 1")?:array()):array();
    $data=DateTimeImmutable::createFromFormat('!Y-m-d',substr((string)($animal['data_de_nascimento']??''),0,10));
    $linhas[]=array('fbb'=>$animal['fbb']??'','nome'=>$animal['nome']??'','tatuagem'=>$animal['tatuagem']??'','sexo'=>$animal['sexo']??'','nascimento'=>$data?$data->format('d/m/Y'):'--','pai_nome'=>$pais[0]['nome']??'--','pai_fbb'=>$pais[0]['fbb']??'','mae_nome'=>$maes[0]['nome']??'--','mae_fbb'=>$maes[0]['fbb']??'');
}
$usuarios=DBRead('admin')?:array();
$pdf=new RelatorioArcoPDF($usuarios[0]??array(),$_GET['numeracao']??'',$_GET['raca']??'',$tipo['banco']);
$paginas=$linhas?array_chunk($linhas,28):array(array());
foreach($paginas as $pagina)$pdf->paginaAnimais($pagina);
$momento=(new DateTimeImmutable('now',new DateTimeZone('America/Bahia')))->format('Y-m-d-His');
$pdf->Output('D','relatorio-arco-'.$tipo['arquivo'].'-'.$momento.'.pdf');
exit;
