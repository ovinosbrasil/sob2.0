<?php
require_once __DIR__ . '/../animal/importacao/leitor_consulta_rebanho.php';
require_once __DIR__ . '/../animal/importacao/comparar_rebanho.php';
require_once __DIR__ . '/../animal/importacao/atualizar_animal.php';
function verificarComparacao($ok, $mensagem) { if (!$ok) throw new RuntimeException($mensagem); }
function hPreviaRebanho($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$registro = array('linha'=>2, 'dados'=>array('Nome'=>'BURIA 2503', 'FBB/FBE'=>'O000123', 'Tat.'=>'0007', 'Nasc.'=>'30/01/2011'), 'problemas'=>array());
$animal = array('id'=>1, 'nome'=>'BURIA 2503', 'fbb'=>'O000123', 'tatuagem'=>'0007', 'data_de_nascimento'=>'2011-01-30');
$iguais = compararAnimaisPrevia(array($registro), array($animal));
verificarComparacao(count($iguais['cadastrados'])===1 && !$iguais['possiveis_atualizacoes'], 'Quatro campos iguais devem classificar como cadastrado.');
foreach (array('nome'=>'OUTRO NOME', 'fbb'=>'O999', 'tatuagem'=>'999', 'data_de_nascimento'=>'2012-01-01') as $campo=>$valor) {
    $diferente=$animal; $diferente[$campo]=$valor;
    $resultado=compararAnimaisPrevia(array($registro), array($diferente));
    verificarComparacao(count($resultado['possiveis_atualizacoes']) === 1, 'Coincidência deve sugerir atualização: ' . $campo);
    $divergencias=divergenciasAnimalPrevia($registro['dados'], $diferente);
    verificarComparacao(array_keys(array_filter($divergencias))===array($campo), 'Destacar somente o campo divergente: '.$campo);
}
$normalizado=$animal; $normalizado['nome']='  buria   2503 '; $normalizado['fbb']='o000123';
verificarComparacao(count(compararAnimaisPrevia(array($registro), array($normalizado))['cadastrados'])===1, 'Preservar normalização existente.');
$semZeros=$animal; $semZeros['tatuagem']='7';
verificarComparacao(divergenciasAnimalPrevia($registro['dados'],$semZeros)['tatuagem'], 'Zeros à esquerda são significativos.');
$invalido=$registro; $invalido['dados']['Nasc.']='31/02/2011';
verificarComparacao(count(compararAnimaisPrevia(array($invalido),array($animal))['possiveis_atualizacoes'])===1, 'Data inválida não confirma cadastro.');
$vazio=$registro; $vazio['dados']['FBB/FBE']=''; $bancoVazio=$animal; $bancoVazio['fbb']='';
verificarComparacao(count(compararAnimaisPrevia(array($vazio),array($bancoVazio))['possiveis_atualizacoes'])===1, 'Campos obrigatórios vazios não confirmam cadastro.');
verificarComparacao(count(compararAnimaisPrevia(array($registro),array())['nao_cadastrados'])===1, 'Banco vazio.');
$semRelacao=array('id'=>3,'nome'=>'SEM RELACAO','fbb'=>'X','tatuagem'=>'Y','data_de_nascimento'=>'2011-01-30');
verificarComparacao(count(compararAnimaisPrevia(array($registro),array($semRelacao))['nao_cadastrados'])===1, 'Apenas data igual não identifica animal.');
$outro=$animal; $outro['id']=2; $outro['nome']='OUTRO'; $outro['fbb']='O000123'; $outro['tatuagem']='999';
$divergente=$animal; $divergente['data_de_nascimento']='2012-01-01';
$gruposPrevia=compararAnimaisPrevia(array($registro),array($divergente,$outro,$semRelacao));
verificarComparacao(array_column($gruposPrevia['possiveis_atualizacoes'][0]['animais_banco'],'id')===array(1,2), 'Não combinar campos de animais distintos nem duplicar candidatos.');
verificarComparacao(count(compararAnimaisPrevia(array($registro),array($outro,$animal))['cadastrados'])===1, 'Correspondência completa tem prioridade.');
$gruposPrevia['possiveis_atualizacoes'][0]['animais_banco'][1]['nome']='<script>alert(1)</script>';
ob_start(); require __DIR__.'/../animal/importacao/tabela_possiveis_atualizacoes.php'; $html=ob_get_clean();
verificarComparacao(substr_count($html,'data-registro-previa data-pesquisa=')===1 && strpos($html,'rowspan="2"')!==false, 'Agrupamento dos candidatos.');
verificarComparacao(substr_count($html,'class="dado-divergente"')===3, 'Vermelho deve refletir diferenças de cada candidato separadamente.');
verificarComparacao(strpos($html,'&lt;script&gt;')!==false && strpos($html,'<script>alert(1)</script>')===false, 'Valores do banco devem ser escapados.');
verificarComparacao(strpos($html,'01/01/2012')!==false && strpos($html,'Atualizar')!==false, 'Preservar datas e ação.');
verificarComparacao(strpos($html, 'Situação') === false, 'Situação não deve aparecer na tabela ou no modal de atualização.');
echo "Quatro campos, divergências individuais, candidatos, prioridade, normalização e destaque seguro: OK\n";

$tatRepetida = $animal; $tatRepetida['nome'] = 'OUTRO ANIMAL'; $tatRepetida['fbb'] = 'FBB-NOVO';
$comparacaoTat = compararAnimaisPrevia(array($registro), array($tatRepetida));
verificarComparacao(count($comparacaoTat['possiveis_atualizacoes']) === 1, 'Tatuagem igual deve sugerir candidato para revisão.');
echo "Tatuagem igual: candidato sugerido para correção do FBB: OK\n";

$porNome = $animal; $porNome['id'] = 30; $porNome['fbb'] = ''; $porNome['tatuagem'] = 'OUTRA';
$porTat = $animal; $porTat['id'] = 31; $porTat['fbb'] = 'ANTIGO'; $porTat['nome'] = 'OUTRO';
$sugestoes = compararAnimaisPrevia(array($registro), array($porNome, $porTat));
verificarComparacao(array_column($sugestoes['possiveis_atualizacoes'][0]['animais_banco'], 'id') === array(30, 31), 'Nome e tatuagem devem sugerir todos os candidatos distintos.');
$prioridade = compararAnimaisPrevia(array($registro), array($porNome, $porTat, $divergente));
verificarComparacao(array_column($prioridade['possiveis_atualizacoes'][0]['animais_banco'], 'id') === array(1), 'FBB existente deve ter prioridade.');
verificarComparacao(count(compararAnimaisPrevia(array($registro), array($porNome), false)['nao_cadastrados']) === 1, 'Sugestão por nome não é uma restrição de unicidade.');
$deduplicado = $animal; $deduplicado['fbb'] = '';
verificarComparacao(count(compararAnimaisPrevia(array($registro), array($deduplicado))['possiveis_atualizacoes'][0]['animais_banco']) === 1, 'Mesmo candidato por nome e tatuagem não pode aparecer duplicado.');
echo "Nome e tatuagem: candidatos, deduplicação e prioridade do FBB: OK\n";
