<?php
// Usa apenas tabelas TEMPORARY da conexão; não altera os registros existentes.
require_once __DIR__ . '/../includes/termos.php';
require_once __DIR__ . '/../exposicao/conjunto_familia.php';
$dbFamiliaTeste = conexaoTermosSob();
function DBRead($tabela, $condicao, $campos) {
    global $dbFamiliaTeste;
    // MySQL não permite abrir a mesma tabela TEMPORARY três vezes na consulta.
    foreach (array('mae', 'avo') as $parentesco) {
        mysqli_query($dbFamiliaTeste, "DROP TEMPORARY TABLE IF EXISTS teste_$parentesco");
        mysqli_query($dbFamiliaTeste, "CREATE TEMPORARY TABLE teste_$parentesco AS SELECT * FROM animais");
        $tabela = str_replace("JOIN animais $parentesco ON", "JOIN teste_$parentesco $parentesco ON", $tabela);
    }
    return mysqli_fetch_all(mysqli_query($dbFamiliaTeste, "SELECT $campos FROM $tabela $condicao"), MYSQLI_ASSOC);
}
try {
    foreach (array(
        'julgamento' => 'id INT, data DATE',
        'julgamento_controle' => 'id_animal INT, id_julgamento INT',
        'animais_evento' => 'id_animal INT, id_julgamento INT',
        'animais' => 'id INT, nome VARCHAR(100), sexo VARCHAR(20), mae VARCHAR(20), terceiro_mae INT, status INT, data_de_nascimento DATE, tipo INT'
    ) as $tabela => $colunas) mysqli_query($dbFamiliaTeste, "CREATE TEMPORARY TABLE $tabela ($colunas) CHARACTER SET utf8mb4");
    mysqli_query($dbFamiliaTeste, "INSERT INTO julgamento VALUES (1,'2026-10-22'), (2,'2026-10-22')");
    mysqli_query($dbFamiliaTeste, "INSERT INTO animais VALUES
        (1,'Filha','Fêmea','2',0,0,'2026-01-01',3),
        (2,'Mãe','Fêmea','3',0,0,'2023-10-23',4),
        (3,'Avó','Fêmea','0',0,0,'2020-01-01',4)");
    mysqli_query($dbFamiliaTeste, 'INSERT INTO julgamento_controle VALUES (1,1),(1,1)');
    mysqli_query($dbFamiliaTeste, 'INSERT INTO animais_evento VALUES (1,1),(1,1)');
    $verificar = function ($quantidade, $cenario) {
        $lista = listarConjuntosFamiliaExposicao(1);
        if (count($lista) !== $quantidade) throw new RuntimeException($cenario);
    };
    $verificar(1, 'Conjunto válido e deduplicação');
    if (listarConjuntosFamiliaExposicao(2)) throw new RuntimeException('Misturou eventos');
    foreach (array(
        "UPDATE animais SET data_de_nascimento='2023-10-22' WHERE id=2",
        "UPDATE animais SET data_de_nascimento='2027-01-01' WHERE id=2"
    ) as $sql) {
        mysqli_query($dbFamiliaTeste, $sql); $verificar(0, 'Idade inválida permitida');
    }
    mysqli_query($dbFamiliaTeste, "UPDATE animais SET data_de_nascimento='2023-10-23' WHERE id=2");
    foreach (array('UPDATE animais SET status=1 WHERE id=2', 'UPDATE animais SET status=1 WHERE id=3',
        'UPDATE animais SET terceiro_mae=1 WHERE id=1', 'UPDATE animais SET terceiro_mae=1 WHERE id=2',
        "UPDATE animais SET sexo='Macho' WHERE id=1", "UPDATE animais SET mae='9999' WHERE id=2") as $sql) {
        mysqli_query($dbFamiliaTeste, $sql); $verificar(0, $sql);
        mysqli_query($dbFamiliaTeste, "UPDATE animais SET status=0, terceiro_mae=0, sexo='Fêmea'");
        mysqli_query($dbFamiliaTeste, "UPDATE animais SET mae='3' WHERE id=2");
    }
    mysqli_query($dbFamiliaTeste, 'DELETE FROM julgamento_controle');
    $verificar(0, 'Fêmea fora da pista permitida');
    echo "OK: conjunto família, limite de 36 meses, parentes vivos, terceiros, evento e duplicatas.\n";
} finally { mysqli_close($dbFamiliaTeste); }
