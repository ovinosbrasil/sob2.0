<?php
function voltarFluxoTe($mensagem)
{
    $id = (int)($_GET['id_lote'] ?? 0);
    $_SESSION['alerta_te'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>$mensagem);
    $_SESSION['campos_te'] = array_intersect_key($_POST, array_flip(array('lote', 'data_inicial', 'macho', 'femea', 'macho_complementar', 'id_pai_2', 'terceiro_pai_2', 'embrioes', 'congelados', 'usados', 'raca', 'tipo_semen', 'data_coleta')));
    header('Location: ../../geral.php?pg=' . ($id > 0 ? 'te&id_lote=' . $id : 'cadastrar_te'));
    exit;
}
function validarCamposLoteTe($alteracao = false)
{
    $campos = array('lote', 'data_inicial', 'macho', 'femea', 'embrioes');
    if ($alteracao) { $campos = array_merge($campos, array('congelados', 'usados', 'raca', 'tipo_semen')); }
    foreach ($campos as $campo) {
        if (!isset($_POST[$campo]) || !is_string($_POST[$campo]) || trim($_POST[$campo]) === '') {
            voltarFluxoTe('Preencha todos os campos obrigatórios.');
        }
    }
    foreach (array('data_inicial', 'data_coleta') as $campo) {
        $valor = $_POST[$campo] ?? '';
        if ($valor === '' && $campo === 'data_coleta') continue;
        $data = is_string($valor) ? DateTimeImmutable::createFromFormat('!d/m/Y', $valor) : false;
        if (!$data || $data->format('d/m/Y') !== $valor || (int)$data->format('Y') < 1000) {
            voltarFluxoTe('Informe uma data válida no formato dd/mm/aaaa.');
        }
    }
}
