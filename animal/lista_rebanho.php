<?php
require_once __DIR__ . '/_filtros_rebanho.php';
$filtros = filtrosRebanho($_GET);
list($fonte, $condicao) = fonteRebanho($filtros);
$contagem = DBRead($fonte, $condicao, 'COUNT(*) AS total');
$totalAnimais = (int)($contagem[0]['total'] ?? 0);
$porPagina = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) {
    $porPagina = 10;
}
$qtd_pag = (int)ceil($totalAnimais / $porPagina);
$paginaInformada = filter_var($_GET['pag'] ?? 0, FILTER_VALIDATE_INT);
$pagina = min(max(0, $paginaInformada === false ? 0 : $paginaInformada), max(0, $qtd_pag - 1));
$loop = $pagina * $porPagina;
$animais = DBRead($fonte, "$condicao ORDER BY id DESC, origem ASC LIMIT $loop,$porPagina") ?: array();
$parametros = htmlspecialchars(http_build_query($filtros), ENT_QUOTES, 'UTF-8');
$urlPagina = 'geral.php?pg=lista_rebanho&amp;' . $parametros . '&amp;por_pagina=' . $porPagina;
?>
<section class="content-header">
  <h1>Rebanho</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-github-alt"></i> Animais</a></li>
    <li><a href="#">Rebanho</a></li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box" style="border-top:0;">
        <div class="box-body">
          <form action="geral.php" method="get">
            <div class="row">
            <input type="hidden" name="pg" value="lista_rebanho">
            <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
            <?php foreach (array(
                'tipo' => array('Tipo', array('Todos' => 'Todos', 'Rebanho' => 'Rebanho', 'Terceiros' => 'Terceiros')),
                'sexo' => array('Sexo', array('' => 'Todos', 'Macho' => 'Macho', 'Fêmea' => 'Fêmea')),
                'situacao' => array('Situação', array('Todos' => 'Todos', 'Vivo' => 'Vivo', 'Morto' => 'Morto', 'Vendido' => 'Vendido')),
            ) as $campo => $opcoes): ?>
            <div class="form-group col-sm-3">
              <label for="filtro-<?=$campo?>"><?=$opcoes[0]?></label>
              <select class="form-control" id="filtro-<?=$campo?>" name="<?=$campo?>" onchange="this.form.submit()">
                <?php foreach ($opcoes[1] as $valor => $rotulo): ?>
                <option value="<?=$valor?>" <?=$filtros[$campo] === $valor ? 'selected' : ''?>><?=$rotulo?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php endforeach; ?>
              <div class="form-group col-sm-3">
                <label class="hidden-xs" aria-hidden="true">&nbsp;</label>
                <div style="display:flex; gap:8px;">
                  <a class="btn btn-primary" style="flex:1;" href="animal/_imprimir_rebanho.php?<?=$parametros?>" >Gerar PDF</a>
                  <a class="btn btn-default" style="flex:1;" href="geral.php?pg=lista_rebanho">Limpar</a>
                </div>
              </div>
            </div>
            <noscript><button type="submit" class="btn btn-default">Filtrar</button></noscript>
          </form>
          <?php if ($filtros['tipo'] !== 'Rebanho'): ?>
          <p class="help-block">Animais de terceiros não possuem situação registrada e aparecem somente na situação Todos.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="box" style="border-top:0;">
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead><tr><th style="width:1%; white-space:nowrap;">Nº</th><th>Animal</th><th>Sexo</th><th>Nascimento</th><th>Idade</th><th>Entrada</th><th>Tipo</th><th style="width:1%; white-space:nowrap;"><span class="sr-only">Ações</span></th></tr></thead>
              <tbody>
                <?php if (!$animais): ?>
                <tr><td colspan="8" class="text-center">Nenhum animal encontrado para estes filtros.</td></tr>
                <?php endif; ?>
                <?php foreach ($animais as $indice => $animal):
                    list($nascimento, $idade) = nascimentoRebanho($animal['data_de_nascimento']);
                    $abrir = $animal['origem'] === 'Terceiros' ? 'abrir_terceiro' : 'abrir_animal';
                    $situacao = array(0 => 'Rebanho', 1 => 'Morto', 2 => 'Vendido', 3 => 'Empréstimo', 4 => 'Doação', 5 => 'Abate');
                ?>
                <tr<?=(int)$animal['status'] === 1 ? ' style="color:red;"' : ((int)$animal['status'] === 2 ? ' style="color:green;"' : '')?>>
                  <td><?=$loop + $indice + 1?></td>
                  <td onclick="<?=$abrir?>('<?=(int)$animal['id']?>')" style="cursor:pointer;"><?=htmlspecialchars($animal['nome'], ENT_QUOTES, 'UTF-8')?> (<?=$animal['origem'] === 'Terceiros' ? 'Terceiro' : ($animal['status'] === null ? '--' : ($situacao[$animal['status']] ?? '--'))?>)</td>
                  <td><?=htmlspecialchars($animal['sexo'], ENT_QUOTES, 'UTF-8')?></td>
                  <td><?=$nascimento?></td>
                  <td><?=$idade?></td>
                  <td><?=$animal['entrada'] === null ? '--' : (array(0 => 'Nascimento', 1 => 'Compra')[$animal['entrada']] ?? '--')?></td>
                  <td><?=htmlspecialchars((string)($animal['tipo'] ?? '--'), ENT_QUOTES, 'UTF-8')?></td>
                  <td style="white-space:nowrap;">
                    <button type="button" class="text-primary" style="background:none; border:0; padding:0; margin-right:10px; cursor:pointer;" onclick="<?=$abrir?>('<?=(int)$animal['id']?>')" title="Abrir dados do animal" aria-label="Abrir dados do animal"><i class="fa fa-search" aria-hidden="true"></i></button>
                    <button type="button" class="text-danger" style="background:none; border:0; padding:0; cursor:pointer;" data-id="<?=(int)$animal['id']?>" data-origem="<?=$animal['origem']?>" data-nome="<?=htmlspecialchars($animal['nome'], ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoRebanho(this)" title="Excluir animal" aria-label="Excluir animal"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
            <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
              <input type="hidden" name="pg" value="lista_rebanho">
              <?php foreach ($filtros as $campo => $valor): ?>
              <input type="hidden" name="<?=$campo?>" value="<?=htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')?>">
              <?php endforeach; ?>
              <label for="por-pagina" style="margin:0; font-weight:normal;">Por página</label>
              <select id="por-pagina" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
                <?php foreach (array(10, 20, 50, 100) as $quantidade): ?>
                <option value="<?=$quantidade?>" <?=$porPagina === $quantidade ? 'selected' : ''?>><?=$quantidade?></option>
                <?php endforeach; ?>
              </select>
              <noscript><button type="submit" class="btn btn-default btn-sm">Aplicar</button></noscript>
            </form>
            <span class="text-muted">
              Exibindo <?=$totalAnimais ? $loop + 1 : 0?> a <?=min($loop + $porPagina, $totalAnimais)?> de <?=$totalAnimais?> animais
            </span>
            <?php if ($qtd_pag > 1):
                $paginasVisiveis = array(0, $qtd_pag - 1);
                $inicioPaginas = max(0, min($pagina - 1, $qtd_pag - 3));
                $fimPaginas = min($qtd_pag - 1, max($pagina + 1, 2));
                for ($numero = $inicioPaginas; $numero <= $fimPaginas; $numero++) {
                    $paginasVisiveis[] = $numero;
                }
                $paginasVisiveis = array_unique($paginasVisiveis);
                sort($paginasVisiveis);
                $anterior = -1;
            ?>
            <nav aria-label="Paginação do rebanho">
              <ul class="pagination pagination-sm no-margin">
                <?php if ($pagina > 0): ?>
                <li><a href="<?=$urlPagina?>&amp;pag=<?=$pagina-1?>" aria-label="Página anterior" title="Página anterior" rel="prev">&laquo;</a></li>
                <?php else: ?>
                <li class="disabled"><span aria-label="Página anterior" aria-disabled="true">&laquo;</span></li>
                <?php endif; ?>
                <?php foreach ($paginasVisiveis as $numero): ?>
                  <?php if ($anterior >= 0 && $numero > $anterior + 1): ?>
                  <li class="disabled"><span aria-hidden="true">&hellip;</span></li>
                  <?php endif; ?>
                  <?php if ($numero === $pagina): ?>
                  <li class="active"><span aria-current="page" aria-label="Página <?=$numero+1?>"><?=$numero+1?></span></li>
                  <?php else: ?>
                  <li><a href="<?=$urlPagina?>&amp;pag=<?=$numero?>" aria-label="Página <?=$numero+1?>"><?=$numero+1?></a></li>
                  <?php endif; ?>
                <?php $anterior = $numero; endforeach; ?>
                <?php if ($pagina < $qtd_pag - 1): ?>
                <li><a href="<?=$urlPagina?>&amp;pag=<?=$pagina+1?>" aria-label="Próxima página" title="Próxima página" rel="next">&raquo;</a></li>
                <?php else: ?>
                <li class="disabled"><span aria-label="Próxima página" aria-disabled="true">&raquo;</span></li>
                <?php endif; ?>
              </ul>
            </nav>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
