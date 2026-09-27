<?php
require __DIR__ . '/../../_config.php';

$nome = DBEscape(trim($_GET['nome'] ?? ''));

function resultadoPaiMonta($animal, $terceiro = false)
{
    $nascimento = $animal['data_de_nascimento'] ?? '';
    $data = '--';
    if (is_string($nascimento) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $nascimento, $partes)
        && checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
        $data = $partes[3] . '/' . $partes[2] . '/' . $partes[1];
    }

    $situacoes = array(
        0 => array('Rebanho', '#00a65a'),
        1 => array('Morto', '#dd4b39'),
        2 => array('Vendido', '#008d4c'),
        3 => array('Empréstimo', '#dd4b39'),
        4 => array('Doação', '#dd4b39'),
        5 => array('Abate', '#dd4b39')
    );
    $situacao = $terceiro ? array('Terceiros', '#777') : ($situacoes[(int)($animal['status'] ?? 0)] ?? array('Rebanho', '#00a65a'));
    ?>
    <button type="button" class="animal-search-result" onclick="linkar_pai_monta(this.getAttribute('data-nome'))" data-nome="<?=htmlspecialchars($animal['nome'], ENT_QUOTES, 'UTF-8')?>">
      <strong><?=htmlspecialchars($animal['nome'], ENT_QUOTES, 'UTF-8')?></strong>
      <span class="animal-search-result-meta">Nascimento: <?=$data?> <span style="color:<?=$situacao[1]?>;">(<?=$situacao[0]?>)</span></span>
    </button>
    <?php
}

$rebanho = DBRead('animais', "WHERE nome LIKE '%$nome%' AND sexo = 'Macho' ORDER BY nome ASC LIMIT 7") ?: array();
$terceiros = DBRead('terceiros', "WHERE nome LIKE '%$nome%' AND sexo = 'Macho' ORDER BY nome ASC LIMIT 3") ?: array();

if (!$rebanho && !$terceiros) {
    echo '<div class="animal-search-empty">Nenhum reprodutor encontrado.</div>';
}
foreach ($rebanho as $animal) { resultadoPaiMonta($animal); }
foreach ($terceiros as $animal) { resultadoPaiMonta($animal, true); }
?>
<button type="button" onclick="fechar_lista_pai()" class="btn btn-link btn-sm animal-search-close">Fechar Pesquisa</button>
