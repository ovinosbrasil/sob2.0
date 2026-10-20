<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') avisarFinanceiro('warning', 'Envie o formulário para cadastrar o lançamento.');
$dados = dadosFinanceiro($sufixoFinanceiro, $tipoFinanceiro);
$parcelas = $dados['parcelas'];
unset($dados['parcelas']);
$dados['tipo'] = $tipoFinanceiro;
$dados['status'] = 0;
// Campos obrigatórios do schema legado para lançamentos avulsos.
$dados += array('categoria' => '', 'id_tipo' => 0, 'obs' => '', 'id_animal' => 0,
    'id_comprador' => 0, 'id_embriao' => 0, 'id_semen' => 0);
$totalCentavos = (int)round((float)$dados['valor'] * 100);
if ($totalCentavos < $parcelas) avisarFinanceiro('warning', 'O valor total deve permitir ao menos R$ 0,01 por parcela.');
$valorBase = intdiv($totalCentavos, $parcelas);
$centavosRestantes = $totalCentavos % $parcelas;
$tituloBase = $dados['titulo'];
if (preg_match_all('/./us', $tituloBase . ' ' . $parcelas . '/' . $parcelas) > 50) {
    avisarFinanceiro('warning', 'Reduza a descrição para incluir a identificação da parcela (máximo de 50 caracteres no total).');
}
$primeiraData = new DateTime($dados['data']);
for ($i = 0; $i < $parcelas; $i++) {
    $mes = (clone $primeiraData)->modify('first day of this month')->modify('+' . $i . ' months');
    $dia = min((int)$primeiraData->format('d'), (int)$mes->format('t'));
    $dados['data'] = $mes->format('Y-m-') . sprintf('%02d', $dia);
    $centavos = $valorBase + ($i < $centavosRestantes ? 1 : 0);
    $dados['valor'] = intdiv($centavos, 100) . '.' . sprintf('%02d', $centavos % 100);
    $dados['titulo'] = $tituloBase . ' ' . ($i + 1) . '/' . $parcelas;
    gravarFinanceiro('DBCreate', 'controle_financeiro', $dados);
}
unset($_SESSION['campos_financeiro']);
avisarFinanceiro('success', $tipoFinanceiro ? 'Despesa cadastrada com sucesso.' : 'Receita cadastrada com sucesso.');
