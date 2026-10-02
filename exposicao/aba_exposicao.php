<?php
require __DIR__ . '/../_config.php';
require_once __DIR__ . '/../includes/busca_animais.php';
require_once __DIR__ . '/../funcoes_data/categorias.php';
header('Content-Type: text/html; charset=UTF-8');

$idEvento = filter_var($_GET['id_exposicao'] ?? 0, FILTER_VALIDATE_INT);
$aba = is_string($_GET['aba'] ?? null) ? $_GET['aba'] : '';
if (!$idEvento || !in_array($aba, array('julgamento', 'progenie_pai', 'progenie_mae', 'vendas'), true)) { http_response_code(400); exit('Parâmetros inválidos.'); }
$h = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$evento = (DBRead('julgamento', "WHERE id = '" . (int)$idEvento . "' LIMIT 1") ?: array())[0] ?? null;
if (!$evento) { http_response_code(404); exit('<div class="alert alert-warning">Exposição não encontrada.</div>'); }
$porPagina = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) $porPagina = 10;
$pagina = max(1, (int)($_GET['pag'] ?? 1));

if ($aba === 'julgamento') {
    $registros = DBRead(
        'julgamento_controle jc INNER JOIN animais a ON a.id = jc.id_animal LEFT JOIN animais pa ON pa.id = a.pai AND COALESCE(a.terceiro_pai,0) = 0 LEFT JOIN terceiros pt ON pt.id = a.pai AND a.terceiro_pai = 1 LEFT JOIN animais ma ON ma.id = a.mae AND COALESCE(a.terceiro_mae,0) = 0 LEFT JOIN terceiros mt ON mt.id = a.mae AND a.terceiro_mae = 1',
        "WHERE jc.id_julgamento = '" . (int)$idEvento . "' ORDER BY a.sexo DESC, a.data_de_nascimento DESC, a.id ASC",
        "jc.categoria, a.id, a.nome, a.tipo, a.sexo, a.data_de_nascimento, COALESCE(pa.nome,pt.nome,'Não informado') pai_nome, COALESCE(ma.nome,mt.nome,'Não informado') mae_nome, (SELECT COUNT(*) FROM animais f WHERE f.mae=a.id AND COALESCE(f.terceiro_mae,0)=0) quantidade_crias"
    ) ?: array();
} elseif ($aba === 'vendas') {
    $registros = DBRead(
        'animais_evento ae INNER JOIN animais a ON a.id = ae.id_animal INNER JOIN vendas v ON v.id_animal = a.id LEFT JOIN mercado m ON m.id = v.comprador',
        "WHERE ae.id_julgamento = '" . (int)$idEvento . "' AND v.tipo_venda IN ('Exposição','Leilão') ORDER BY v.data DESC, v.id DESC",
        "a.id id_animal, a.nome animal_nome, v.data, v.preco_de_venda, v.parcelas, v.tipo_venda, v.forma_de_pagamento, COALESCE(m.nome,'Não informado') comprador_nome"
    ) ?: array();
} else {
    $progeniePai = $aba === 'progenie_pai';
    $campoProgenitor = $progeniePai ? 'pai' : 'mae';
    $campoOutroProgenitor = $progeniePai ? 'mae' : 'pai';
    $campoTerceiroOutro = $progeniePai ? 'terceiro_mae' : 'terceiro_pai';
    $sexoProgenitor = $progeniePai ? 'M' : 'F';
    $registros = DBRead(
        "progene p INNER JOIN animais progenitor ON progenitor.id = p.id_animal INNER JOIN julgamento_controle jc ON jc.id_julgamento = p.id_lote AND jc.$campoProgenitor = p.id_animal INNER JOIN animais cria ON cria.id = jc.id_animal LEFT JOIN animais outro_animal ON outro_animal.id = cria.$campoOutroProgenitor AND COALESCE(cria.$campoTerceiroOutro,0) = 0 LEFT JOIN terceiros outro_terceiro ON outro_terceiro.id = cria.$campoOutroProgenitor AND cria.$campoTerceiroOutro = 1",
        "WHERE p.id_lote = '" . (int)$idEvento . "' AND p.qtd > 1 AND p.sexo = '$sexoProgenitor' ORDER BY progenitor.nome ASC, cria.nome ASC",
        "progenitor.id progenitor_id, progenitor.nome progenitor_nome, cria.id cria_id, cria.nome cria_nome, cria.sexo cria_sexo, cria.tipo cria_tipo, COALESCE(outro_animal.nome,outro_terceiro.nome,'Não informado') outro_progenitor_nome"
    ) ?: array();
}
$total = count($registros);
if ($aba === 'julgamento') {
    $gruposJulgamento = array(
        'Machos' => array_values(array_filter($registros, function ($animal) { return $animal['sexo'] === 'Macho'; })),
        'Fêmeas' => array_values(array_filter($registros, function ($animal) { return $animal['sexo'] === 'Fêmea'; }))
    );
    $paginas = 1; $pagina = 1; $offset = 0; $paginaRegistros = $registros;
} elseif ($aba === 'vendas') {
    $paginas = max(1, (int)ceil($total / $porPagina)); $pagina = min($pagina, $paginas); $offset = ($pagina - 1) * $porPagina; $paginaRegistros = array_slice($registros, $offset, $porPagina);
} else {
    $progeniesAgrupadas = array();
    foreach ($registros as $registroProgenie) {
        $idProgenitor = (int)$registroProgenie['progenitor_id'];
        if (!isset($progeniesAgrupadas[$idProgenitor])) $progeniesAgrupadas[$idProgenitor] = array('nome'=>$registroProgenie['progenitor_nome'], 'crias'=>array());
        $progeniesAgrupadas[$idProgenitor]['crias'][] = $registroProgenie;
    }
    $paginas = 1; $pagina = 1; $offset = 0; $paginaRegistros = $registros;
}
?>
<?php if ($aba === 'julgamento'): ?>
<div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:15px; margin-bottom:15px;"><div style="width:100%; max-width:520px;"><?php renderBuscaAnimais(array('id'=>'animal-pista-exposicao','name'=>'animal_pista','label'=>'Adicionar animal à pista','tipo'=>'rebanho','placeholder'=>'Digite para pesquisar','classe'=>'busca-animal-pista-exposicao')); ?></div><span class="text-muted"><?=$total?> animal(is) na pista</span></div>
<?php foreach ($gruposJulgamento as $tituloGrupo => $animaisGrupo): ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin:20px 0 10px;"><strong><?=$h($tituloGrupo)?></strong><span class="text-muted"><?=count($animaisGrupo)?> animal(is)</span></div>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th style="width:60px;">Nº</th><th>Animal</th><th>Tipo</th><th>Pai</th><th>Mãe</th><th>Nascimento</th><th>Idade</th><th>Categoria</th><th><?=$tituloGrupo === 'Machos' ? 'CE' : 'Status'?></th><th style="width:1%;"><span class="sr-only">Remover</span></th></tr></thead><tbody>
<?php if (!$animaisGrupo): ?><tr><td colspan="10" class="text-center text-muted" style="padding:30px;">Nenhum animal nesta lista.</td></tr><?php endif; ?>
<?php foreach ($animaisGrupo as $indice => $animal): $nascimento = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$animal['data_de_nascimento'],0,10)); $idade = calcularIdadeMesesDias($animal['data_de_nascimento'], $evento['data']); $categoria = $idade ? determinarCategoriaPorIdade($idade['meses'],$idade['dias']) : $animal['categoria']; $idadeTexto = $idade ? intdiv($idade['meses'],12).'A '.($idade['meses']%12).'M' : 'Não informada'; $status = '—'; if ($animal['sexo']==='Macho' && $idade && $idade['meses']>=4) $status = ($idade['meses']<=6?24:($idade['meses']<=8?26:($idade['meses']<=10?28:30))).' cm'; elseif ($animal['sexo']==='Fêmea') $status = (int)$animal['quantidade_crias'] ? 'Com cria' : ($idade && $idade['meses']<14 ? 'Aprovada' : 'Sem cria'); $corStatus = $animal['sexo'] === 'Fêmea' ? ($status === 'Aprovada' ? '#008d4c' : '#dd4b39') : 'inherit'; ?>
<tr><td><?=$indice+1?></td><td><a href="geral.php?pg=animal&amp;id_animal=<?=(int)$animal['id']?>" target="_blank" rel="noopener" style="color:inherit;"><?=$h($animal['nome'])?></a></td><td><?=$h($animal['tipo'])?></td><td><?=$h($animal['pai_nome'])?></td><td><?=$h($animal['mae_nome'])?></td><td><?=$nascimento?$nascimento->format('d/m/Y'):'Não informada'?></td><td><?=$h($idadeTexto)?></td><td><?=$h($categoria)?></td><td style="color:<?=$corStatus?>;"><?=$h($status)?></td><td><button type="button" class="text-danger" style="background:none;border:0;padding:0;" data-remover-pista data-id="<?=(int)$animal['id']?>" data-nome="<?=$h($animal['nome'])?>" title="Remover da pista"><i class="fa fa-trash-o"></i></button></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php endforeach; ?>
<?php elseif ($aba === 'vendas'): ?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;"><strong>Vendas da exposição</strong><span class="text-muted"><?=$total?> venda(s)</span></div>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th style="width:60px;">Nº</th><th>Data</th><th>Animal</th><th>Comprador</th><th>Valor</th><th>Parcelas</th><th>Tipo de venda</th><th>Pagamento</th><th style="width:1%;"><span class="sr-only">Excluir</span></th></tr></thead><tbody>
<?php if (!$paginaRegistros): ?><tr><td colspan="9" class="text-center text-muted" style="padding:30px;">Nenhuma venda cadastrada para esta exposição.</td></tr><?php endif; ?>
<?php foreach ($paginaRegistros as $indice => $venda): $dataVenda=DateTimeImmutable::createFromFormat('!Y-m-d',substr((string)$venda['data'],0,10)); ?><tr><td><?=$offset+$indice+1?></td><td><?=$dataVenda?$dataVenda->format('d/m/Y'):'Não informada'?></td><td><a href="geral.php?pg=animal&amp;id_animal=<?=(int)$venda['id_animal']?>" target="_blank" rel="noopener" style="color:inherit;"><?=$h($venda['animal_nome'])?></a></td><td><?=$h($venda['comprador_nome'])?></td><td>R$ <?=number_format((float)$venda['preco_de_venda'],2,',','.')?></td><td><?=(int)$venda['parcelas']?>x</td><td><?=$h($venda['tipo_venda'])?></td><td><?=$h($venda['forma_de_pagamento'])?></td><td><button type="button" class="text-danger" style="background:none;border:0;padding:0;" data-excluir-venda-exposicao data-id="<?=(int)$venda['id_animal']?>" data-nome="<?=$h($venda['animal_nome'])?>" title="Excluir venda"><i class="fa fa-trash-o"></i></button></td></tr><?php endforeach; ?></tbody></table></div>
<?php else: $progeniePai = $aba === 'progenie_pai'; ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;"><strong><?=$progeniePai ? 'Progênie de pai' : 'Progênie de mãe'?></strong><span class="text-muted"><?=count($progeniesAgrupadas)?> progenitor(es)</span></div>
<?php if (!$progeniesAgrupadas): ?><div class="text-center text-muted" style="padding:30px;">Nenhuma progênie com duas ou mais crias encontrada neste evento.</div><?php endif; ?>
<?php foreach ($progeniesAgrupadas as $grupoProgenie): ?>
<div class="box" style="border-top:0;box-shadow:none;border:1px solid #eee;margin-bottom:20px;"><div class="box-header" style="text-align:left;"><h3 class="box-title" style="float:none;font-size:15px;font-weight:600;margin:0;"><?=$h($grupoProgenie['nome'])?> <span class="text-muted" style="font-size:13px;font-weight:normal;margin-left:8px;"><?=count($grupoProgenie['crias'])?> cria(s)</span></h3></div><div class="box-body no-padding"><div class="table-responsive"><table class="table table-bordered table-striped" style="margin-bottom:0;table-layout:fixed;width:100%;"><colgroup><col style="width:50px;"><col style="width:35%;"><col style="width:40%;"><col style="width:15%;"><col style="width:10%;"></colgroup><thead><tr><th>Nº</th><th>Cria</th><th><?=$progeniePai ? 'Mãe' : 'Pai'?></th><th>Sexo</th><th>Tipo</th></tr></thead><tbody><?php foreach ($grupoProgenie['crias'] as $indiceCria => $criaProgenie): ?><tr><td><?=$indiceCria+1?></td><td><a href="geral.php?pg=animal&amp;id_animal=<?=(int)$criaProgenie['cria_id']?>" target="_blank" rel="noopener" style="color:inherit;"><?=$h($criaProgenie['cria_nome'])?></a></td><td><?=$h($criaProgenie['outro_progenitor_nome'])?></td><td><?=$h($criaProgenie['cria_sexo'])?></td><td><?=$h($criaProgenie['cria_tipo'])?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php endforeach; ?>
<?php endif; ?>
<?php if ($aba === 'vendas'): ?><form data-paginacao-aba-exposicao style="display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:16px;border-top:1px solid #f4f4f4;padding-top:10px;"><label style="margin:0;font-weight:normal;">Por página</label><select class="form-control" name="por_pagina" style="width:70px;"><?php foreach(array(10,20,50,100) as $limite): ?><option value="<?=$limite?>" <?=$limite===$porPagina?'selected':''?>><?=$limite?></option><?php endforeach; ?></select><span class="text-muted">Exibindo <?=$total?$offset+1:0?> a <?=min($offset+$porPagina,$total)?> de <?=$total?> registros</span><ul class="pagination pagination-sm" style="margin:0;"><li class="<?=$pagina===1?'disabled':''?>"><a href="#" data-pagina="<?=max(1,$pagina-1)?>">«</a></li><?php for($p=1;$p<=$paginas;$p++): if($p!==1&&$p!==$paginas&&abs($p-$pagina)>1){if($p===2||$p===$paginas-1)echo '<li class="disabled"><span>…</span></li>';continue;} ?><li class="<?=$p===$pagina?'active':''?>"><a href="#" data-pagina="<?=$p?>"><?=$p?></a></li><?php endfor; ?><li class="<?=$pagina===$paginas?'disabled':''?>"><a href="#" data-pagina="<?=min($paginas,$pagina+1)?>">»</a></li></ul></form><?php endif; ?>
