<?php
require_once __DIR__ . '/../../includes/paginacao_crias.php';
$registrosReproducao = array();
$idAnimalReproducao = (int)$id_animal;
if (($animal[0]['sexo'] ?? '') === 'Fêmea') {
    foreach ((DBRead('monta_controle', "WHERE id_animal = '$idAnimalReproducao' AND terceiro = '0' ORDER BY id DESC") ?: array()) as $controle) {
        $idLote = (int)$controle['id_monta'];
        $lote = DBRead('monta', "WHERE id = '$idLote'");
        if (!$lote) continue;
        $registrosReproducao[] = array('tipo' => 'Monta natural', 'lote' => $lote[0], 'controle' => $controle,
            'macho_id' => $lote[0]['id_animal'], 'terceiro' => $lote[0]['terceiro'],
            'inicio' => $lote[0]['data_inicio'], 'fim' => $lote[0]['data_fim'], 'receptora' => '—');
    }
    foreach ((DBRead('inseminacao_controle', "WHERE id_femea = '$idAnimalReproducao' AND terceiro = '0' ORDER BY id DESC") ?: array()) as $controle) {
        $idLote = (int)$controle['id_lote'];
        $lote = DBRead('inseminacao', "WHERE id = '$idLote'");
        if (!$lote) continue;
        $registrosReproducao[] = array('tipo' => 'Inseminação artificial', 'lote' => $lote[0], 'controle' => $controle,
            'macho_id' => $lote[0]['id_macho'], 'terceiro' => $lote[0]['terceiro'],
            'inicio' => $lote[0]['data'], 'fim' => $lote[0]['data'], 'receptora' => '—');
    }
    foreach ((DBRead('transplante', "WHERE id_mae = '$idAnimalReproducao' AND terceiro_mae = '0' ORDER BY id DESC") ?: array()) as $lote) {
        $idLote = (int)$lote['id'];
        foreach ((DBRead('transplante_controle', "WHERE id_lote = '$idLote' ORDER BY id DESC") ?: array()) as $controle) {
            $registrosReproducao[] = array('tipo' => 'Transferência de embriões', 'lote' => $lote, 'controle' => $controle,
                'macho_id' => $lote['id_pai'], 'terceiro' => $lote['terceiro_pai'],
                'inicio' => $lote['data'], 'fim' => $lote['data'], 'receptora' => $controle['receptora'] ?? '—');
        }
    }
}
usort($registrosReproducao, function ($a, $b) { return strcmp($b['inicio'] ?? '', $a['inicio'] ?? ''); });
$paginacaoReproducao = paginacaoCrias(count($registrosReproducao), array('pg' => 'animal', 'id_animal' => $idAnimalReproducao, 'aba' => 'reproducao'), 'pagina_reproducao');
$registrosPagina = array_slice($registrosReproducao, $paginacaoReproducao['inicio'], $paginacaoReproducao['por_pagina']);
$hReproducao = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$dataReproducao = function ($valor, $dias = 0) {
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$valor, 0, 10));
    if (!$data || $data->format('Y-m-d') !== substr((string)$valor, 0, 10)) return '—';
    return $data->modify('+' . (int)$dias . ' days')->format('d/m/Y');
};
?>
<div id="reproducao-animal">
  <h3 style="font-size:16px; margin:0 0 15px;">Histórico de reprodução</h3>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead><tr>
        <th>Lote</th><th>Tipo</th><th>Macho</th><th>Receptora</th><th>Data</th><th>Previsão de parto</th><th>Ultrassom/Status</th>
      </tr></thead>
      <tbody>
      <?php if (!$registrosPagina): ?>
        <tr><td colspan="7" class="text-center text-muted">Nenhum registro de reprodução encontrado para este animal.</td></tr>
      <?php endif; ?>
      <?php foreach ($registrosPagina as $registro):
          $idMacho = (int)$registro['macho_id'];
          $macho = DBRead($registro['terceiro'] ? 'terceiros' : 'animais', "WHERE id = '$idMacho'");
          $controle = $registro['controle'];
      ?>
        <tr>
          <td>Lote <?=$hReproducao($registro['lote']['codigo'])?></td>
          <td><?=$hReproducao($registro['tipo'])?></td>
          <td<?php if ($macho): ?> class="celula-animal-link" role="link" tabindex="0" onclick="<?=$registro['terceiro'] ? 'abrir_terceiro' : 'abrir_animal'?>(<?=$idMacho?>)" onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.click(); }"<?php endif; ?>><?=$macho ? $hReproducao($macho[0]['nome']) : '—'?></td>
          <td><?=$hReproducao($registro['receptora'])?></td>
          <td><?php if ($registro['tipo'] === 'Monta natural'): ?>Inicial: <?=$dataReproducao($registro['inicio'])?><br>Final: <?=$dataReproducao($registro['fim'])?><?php else: ?><?=$dataReproducao($registro['inicio'])?><?php endif; ?></td>
          <td><?=$dataReproducao($registro['inicio'], 140)?> até <?=$dataReproducao($registro['fim'], 160)?></td>
          <td>
            <?php if (($controle['ultrassom'] ?? 0) == 1): ?><span class="text-success">Positivo</span><br><?php elseif (($controle['ultrassom'] ?? 0) == 2): ?><span class="text-danger">Negativo</span><br><?php endif; ?>
            <span class="<?=($controle['status_nascimento'] ?? 0) == 1 ? 'text-success' : 'text-danger'?>"><?=($controle['status_nascimento'] ?? 0) == 1 ? 'Nasceu' : 'Não nasceu'?></span>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php renderPaginacaoCrias($paginacaoReproducao, 'registros'); ?>
</div>
