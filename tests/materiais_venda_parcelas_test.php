<?php
// Verifica os cálculos executados pelos dois handlers, sem gravar no banco.
foreach (array('semen', 'embrioes') as $material) {
    $source = file_get_contents(__DIR__ . '/../reproducao/' . $material . '/_vender.php');
    $inicio = strpos($source, '$descricaoFinanceira');
    $fim = strpos($source, 'mysqli_prepare($link', $inicio);
    $trecho = substr($source, $inicio, $fim - $inicio);
    $trecho = preg_replace('/\$stmt\s*=\s*$/', '', $trecho);
    // Mantém o corpo dos cálculos, omitindo somente a gravação SQL.
    $trecho .= '$resultadoParcelas[] = array($titulo, $dataVencimento, $valorParcela); }';
    $macho = array('nome'=>'BURIA 2705');
    $pai = array('nome'=>'Pai'); $mae = array('nome'=>'Mãe');
    $doses = $quantidade = 2;
    $totalCentavos = 10000; $parcelas = 3;
    $dataVenda = new DateTimeImmutable('2026-01-31');
    $resultadoParcelas = array();
    eval($trecho);
    $esperado = $material === 'semen' ? 'Venda de sêmen - BURIA 2705 - 2 Doses' : 'Venda de embriões - Pai / Mãe - 2 Doses';
    $soma = 0;
    foreach ($resultadoParcelas as $i => $parcelaTeste) {
        if ($parcelaTeste[0] !== $esperado . ' - ' . ($i + 1) . '/3') throw new RuntimeException('Descrição incorreta.');
        if ($parcelaTeste[1] !== array('2026-01-31','2026-02-28','2026-03-31')[$i]) throw new RuntimeException('Vencimento incorreto.');
        $soma += (int)round($parcelaTeste[2] * 100);
    }
    if (count($resultadoParcelas) !== 3 || $soma !== 10000) throw new RuntimeException('Total das parcelas incorreto.');
}
echo "Sêmen e embriões: descrição, doses, parcelas, arredondamento e vencimentos OK\n";
