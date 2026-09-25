<?php
require_once __DIR__ . '/../../_config.php';

$link = DBConnect();
try {
    $executar = function ($sql, array $valores = array()) use ($link) {
        $stmt = mysqli_prepare($link, $sql);
        if (!$stmt) {
            throw new RuntimeException(mysqli_error($link));
        }
        if ($valores) {
            $tipos = str_repeat('s', count($valores));
            mysqli_stmt_bind_param($stmt, $tipos, ...$valores);
        }
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException(mysqli_stmt_error($stmt));
        }
        return $stmt;
    };
    $idSemen = filter_var($_GET['id_embriao'] ?? '', FILTER_VALIDATE_INT);
    $doses = filter_var($_POST['qtd'] ?? '', FILTER_VALIDATE_INT);
    $parcelas = filter_var($_POST['parcelas'] ?? 1, FILTER_VALIDATE_INT);
    $dataTexto = $_POST['data'] ?? '';
    $dataVenda = DateTime::createFromFormat('!d/m/Y', $dataTexto);
    $valorTexto = str_replace(',', '', $_POST['valor'] ?? '');
    if (!$idSemen || !$doses || $doses < 1 || !$parcelas || $parcelas < 1 || $parcelas > 24
        || !$dataVenda || $dataVenda->format('d/m/Y') !== $dataTexto
        || !is_numeric($valorTexto) || (float)$valorTexto < 0) {
        throw new InvalidArgumentException('Confira a data, a quantidade, o valor e as parcelas da venda.');
    }
    $totalCentavos = (int)round((float)$valorTexto * 100);
    if (!mysqli_begin_transaction($link)) {
        throw new RuntimeException(mysqli_error($link));
    }
    $stmt = $executar('SELECT * FROM semen WHERE id = ? FOR UPDATE', array($idSemen));
    $semen = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$semen || $doses > (int)$semen['qtd']) {
        throw new InvalidArgumentException('Quantidade de doses indisponível. Confira o estoque.');
    }
    $stmt = $executar('SELECT id FROM mercado WHERE nome = ? LIMIT 1', array($_POST['comprador'] ?? ''));
    $comprador = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$comprador) {
        throw new InvalidArgumentException('Comprador não encontrado. Selecione um comprador cadastrado.');
    }
    $tabelaAnimal = $semen['terceiro'] ? 'terceiros' : 'animais';
    $stmt = $executar("SELECT nome FROM $tabelaAnimal WHERE id = ?", array($semen['id_animal']));
    $macho = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$macho) {
        throw new InvalidArgumentException('Animal do sêmen não encontrado. Confira o cadastro.');
    }
    $forma = $_POST['forma'] ?? '';
    $data = $dataVenda->format('Y-m-d');
    $executar('INSERT INTO venda_semen (id_semen, data, doses, forma_de_pagamento, parcelas, tipo_venda, comprador, valor, obs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        array($idSemen, $data, $doses, $forma, $parcelas, $_POST['tipo_venda'] ?? '', $comprador['id'], $totalCentavos / 100, $_POST['observacoes'] ?? ''));
    $idVenda = mysqli_insert_id($link);
    $executar('UPDATE semen SET qtd = qtd - ? WHERE id = ?', array($doses, $idSemen));
    for ($parcela = 0; $parcela < $parcelas; $parcela++) {
        $vencimento = clone $dataVenda;
        $vencimento->modify('first day of this month');
        $vencimento->modify('+' . $parcela . ' months');
        $dia = min((int)$dataVenda->format('d'), (int)$vencimento->format('t'));
        $vencimento->setDate((int)$vencimento->format('Y'), (int)$vencimento->format('m'), $dia);
        $centavos = intdiv($totalCentavos, $parcelas) + ($parcela < $totalCentavos % $parcelas ? 1 : 0);
        // id_animal é reservado à venda de animais; o sêmen é vinculado por id_semen.
        $executar('INSERT INTO controle_financeiro (titulo, data, valor, id_animal, forma_de_pagamento, id_comprador, id_semen, categoria, id_tipo, obs, status, tipo, id_embriao) VALUES (?, ?, ?, 0, ?, ?, ?, ?, 0, ?, 0, 0, 0)',
            array('Sêmen: ' . $macho['nome'], $vencimento->format('Y-m-d'), $centavos / 100, $forma, $comprador['id'], $idVenda, '', ''));
    }
    if (!mysqli_commit($link)) {
        throw new RuntimeException(mysqli_error($link));
    }
    mysqli_close($link);
    header('Location: ../../geral.php?pg=semen');
    exit;
} catch (Exception $erro) {
    mysqli_rollback($link);
    mysqli_close($link);
    if ($erro instanceof InvalidArgumentException) {
        $mensagem = $erro->getMessage();
    } else {
        error_log('Erro na venda de sêmen: ' . $erro->getMessage());
        $mensagem = 'Não foi possível concluir a venda. Nenhuma alteração foi gravada.';
    }
    echo '<script>alert(' . json_encode($mensagem) . ');history.back();</script>';
}
