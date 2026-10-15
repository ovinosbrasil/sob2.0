<?php
require_once __DIR__ . '/../funcoes_data/categorias.php';

// Mesma origem para o contador legado e para a listagem. IDs de terceiros não
// podem coincidir com IDs do rebanho; inscrições duplicadas contam uma vez.
function sqlProgeniesExposicao($idEvento)
{
    $idEvento = (int)$idEvento;
    $partes = array();
    foreach (array('pai' => 'M', 'mae' => 'F') as $campo => $sexo) {
        $partes[] = "SELECT DISTINCT progenitor.id AS id_animal, '$sexo' AS sexo, cria.id AS cria_id
            FROM julgamento_controle jc
            INNER JOIN animais_evento ae ON ae.id_animal = jc.id_animal AND ae.id_julgamento = jc.id_julgamento
            INNER JOIN animais cria ON cria.id = jc.id_animal
            INNER JOIN animais progenitor ON progenitor.id = cria.$campo
            WHERE jc.id_julgamento = $idEvento
              AND COALESCE(cria.terceiro_$campo, 0) = 0
              AND cria.$campo REGEXP '^[0-9]+$' AND progenitor.id > 0";
    }
    return implode(' UNION ALL ', $partes);
}

function sincronizarProgeniesExposicao($link, $idEvento)
{
    $idEvento = (int)$idEvento;
    $origem = sqlProgeniesExposicao($idEvento);
    $resumo = "SELECT id_animal, sexo, COUNT(DISTINCT cria_id) AS qtd FROM ($origem) crias GROUP BY id_animal, sexo";
    // Recalcula os grupos pelo sexo e remove aqueles que ficaram sem crias.
    mysqli_query($link, "UPDATE progene SET qtd = 0 WHERE id_lote = $idEvento");
    mysqli_query($link, "UPDATE progene p INNER JOIN ($resumo) r ON r.id_animal = p.id_animal AND r.sexo = p.sexo
        SET p.qtd = r.qtd WHERE p.id_lote = $idEvento");
    mysqli_query($link, "DELETE FROM progene WHERE id_lote = $idEvento AND qtd <= 0");
    mysqli_query($link, "INSERT INTO progene (id_lote, id_animal, sexo, qtd)
        SELECT $idEvento, r.id_animal, r.sexo, r.qtd FROM ($resumo) r
        WHERE NOT EXISTS (SELECT 1 FROM progene p WHERE p.id_lote = $idEvento AND p.id_animal = r.id_animal AND p.sexo = r.sexo)");
}

function alterarParticipanteExposicao($idEvento, $idAnimal, $acao)
{
    $idEvento = (int)$idEvento; $idAnimal = (int)$idAnimal;
    if ($idEvento < 1 || $idAnimal < 1) throw new DomainException('Selecione uma exposição e um animal válidos.');
    if (!in_array($acao, array('adicionar', 'julgamento', 'adicionar_julgamento', 'remover', 'remover_julgamento'), true)) throw new InvalidArgumentException('Ação de exposição inválida.');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    try {
        mysqli_begin_transaction($link);
        // Todas as inclusões e exclusões do evento usam este mesmo bloqueio.
        $evento = mysqli_fetch_assoc(mysqli_query($link, "SELECT id, data FROM julgamento WHERE id = $idEvento FOR UPDATE"));
        if (!$evento) throw new DomainException('Exposição não encontrada.');
        $animal = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM animais WHERE id = $idAnimal FOR UPDATE"));
        if (!$animal) throw new DomainException('Animal não encontrado.');
        $condicao = "id_julgamento = $idEvento AND id_animal = $idAnimal";
        $participantes = mysqli_query($link, "SELECT id FROM animais_evento WHERE $condicao ORDER BY id FOR UPDATE");
        $controles = mysqli_query($link, "SELECT id FROM julgamento_controle WHERE $condicao ORDER BY id FOR UPDATE");
        $participa = mysqli_num_rows($participantes) > 0;
        $julgado = mysqli_num_rows($controles) > 0;
        if (in_array($acao, array('julgamento', 'remover'), true) && !$participa) throw new DomainException('O animal não está cadastrado nesta exposição.');
        $alterado = false;
        if (in_array($acao, array('adicionar', 'adicionar_julgamento'), true) && !$participa) {
            mysqli_query($link, "INSERT INTO animais_evento (id_animal, id_julgamento, leilao, julgamento) VALUES ($idAnimal, $idEvento, 0, 0)");
            $participa = true; $alterado = true;
        }
        if (in_array($acao, array('julgamento', 'adicionar_julgamento'), true) && !$julgado) {
            $idade = calcularIdadeMesesDias($animal['data_de_nascimento'], $evento['data']);
            $meses = $idade ? (int)$idade['meses'] : 0;
            $categoria = $idade ? (int)determinarCategoriaPorIdade($idade['meses'], $idade['dias']) : 0;
            $pai = ctype_digit((string)$animal['pai']) ? (int)$animal['pai'] : 0;
            $mae = ctype_digit((string)$animal['mae']) ? (int)$animal['mae'] : 0;
            mysqli_query($link, "INSERT INTO julgamento_controle (id_animal, id_julgamento, categoria, meses, pai, mae)
                VALUES ($idAnimal, $idEvento, $categoria, $meses, $pai, $mae)");
            $julgado = true; $alterado = true;
        }
        if (in_array($acao, array('remover', 'remover_julgamento'), true)) {
            mysqli_query($link, "DELETE FROM julgamento_controle WHERE $condicao");
            $alterado = $julgado; $julgado = false;
            if ($acao === 'remover') {
                mysqli_query($link, "DELETE FROM animais_evento WHERE $condicao");
                $alterado = $participa;
            }
        }
        if ($acao !== 'remover') {
            $status = $julgado ? 1 : 0;
            mysqli_query($link, "UPDATE animais_evento SET julgamento = $status WHERE $condicao");
        }
        sincronizarProgeniesExposicao($link, $idEvento);
        mysqli_commit($link);
        return $alterado;
    } catch (Throwable $erro) {
        mysqli_rollback($link);
        throw $erro;
    } finally {
        DBClose($link);
    }
}

function definirProgeneExposicao($idEvento, $idAnimal, $ativo)
{
    $idEvento = (int)$idEvento; $idAnimal = (int)$idAnimal;
    if ($idEvento < 1 || $idAnimal < 1 || !in_array($ativo, array(0, 1), true)) throw new DomainException('Seleção de progênie inválida.');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    try {
        mysqli_begin_transaction($link);
        $evento = mysqli_query($link, "SELECT id FROM julgamento WHERE id = $idEvento FOR UPDATE");
        if (!mysqli_num_rows($evento)) throw new DomainException('Exposição não encontrada.');
        $controle = mysqli_query($link, "SELECT jc.id FROM julgamento_controle jc
            INNER JOIN animais_evento ae ON ae.id_julgamento = jc.id_julgamento AND ae.id_animal = jc.id_animal
            INNER JOIN animais a ON a.id = jc.id_animal
            WHERE jc.id_julgamento = $idEvento AND jc.id_animal = $idAnimal FOR UPDATE");
        if (!mysqli_num_rows($controle)) throw new DomainException('Animal não encontrado no julgamento desta exposição.');
        // Atualiza também eventuais inscrições legadas duplicadas do mesmo animal.
        mysqli_query($link, "UPDATE julgamento_controle SET progene = $ativo WHERE id_julgamento = $idEvento AND id_animal = $idAnimal");
        mysqli_commit($link);
        return $ativo;
    } catch (Throwable $erro) {
        mysqli_rollback($link);
        throw $erro;
    } finally {
        DBClose($link);
    }
}
