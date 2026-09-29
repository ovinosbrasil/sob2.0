<?php
require __DIR__ . '/../_config.php';

header('Content-Type: application/json; charset=utf-8');

$tipos = array('machos', 'femeas', 'femeas_receptoras', 'receptoras', 'rebanho', 'todos');
$tipo = $_GET['tipo'] ?? 'todos';
$termo = trim((string)($_GET['q'] ?? ''));
$limiteOrigem = filter_var($_GET['limite_origem'] ?? 0, FILTER_VALIDATE_INT);
$limiteOrigem = $limiteOrigem && $limiteOrigem >= 1 && $limiteOrigem <= 10 ? $limiteOrigem : 0;

if (!in_array($tipo, $tipos, true)) {
    http_response_code(400);
    echo json_encode(array('erro' => 'Tipo de busca inválido.'), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($termo === '') {
    echo json_encode(array('resultados' => array()), JSON_UNESCAPED_UNICODE);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();

function escaparLikeBuscaAnimais($valor)
{
    return str_replace(array('!', '%', '_'), array('!!', '!%', '!_'), $valor);
}

function nascimentoBuscaAnimais($valor)
{
    if (is_string($valor) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valor, $partes)
        && checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
        return $partes[3] . '/' . $partes[2] . '/' . $partes[1];
    }
    return '--';
}

function situacaoBuscaAnimais($status)
{
    $situacoes = array(
        0 => 'Rebanho',
        1 => 'Morto',
        2 => 'Vendido',
        3 => 'Empréstimo',
        4 => 'Doação',
        5 => 'Abate'
    );
    return $situacoes[(int)$status] ?? 'Rebanho';
}

function consultarBuscaAnimais($link, $tabela, $termoLike, $sexo = null, $limite = 15)
{
    $limite = max(1, min(15, (int)$limite));
    $sql = $tabela === 'animais'
        ? 'SELECT id, nome, sexo, data_de_nascimento, status'
        : 'SELECT id, nome, sexo, NULL AS data_de_nascimento';
    $sql .= " FROM $tabela WHERE nome LIKE ? ESCAPE '!'";
    if ($sexo !== null) {
        $sql .= ' AND sexo = ?';
    }
    $sql .= ' ORDER BY nome ASC LIMIT ' . $limite;

    $stmt = mysqli_prepare($link, $sql);
    if (!$stmt) {
        throw new RuntimeException('Não foi possível preparar a busca.');
    }
    if ($sexo === null) {
        mysqli_stmt_bind_param($stmt, 's', $termoLike);
    } else {
        mysqli_stmt_bind_param($stmt, 'ss', $termoLike, $sexo);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $itens = array();
    while ($registro = mysqli_fetch_assoc($resultado)) {
        $itens[] = array(
            'id' => (int)$registro['id'],
            'nome' => $registro['nome'],
            'origem' => $tabela === 'animais' ? 'rebanho' : 'terceiros',
            'sexo' => $registro['sexo'] ?? '',
            'nascimento' => nascimentoBuscaAnimais($registro['data_de_nascimento'] ?? ''),
            'situacao' => $tabela === 'animais' ? situacaoBuscaAnimais($registro['status'] ?? 0) : 'Terceiros'
        );
    }
    mysqli_stmt_close($stmt);
    return $itens;
}

try {
    $termoLike = '%' . escaparLikeBuscaAnimais($termo) . '%';
    $resultados = array();

    if ($tipo === 'receptoras') {
        $stmt = mysqli_prepare($link, "SELECT id, nome FROM receptora WHERE ativo = 1 AND nome LIKE ? ESCAPE '!' ORDER BY nome ASC LIMIT 10");
        if (!$stmt) {
            throw new RuntimeException('Não foi possível preparar a busca.');
        }
        mysqli_stmt_bind_param($stmt, 's', $termoLike);
        mysqli_stmt_execute($stmt);
        $consulta = mysqli_stmt_get_result($stmt);
        while ($registro = mysqli_fetch_assoc($consulta)) {
            $resultados[] = array(
                'id' => (int)$registro['id'],
                'nome' => $registro['nome'],
                'origem' => 'receptora',
                'sexo' => 'Fêmea',
                'nascimento' => '--',
                'situacao' => 'Ativa'
            );
        }
        mysqli_stmt_close($stmt);
    } else {
        $sexo = $tipo === 'machos' ? 'Macho' : (in_array($tipo, array('femeas', 'femeas_receptoras'), true) ? 'Fêmea' : null);
        $resultados = consultarBuscaAnimais($link, 'animais', $termoLike, $sexo, $limiteOrigem ?: 15);
        if ($tipo !== 'rebanho') {
            $resultados = array_merge($resultados, consultarBuscaAnimais($link, 'terceiros', $termoLike, $sexo, $limiteOrigem ?: 15));
        }
        if (!$limiteOrigem) {
            usort($resultados, function ($a, $b) {
                return strcasecmp($a['nome'], $b['nome']);
            });
        }
        $resultados = array_slice($resultados, 0, $limiteOrigem ? $limiteOrigem * 2 : 10);
        if ($tipo === 'femeas_receptoras') {
            $stmt = mysqli_prepare($link, "SELECT id, nome FROM receptora WHERE ativo = 1 AND nome LIKE ? ESCAPE '!' ORDER BY nome ASC LIMIT 10");
            mysqli_stmt_bind_param($stmt, 's', $termoLike);
            mysqli_stmt_execute($stmt);
            $consulta = mysqli_stmt_get_result($stmt);
            while ($registro = mysqli_fetch_assoc($consulta)) {
                $resultados[] = array('id'=>(int)$registro['id'], 'nome'=>$registro['nome'], 'origem'=>'receptora', 'sexo'=>'Fêmea', 'nascimento'=>'--', 'situacao'=>'Ativa');
            }
            mysqli_stmt_close($stmt);
            usort($resultados, function ($a, $b) { return strcasecmp($a['nome'], $b['nome']); });
        }
    }

    echo json_encode(array('resultados' => $resultados), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $erro) {
    http_response_code(500);
    echo json_encode(array('erro' => 'Não foi possível realizar a busca.'), JSON_UNESCAPED_UNICODE);
}
DBClose($link);
