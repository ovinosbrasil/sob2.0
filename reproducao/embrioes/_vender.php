<?php
require_once __DIR__ . '/../../_config.php';
$idEmbriao = filter_input(INPUT_GET, 'id_embriao', FILTER_VALIDATE_INT);
$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$idCompradorSelecionado = filter_input(INPUT_POST, 'comprador_id', FILTER_VALIDATE_INT);
$dados = array('comprador'=>trim((string)($_POST['comprador']??'')),'comprador_id'=>(string)($_POST['comprador_id']??''),'data'=>trim((string)($_POST['data']??'')),'parcelas'=>(string)($_POST['parcelas']??'1'),'qtd'=>(string)($_POST['qtd']??''),'tipo_venda'=>trim((string)($_POST['tipo_venda']??'')),'valor'=>trim((string)($_POST['valor']??'')),'forma'=>trim((string)($_POST['forma']??'')),'observacoes'=>trim((string)($_POST['observacoes']??'')));
$link = null; $emTransacao = false;
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$idEmbriao) { throw new InvalidArgumentException('Registro de embrião inválido.'); }
    if (empty($_SESSION['embriao_venda_csrf']) || !hash_equals($_SESSION['embriao_venda_csrf'], $token)) { throw new InvalidArgumentException('Sua sessão expirou. Atualize a página e tente novamente.'); }
    $quantidade = filter_var($_POST['qtd'] ?? '', FILTER_VALIDATE_INT);
    $parcelas = filter_var($_POST['parcelas'] ?? 1, FILTER_VALIDATE_INT);
    $dataVenda = DateTimeImmutable::createFromFormat('!d/m/Y', $dados['data']);
    $valorNormalizado = preg_replace('/[^0-9,.-]/', '', $dados['valor']);
    if (strpos($valorNormalizado, ',') !== false) { $valorNormalizado = str_replace('.', '', $valorNormalizado); $valorNormalizado = str_replace(',', '.', $valorNormalizado); }
    if (!$quantidade || $quantidade < 1 || !$parcelas || $parcelas < 1 || $parcelas > 24 || !$dataVenda || $dataVenda->format('d/m/Y') !== $dados['data'] || !is_numeric($valorNormalizado) || (float)$valorNormalizado < 0 || !in_array($dados['tipo_venda'], array('Fazenda','Leilão','Exposição','Virtual'), true) || !in_array($dados['forma'], array('','Boleto','Cheque','Dinheiro','Depósito','Transferência','Troca'), true) || !$idCompradorSelecionado || $idCompradorSelecionado < 1 || mb_strlen($dados['observacoes'], 'UTF-8') > 200) {
        throw new InvalidArgumentException('Confira o comprador, a data, a quantidade, o valor e o tipo da venda.');
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link=DBConnect(); mysqli_set_charset($link,'utf8mb4'); mysqli_begin_transaction($link); $emTransacao=true;
    $stmt=mysqli_prepare($link,'SELECT pai, mae, terceiro, terceiro_mae, qtd FROM embriao WHERE id = ? FOR UPDATE'); mysqli_stmt_bind_param($stmt,'i',$idEmbriao); mysqli_stmt_execute($stmt); $embriao=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if (!$embriao || $quantidade > (int)$embriao['qtd']) { throw new InvalidArgumentException('Quantidade indisponível. Confira o estoque.'); }
    $stmt=mysqli_prepare($link,'SELECT id FROM mercado WHERE id = ? LIMIT 1'); mysqli_stmt_bind_param($stmt,'i',$idCompradorSelecionado); mysqli_stmt_execute($stmt); $comprador=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if (!$comprador) { throw new InvalidArgumentException('Comprador não encontrado. Selecione um comprador cadastrado.'); }
    $tabelaPai=!empty($embriao['terceiro'])?'terceiros':'animais'; $tabelaMae=!empty($embriao['terceiro_mae'])?'terceiros':'animais';
    $stmt=mysqli_prepare($link,"SELECT nome FROM $tabelaPai WHERE id = ?" . ($tabelaPai === 'terceiros' ? ' AND ativo = 1' : '')); mysqli_stmt_bind_param($stmt,'i',$embriao['pai']); mysqli_stmt_execute($stmt); $pai=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    $stmt=mysqli_prepare($link,"SELECT nome FROM $tabelaMae WHERE id = ?" . ($tabelaMae === 'terceiros' ? ' AND ativo = 1' : '')); mysqli_stmt_bind_param($stmt,'i',$embriao['mae']); mysqli_stmt_execute($stmt); $mae=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if (!$pai || !$mae) { throw new InvalidArgumentException('Pai ou mãe do embrião não encontrado. Confira o cadastro.'); }
    $valor=(float)$valorNormalizado; $totalCentavos=(int)round($valor*100); $dataBanco=$dataVenda->format('Y-m-d'); $idComprador=(int)$comprador['id'];
    $stmt=mysqli_prepare($link,'INSERT INTO venda_embriao (id_embriao, data, doses, forma_de_pagamento, parcelas, tipo_venda, comprador, valor, obs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'); mysqli_stmt_bind_param($stmt,'isisisids',$idEmbriao,$dataBanco,$quantidade,$dados['forma'],$parcelas,$dados['tipo_venda'],$idComprador,$valor,$dados['observacoes']); mysqli_stmt_execute($stmt); $idVenda=mysqli_insert_id($link); mysqli_stmt_close($stmt);
    $stmt=mysqli_prepare($link,'UPDATE embriao SET qtd = qtd - ? WHERE id = ?'); mysqli_stmt_bind_param($stmt,'ii',$quantidade,$idEmbriao); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    $titulo='Embrião: '.$pai['nome'].' / '.$mae['nome'];
    for($parcela=0;$parcela<$parcelas;$parcela++){
        $vencimento=$dataVenda->modify('first day of this month')->modify('+'.$parcela.' months'); $dia=min((int)$dataVenda->format('d'),(int)$vencimento->format('t')); $vencimento=$vencimento->setDate((int)$vencimento->format('Y'),(int)$vencimento->format('m'),$dia); $centavos=intdiv($totalCentavos,$parcelas)+($parcela<$totalCentavos%$parcelas?1:0); $valorParcela=$centavos/100; $dataVencimento=$vencimento->format('Y-m-d');
        $stmt=mysqli_prepare($link,"INSERT INTO controle_financeiro (titulo,data,valor,id_animal,forma_de_pagamento,id_comprador,id_semen,categoria,id_tipo,obs,status,tipo,id_embriao) VALUES (?,?,?,0,?,?,0,'',0,'',0,0,?)"); mysqli_stmt_bind_param($stmt,'ssdsii',$titulo,$dataVencimento,$valorParcela,$dados['forma'],$idComprador,$idVenda); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    }
    mysqli_commit($link); $emTransacao=false; DBClose($link); $_SESSION['alerta_embrioes']=array('tipo'=>'success','titulo'=>'Sucesso!','mensagem'=>'Venda de embriões cadastrada com sucesso.'); header('Location: ../../geral.php?pg=embrioes',true,303); exit;
} catch(Exception $e) {
    if($link){if($emTransacao){mysqli_rollback($link);} DBClose($link);} $erro=$e instanceof InvalidArgumentException?$e->getMessage():'Não foi possível concluir a venda. Nenhuma alteração foi gravada.'; if(!($e instanceof InvalidArgumentException)){error_log('Erro na venda de embrião: '.$e->getMessage());}
}
$_SESSION['alerta_embrioes']=array('tipo'=>'warning','titulo'=>'Atenção!','mensagem'=>$erro);
$_SESSION['embriao_venda_flash']=array('erro'=>$erro,'dados'=>$dados);
header('Location: ../../geral.php?pg=vender_embrioes&id_embriao='.(int)$idEmbriao,true,303); exit;
