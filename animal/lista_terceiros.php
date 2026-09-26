<?php
$sexo = $_GET['sexo'] ?? '';
if (!in_array($sexo, array('', 'Macho', 'Fêmea'), true)) {
  $sexo = '';
}
$filtroSexo = $sexo === '' ? '' : "WHERE sexo = '$sexo'";
$contagem = DBRead('terceiros', $filtroSexo, 'COUNT(*) AS total');
$qtd = (int)($contagem[0]['total'] ?? 0);
$porPagina = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) { $porPagina = 10; }
$qtd_pag = (int)ceil($qtd / $porPagina);
$paginaInformada = filter_var($_GET['pag'] ?? 0, FILTER_VALIDATE_INT);
$pagina = min(max(0, $paginaInformada === false ? 0 : $paginaInformada), max(0, $qtd_pag - 1));
$loop = $pagina * $porPagina;
$animais = $qtd > 0 ? (DBRead('terceiros', "$filtroSexo ORDER BY id desc LIMIT $loop,$porPagina") ?: array()) : array();
$filtros = array('sexo' => $sexo);
$urlPagina = 'geral.php?pg=lista_terceiros&amp;sexo=' . rawurlencode($sexo) . '&amp;por_pagina=' . $porPagina;
?>

<script type="text/javascript">
</script>

<section class="content-header">
  <h1>
    Animais de Terceiros
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-github-alt"></i> Animais</a></li>
    <li><a href="#">Terceiros</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box" style="border-top:0;">
        <div class="box-body">
          <form action="geral.php" method="get">
            <input type="hidden" name="pg" value="lista_terceiros">
            <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
            <div class="row">
              <div class="form-group col-sm-3">
                <label for="filtro-sexo">Sexo</label>
                <select class="form-control" id="filtro-sexo" name="sexo" onchange="this.form.submit()">
                  <option value="" <?=$sexo === '' ? 'selected' : ''?>>Todos</option>
                  <option value="Macho" <?=$sexo === 'Macho' ? 'selected' : ''?>>Macho</option>
                  <option value="Fêmea" <?=$sexo === 'Fêmea' ? 'selected' : ''?>>Fêmea</option>
                </select>
              </div>
              <div class="form-group col-sm-3">
                <label class="hidden-xs" aria-hidden="true">&nbsp;</label>
                <a class="btn btn-default btn-block" href="geral.php?pg=lista_terceiros">Limpar</a>
              </div>
            </div>
            <noscript><button type="submit" class="btn btn-default">Filtrar</button></noscript>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="box" style="border-top:0;">
        <div class="box-body">
          <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
            <tr>
              <th style="width:1%; white-space:nowrap;">Nº</th>
              <th>Animal</th>
              <th>Sexo</th>
              <th>Pai</th>
              <th>Mãe</th>
              <th style="width:1%; white-space:nowrap;"><span class="sr-only">Ações</span></th>
            </tr>
            </thead>
            <tbody>
            <?
            $x = $loop;
            if (!$animais) { ?>
            <tr><td colspan="6" class="text-center">Nenhum animal de terceiros encontrado.</td></tr>
            <?php }
            foreach ($animais as $animais_){
              $x++;
            ?>
            <tr>
              <td><?=$x?></td>
              <td onclick="abrir_terceiro(<?=(int)$animais_['id']?>)" style="cursor:pointer;" ><?=htmlspecialchars(trim((string)($animais_['nome'] ?? '')) !== '' ? $animais_['nome'] : '--', ENT_QUOTES, 'UTF-8')?></td>
              <td><?=htmlspecialchars(trim((string)($animais_['sexo'] ?? '')) !== '' ? $animais_['sexo'] : '--', ENT_QUOTES, 'UTF-8')?></td>
              <td><?=htmlspecialchars(trim((string)($animais_['pai'] ?? '')) !== '' ? $animais_['pai'] : '--', ENT_QUOTES, 'UTF-8')?></td>
              <td><?=htmlspecialchars(trim((string)($animais_['mae'] ?? '')) !== '' ? $animais_['mae'] : '--', ENT_QUOTES, 'UTF-8')?></td>
              <td style="white-space:nowrap;">
                <button type="button" class="text-primary" style="background:none; border:0; padding:0; margin-right:10px; cursor:pointer;" aria-label="Abrir dados do animal" onclick="abrir_terceiro(<?=(int)$animais_['id']?>)" title="Abrir dados do animal"><i class="fa fa-search" aria-hidden="true"></i></button>
                <button type="button" class="text-danger" style="background:none; border:0; padding:0; cursor:pointer;" aria-label="Excluir animal" onclick="excluir_terceiro(<?=(int)$animais_['id']?>)" title="Excluir animal"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
              </tr>
            <? } ?>
            </tbody>
            </table>
          </div>

          <div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
            <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
              <input type="hidden" name="pg" value="lista_terceiros">
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
              Exibindo <?=$qtd ? $loop + 1 : 0?> a <?=min($loop + $porPagina, $qtd)?> de <?=$qtd?> animais
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
            <nav aria-label="Paginação dos animais de terceiros">
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
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
