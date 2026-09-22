<?php
// Grava o animal, seu vínculo em crias e a confirmação do nascimento juntos.
function registrarNascimentoTe(array $dadosAnimal, $idControle)
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    $etapa = 'iniciar transação';
    try {
        mysqli_set_charset($link, 'utf8mb4');
        mysqli_begin_transaction($link);
        $idControle = (int) $idControle;
        $etapa = 'consultar controle e receptora';
        $resultado = mysqli_query($link, "SELECT tc.id_lote, r.id AS id_receptora, r.nome
            FROM transplante_controle tc
            INNER JOIN transplante t ON t.id = tc.id_lote
            INNER JOIN receptora r ON r.id = tc.id_receptora
            WHERE tc.id = $idControle FOR UPDATE");
        $controle = mysqli_fetch_assoc($resultado);
        if (!$controle) {
            throw new DomainException('Controle de TE ou vínculo da receptora não encontrado. Verifique as migrações de receptoras.');
        }
        $dadosAnimal['receptora'] = $controle['nome'];
        $dadosAnimal['id_receptora'] = (int) $controle['id_receptora'];
        $dadosAnimal['tipo_reproducao'] = 'Embrionagem';
        // Os nomes das colunas vêm exclusivamente do cadastro, nunca da requisição.
        $colunas = implode(',', array_map(function ($coluna) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $coluna)) {
                throw new InvalidArgumentException('Coluna inválida.');
            }
            return '`' . $coluna . '`';
        }, array_keys($dadosAnimal)));
        $valores = array_values($dadosAnimal);
        $marcadores = implode(',', array_fill(0, count($valores), '?'));
        $etapa = 'inserir animal';
        $stmt = mysqli_prepare($link, "INSERT INTO animais ($colunas) VALUES ($marcadores)");
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($valores)), ...$valores);
        mysqli_stmt_execute($stmt);
        $idAnimal = mysqli_insert_id($link);
        mysqli_stmt_close($stmt);

        $etapa = 'inserir cria';
        $stmt = mysqli_prepare($link, "INSERT INTO crias (id_animal, data, id_lote, tipo, receptora, id_receptora)
            VALUES (?, ?, ?, 'Embrionagem', ?, ?)");
        mysqli_stmt_bind_param($stmt, 'isisi', $idAnimal, $dadosAnimal['data_de_nascimento'], $controle['id_lote'], $controle['nome'], $controle['id_receptora']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $etapa = 'confirmar nascimento';
        mysqli_query($link, "UPDATE transplante_controle SET status_nascimento = 1, ultrassom = 1 WHERE id = $idControle");
        $etapa = 'confirmar transação';
        mysqli_commit($link);
        return $idAnimal;
    } catch (Exception $e) {
        mysqli_rollback($link);
        if ($e instanceof DomainException) {
            throw $e;
        }
        throw new RuntimeException('Etapa: ' . $etapa . '; código: ' . $e->getCode() . '; ' . $e->getMessage(), (int) $e->getCode(), $e);
    } finally {
        DBClose($link);
    }
}
