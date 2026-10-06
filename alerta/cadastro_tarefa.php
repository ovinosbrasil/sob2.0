<?php
function validarCadastroTarefa(array $entrada)
{
    $titulo = is_string($entrada['titulo'] ?? null) ? trim($entrada['titulo']) : '';
    $data = is_string($entrada['data'] ?? null) ? trim($entrada['data']) : '';
    $prioridade = in_array($entrada['prioridade'] ?? '', array('0', '1'), true) ? $entrada['prioridade'] : '0';
    $valores = compact('titulo', 'data', 'prioridade');
    $faltantes = array();
    if ($titulo === '') { $faltantes[] = 'Título'; }
    if ($data === '') { $faltantes[] = 'Data'; }
    if ($faltantes) {
        return array('tipo' => 'warning', 'mensagem' => 'Preencha os campos obrigatórios: ' . implode(' e ', $faltantes) . '.', 'valores' => $valores);
    }
    $dataValidada = preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/D', $data) ? DateTimeImmutable::createFromFormat('!d/m/Y', $data) : false;
    if (!$dataValidada || $dataValidada->format('d/m/Y') !== $data) {
        return array('tipo' => 'warning', 'mensagem' => 'Informe uma data válida no formato dia/mês/ano.', 'valores' => $valores);
    }
    return array('dados' => array('titulo' => $titulo, 'data' => $dataValidada->format('Y-m-d'), 'status' => (int)$prioridade), 'valores' => $valores);
}
