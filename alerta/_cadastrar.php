<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/cadastro_tarefa.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../geral.php');
    exit;
}
$cadastro = validarCadastroTarefa($_POST);
if (isset($cadastro['dados'])) {
    $link = null;
    $consulta = null;
    try {
        $link = DBConnect();
        $consulta = mysqli_prepare($link, 'INSERT INTO alerta (titulo, data, status) VALUES (?, ?, ?)');
        if (!$consulta) { throw new RuntimeException('Falha ao preparar cadastro.'); }
        mysqli_stmt_bind_param($consulta, 'ssi', $cadastro['dados']['titulo'], $cadastro['dados']['data'], $cadastro['dados']['status']);
        if (!mysqli_stmt_execute($consulta) || mysqli_stmt_affected_rows($consulta) !== 1) {
            throw new RuntimeException('Falha ao cadastrar tarefa.');
        }
        $cadastro = array('tipo' => 'success', 'mensagem' => 'Tarefa cadastrada com sucesso.');
    } catch (Throwable $erro) {
        error_log('Falha ao cadastrar tarefa de manejo: ' . $erro->getMessage());
        $cadastro = array('tipo' => 'danger', 'mensagem' => 'Não foi possível cadastrar a tarefa. Tente novamente.', 'valores' => $cadastro['valores']);
    } finally {
        if ($consulta) { mysqli_stmt_close($consulta); }
        if ($link) { DBClose($link); }
    }
}
unset($cadastro['dados']);
$_SESSION['tarefa_flash'] = $cadastro;
header('Location: ../geral.php', true, 303);
exit;
