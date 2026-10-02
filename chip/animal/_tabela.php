<?php
function chipH($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead><tr>
      <th style="width:1%;">Nº</th><th>Animal</th><th>Sexo</th><th>Data de nascimento</th><th>FBB</th><th>Chip</th>
      <th style="width:1%; white-space:nowrap;"><span class="sr-only">Ações</span></th>
    </tr></thead>
    <tbody>
      <?php if (!$animaisChip): ?><tr><td colspan="7" class="text-center">Nenhum animal encontrado.</td></tr><?php endif; ?>
      <?php foreach ($animaisChip as $indice => $animalChip):
          $temChip = !empty($animalChip['chip']) && $animalChip['chip'] !== '0';
          $nascimentoChip = !empty($animalChip['data_de_nascimento']) && $animalChip['data_de_nascimento'] !== '0000-00-00'
              ? implode('/', array_reverse(explode('-', $animalChip['data_de_nascimento']))) : '—';
      ?>
      <tr>
        <td><?=$inicioChip + $indice + 1?></td>
        <td><?=chipH($animalChip['nome'])?></td>
        <td><?=chipH($animalChip['sexo'])?></td>
        <td><?=chipH($nascimentoChip)?></td>
        <td><?=chipH($animalChip['fbb'] ?? '')?></td>
        <td><?= $temChip ? chipH($animalChip['chip']) : '<span class="text-muted">Não cadastrado</span>' ?></td>
        <td style="white-space:nowrap;">
          <a class="text-primary" style="margin-right:10px;" href="geral.php?pg=animal&amp;id_animal=<?=(int)$animalChip['id']?>" target="_blank" rel="noopener" title="Abrir animal" aria-label="Abrir animal"><i class="fa fa-search" aria-hidden="true"></i></a>
          <?php if ($temChip): ?>
          <button type="button" class="text-danger" style="background:none; border:0; padding:0; cursor:pointer;" data-id="<?=(int)$animalChip['id']?>" data-nome="<?=chipH($animalChip['nome'])?>" onclick="excluirChip(this)" title="Excluir chip" aria-label="Excluir chip"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
          <?php else: ?>
          <button type="button" class="text-success" style="background:none; border:0; padding:0; cursor:pointer;" data-id="<?=(int)$animalChip['id']?>" data-nome="<?=chipH($animalChip['nome'])?>" onclick="cadastrarChip(this)" title="Cadastrar chip" aria-label="Cadastrar chip"><i class="fa fa-plus" aria-hidden="true"></i></button>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
  <div style="display:flex; align-items:center; gap:8px;">
    <label for="por-pagina-chip" style="margin:0; font-weight:normal;">Por página</label>
    <select id="por-pagina-chip" class="form-control input-sm" style="width:auto;">
      <?php foreach (array(10, 20, 50, 100) as $quantidadeChip): ?>
      <option value="<?=$quantidadeChip?>" <?=$porPaginaChip === $quantidadeChip ? 'selected' : ''?>><?=$quantidadeChip?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <span class="text-muted">Exibindo <?=$totalChip ? $inicioChip + 1 : 0?> a <?=min($inicioChip + $porPaginaChip, $totalChip)?> de <?=$totalChip?> animais</span>
  <?php if ($paginasChip > 1):
      $visiveisChip = array(1, $paginasChip);
      for ($numeroChip = max(1, $paginaChip - 1); $numeroChip <= min($paginasChip, $paginaChip + 1); $numeroChip++) $visiveisChip[] = $numeroChip;
      $visiveisChip = array_unique($visiveisChip);
      sort($visiveisChip);
      $anteriorChip = 0;
      $urlChip = 'geral.php?' . http_build_query(array('pg' => 'cadastrar_chip', 'nome' => $nomeChip, 'por_pagina' => $porPaginaChip));
  ?>
  <nav aria-label="Paginação dos animais">
    <ul class="pagination pagination-sm no-margin">
      <?php if ($paginaChip > 1): ?>
      <li><a href="<?=chipH($urlChip . '&pagina=' . ($paginaChip - 1))?>" data-pagina-chip="<?=$paginaChip - 1?>" aria-label="Página anterior">&laquo;</a></li>
      <?php else: ?><li class="disabled"><span aria-disabled="true" aria-label="Página anterior">&laquo;</span></li><?php endif; ?>
      <?php foreach ($visiveisChip as $numeroChip): ?>
      <?php if ($anteriorChip && $numeroChip > $anteriorChip + 1): ?><li class="disabled"><span>&hellip;</span></li><?php endif; ?>
      <?php if ($numeroChip === $paginaChip): ?>
      <li class="active"><span aria-current="page"><?=$numeroChip?></span></li>
      <?php else: ?>
      <li><a href="<?=chipH($urlChip . '&pagina=' . $numeroChip)?>" data-pagina-chip="<?=$numeroChip?>"><?=$numeroChip?></a></li>
      <?php endif; $anteriorChip = $numeroChip; endforeach; ?>
      <?php if ($paginaChip < $paginasChip): ?>
      <li><a href="<?=chipH($urlChip . '&pagina=' . ($paginaChip + 1))?>" data-pagina-chip="<?=$paginaChip + 1?>" aria-label="Próxima página">&raquo;</a></li>
      <?php else: ?><li class="disabled"><span aria-disabled="true" aria-label="Próxima página">&raquo;</span></li><?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>
</div>
