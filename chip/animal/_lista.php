<?php
$nomeChip = trim((string)($_GET['nome'] ?? ''));
$porPaginaChip = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaChip, array(10, 20, 50, 100), true)) $porPaginaChip = 10;
$condicaoChip = "WHERE chip > '0'";
if ($nomeChip !== '') {
    $nomeChipSql = DBEscape($nomeChip);
    $condicaoChip = "WHERE nome LIKE '%$nomeChipSql%'";
}
$contagemChip = DBRead('animais', $condicaoChip, 'COUNT(*) AS total');
$totalChip = (int)($contagemChip[0]['total'] ?? 0);
$paginasChip = max(1, (int)ceil($totalChip / $porPaginaChip));
$paginaChip = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT);
$paginaChip = min(max(1, (int)$paginaChip), $paginasChip);
$inicioChip = ($paginaChip - 1) * $porPaginaChip;
$animaisChip = DBRead('animais', "$condicaoChip ORDER BY nome ASC, id ASC LIMIT $inicioChip, $porPaginaChip") ?: array();
