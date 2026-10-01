<?php
require_once __DIR__ . '/../reproducao/arco/_codigo_nascimento.php';
require_once __DIR__ . '/../reproducao/arco/_relatorio_arco_pdf.php';
$casos = array(
    array(array('status' => 1, 'causa_da_perda' => 'Nascimento'), 'Óbito'),
    array(array('status' => '1', 'causa_da_perda' => ' nascimento '), 'Óbito'),
    array(array('status' => 0, 'causa_da_perda' => 'Nascimento'), ''),
    array(array('status' => 1, 'causa_da_perda' => 'Doença'), ''),
    array(array('status' => 1, 'causa_da_perda' => ''), ''),
    array(array('status' => 2, 'causa_da_perda' => 'Nascimento'), ''),
    array(array(), '')
);
foreach ($casos as $caso) {
    if (codigoNascimentoArco($caso[0]) !== $caso[1]) { throw new RuntimeException('Código incorreto para a situação de nascimento.'); }
}
$linha = array('fbb' => 'F001', 'nome' => 'ANIMAL TESTE', 'tatuagem' => '001', 'sexo' => 'M', 'nascimento' => '01/01/2026', 'cod' => codigoNascimentoArco($casos[0][0]), 'pai_nome' => 'PAI', 'pai_fbb' => 'F002', 'mae_nome' => 'MAE', 'mae_fbb' => 'F003');
foreach (array('Monta Natural', 'Inseminação Artificial', 'Embrionagem') as $tipo) {
    $pdf = new RelatorioArcoPDF(array(), 'TESTE', 'Dorper', $tipo);
    $pdf->SetCompression(false);
    $pdf->paginaAnimais(array($linha));
    $conteudo = $pdf->Output('S');
    if (strpos($conteudo, iconv('UTF-8', 'Windows-1252', 'Óbito')) === false) { throw new RuntimeException('Óbito não aparece no PDF: ' . $tipo); }
}
echo "COD de nascimento: óbito no nascimento, demais situações em branco e PDF nas três modalidades: OK\n";
