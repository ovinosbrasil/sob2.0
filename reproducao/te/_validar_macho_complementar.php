<?php
$id_pai_2 = 0;
$terceiro_pai_2 = 0;
$nome_macho_complementar = trim($_POST['macho_complementar'] ?? '');

if ($nome_macho_complementar !== '') {
    $id_pai_2 = (int)($_POST['id_pai_2'] ?? 0);
    $terceiro_pai_2 = (int)($_POST['terceiro_pai_2'] ?? 0);
    $macho_complementar = false;
    if ($id_pai_2 > 0 && in_array($terceiro_pai_2, [0, 1], true)) {
        $tabela_macho_complementar = $terceiro_pai_2 ? 'terceiros' : 'animais';
        $macho_complementar = DBRead($tabela_macho_complementar, "WHERE id = '$id_pai_2' AND sexo = 'Macho'");
    }
    if (!$macho_complementar || trim($macho_complementar[0]['nome']) !== $nome_macho_complementar) {
        echo '<script>alert("Selecione o macho complementar na pesquisa ou deixe o campo vazio."); history.back();</script>';
        exit;
    }
}
