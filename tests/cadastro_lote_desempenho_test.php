<?php
require_once __DIR__ . '/../animal/importacao/cadastrar_animal.php';
$registros = $animais = array();
for ($i = 1; $i <= 8000; $i++) {
    $animais[] = array('id' => $i, 'nome' => 'EXISTENTE E' . $i, 'fbb' => 'E' . $i, 'tatuagem' => 'E' . $i, 'sexo' => 'Macho');
    $registros[] = array('linha' => $i, 'problemas' => array(), 'dados' => array('Nome' => 'NOVO N' . $i, 'FBB/FBE' => 'N' . $i, 'Tat.' => 'N' . $i, 'Nasc.' => '01/01/2020', 'Sexo' => 'Macho'));
}
$inicio = microtime(true);
$plano = planejarCadastroPrevia($registros, 'todos', $animais, array(), 'Dorper', $ignorados);
$tempo = microtime(true) - $inicio;
if (count($plano) !== 8000 || $ignorados) throw new RuntimeException('Lote incompleto.');
if ($tempo >= 10) throw new RuntimeException('Planejamento excedeu 10 segundos.');
printf("8.000 novos animais e 8.000 existentes: %.2f segundos, lote completo.\n", $tempo);
