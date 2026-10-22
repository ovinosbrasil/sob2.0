<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;"><strong>Conjunto Família</strong><span class="text-muted"><?=$total?> conjunto(s)</span></div>
<p class="text-muted">Fêmeas na pista com mãe viva de menos de 36 meses na data da exposição e avó materna viva.</p>
<div class="table-responsive"><table class="table table-bordered table-striped">
<thead><tr><th style="width:60px;">Nº</th><th>Fêmea na pista</th><th>Mãe</th><th>Avó materna</th></tr></thead>
<tbody>
<?php if (!$paginaRegistros): ?><tr><td colspan="4" class="text-center text-muted" style="padding:30px;">Nenhum conjunto família atende aos critérios nesta exposição.</td></tr><?php endif; ?>
<?php foreach ($paginaRegistros as $indice => $familia): $idadeMae = calcularIdadeMesesDias($familia['mae_nascimento'], $evento['data']); ?>
<tr><td><?=$offset + $indice + 1?></td>
<td><a href="geral.php?pg=animal&amp;id_animal=<?=(int)$familia['filha_id']?>" target="_blank" rel="noopener" style="color:inherit;"><?=$h($familia['filha_nome'])?></a><br><small class="text-muted">Tipo: <?=$h($familia['filha_tipo'])?></small></td>
<td><a href="geral.php?pg=animal&amp;id_animal=<?=(int)$familia['mae_id']?>" target="_blank" rel="noopener" style="color:inherit;"><?=$h($familia['mae_nome'])?></a><br><small class="text-muted"><?=$idadeMae ? (int)$idadeMae['meses'] . ' meses e ' . (int)$idadeMae['dias'] . ' dias' : 'Idade não informada'?></small></td>
<td><a href="geral.php?pg=animal&amp;id_animal=<?=(int)$familia['avo_id']?>" target="_blank" rel="noopener" style="color:inherit;"><?=$h($familia['avo_nome'])?></a></td></tr>
<?php endforeach; ?>
</tbody></table></div>
