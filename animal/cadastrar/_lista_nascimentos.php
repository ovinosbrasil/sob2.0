<?php
function consultarUltimosNascimentos(array $entrada)
{
    $limite = filter_var($entrada['por_pagina_nascimentos'] ?? 15, FILTER_VALIDATE_INT);
    if (!in_array($limite, array(15, 30, 50, 100), true)) { $limite = 15; }
    $contagem = DBRead('animais', 'WHERE entrada = 0', 'COUNT(*) AS total');
    $total = (int)($contagem[0]['total'] ?? 0);
    $paginas = max(1, (int)ceil($total / $limite));
    $pagina = filter_var($entrada['pagina_nascimentos'] ?? 1, FILTER_VALIDATE_INT);
    $pagina = min($paginas, max(1, (int)$pagina));
    $offset = ($pagina - 1) * $limite;
    // Limita os nascimentos antes de buscar os nomes dos pais.
    $fonte = "(SELECT id, nome, sexo, status, data_de_nascimento, pai, mae, terceiro_pai, terceiro_mae
        FROM animais WHERE entrada = 0 ORDER BY id DESC LIMIT $offset,$limite) AS n
        LEFT JOIN animais AS pai ON pai.id = n.pai AND COALESCE(n.terceiro_pai, 0) = 0
        LEFT JOIN terceiros AS pai_externo ON pai_externo.id = n.pai AND n.terceiro_pai = 1
        LEFT JOIN animais AS mae ON mae.id = n.mae AND COALESCE(n.terceiro_mae, 0) = 0
        LEFT JOIN terceiros AS mae_externa ON mae_externa.id = n.mae AND n.terceiro_mae = 1";
    $campos = 'n.*, CASE WHEN n.terceiro_pai = 1 THEN pai_externo.nome ELSE pai.nome END AS nome_pai,
        CASE WHEN n.terceiro_mae = 1 THEN mae_externa.nome ELSE mae.nome END AS nome_mae';
    $animais = $total ? (DBRead($fonte, 'ORDER BY n.id DESC', $campos) ?: array()) : array();
    return compact('limite', 'total', 'paginas', 'pagina', 'offset', 'animais');
}

function renderListaNascimentos(array $dados)
{
    $escapar = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
    $url = 'geral.php?pg=cadastrar_animal&amp;tipo=3&amp;por_pagina_nascimentos=' . $dados['limite'] . '&amp;pagina_nascimentos=';
    ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead><tr><th>Data de nascimento</th><th>Animal</th><th>Sexo</th><th>Pai</th><th>Mãe</th></tr></thead>
        <tbody>
        <?php if (!$dados['animais']): ?>
          <tr><td colspan="5" class="text-center">Nenhum nascimento cadastrado.</td></tr>
        <?php endif; ?>
        <?php foreach ($dados['animais'] as $animal):
            $data = DateTimeImmutable::createFromFormat('!Y-m-d', (string)$animal['data_de_nascimento']);
        ?>
          <tr<?=$animal['status'] > 0 ? ' style="color:red;"' : ''?>>
            <td><?=$data && $data->format('Y-m-d') === $animal['data_de_nascimento'] ? $data->format('d/m/Y') : '--'?></td>
            <td style="cursor:pointer;" onclick="abrir_animal(<?=(int)$animal['id']?>)"><?=$escapar($animal['nome'])?></td>
            <td><?=$escapar($animal['sexo'])?></td>
            <?php foreach (array('pai', 'mae') as $parentesco): ?>
            <td<?php if (!empty($animal['nome_' . $parentesco])): ?> style="cursor:pointer;" onclick="<?=!empty($animal['terceiro_' . $parentesco]) ? 'abrir_terceiro' : 'abrir_animal'?>(<?=(int)$animal[$parentesco]?>)"<?php endif; ?>><?=$escapar($animal['nome_' . $parentesco] ?? '--')?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
      <label for="por-pagina-nascimentos" style="margin:0; font-weight:normal;">Por página</label>
      <select id="por-pagina-nascimentos" class="form-control input-sm" style="width:auto;">
        <?php foreach (array(15, 30, 50, 100) as $limite): ?>
        <option value="<?=$limite?>" <?=$dados['limite'] === $limite ? 'selected' : ''?>><?=$limite?></option>
        <?php endforeach; ?>
      </select>
      <span class="text-muted">Exibindo <?=$dados['total'] ? $dados['offset'] + 1 : 0?> a <?=min($dados['offset'] + $dados['limite'], $dados['total'])?> de <?=$dados['total']?> nascimentos</span>
      <?php if ($dados['paginas'] > 1): ?>
      <nav aria-label="Paginação dos últimos nascimentos"><ul class="pagination pagination-sm no-margin">
        <?php if ($dados['pagina'] > 1): ?>
        <li><a data-pagina-nascimentos="<?=$dados['pagina'] - 1?>" href="<?=$url . ($dados['pagina'] - 1)?>" aria-label="Página anterior">&laquo;</a></li>
        <?php endif; ?>
        <?php
        $numeros = array(1, $dados['paginas']);
        for ($i = max(1, $dados['pagina'] - 1); $i <= min($dados['paginas'], $dados['pagina'] + 1); ++$i) { $numeros[] = $i; }
        $numeros = array_unique($numeros); sort($numeros); $anterior = 0;
        foreach ($numeros as $numero): ?>
          <?php if ($anterior && $numero > $anterior + 1): ?><li class="disabled"><span>&hellip;</span></li><?php endif; ?>
          <?php if ($numero === $dados['pagina']): ?>
          <li class="active"><span aria-current="page"><?=$numero?></span></li>
          <?php else: ?>
          <li><a data-pagina-nascimentos="<?=$numero?>" href="<?=$url . $numero?>"><?=$numero?></a></li>
          <?php endif; $anterior = $numero; ?>
        <?php endforeach; ?>
        <?php if ($dados['pagina'] < $dados['paginas']): ?>
        <li><a data-pagina-nascimentos="<?=$dados['pagina'] + 1?>" href="<?=$url . ($dados['pagina'] + 1)?>" aria-label="Próxima página">&raquo;</a></li>
        <?php endif; ?>
      </ul></nav>
      <?php endif; ?>
    </div>
    <?php
}
