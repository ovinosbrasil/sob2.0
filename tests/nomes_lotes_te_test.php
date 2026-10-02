<?php
require __DIR__.'/../reproducao/te/nomes_lotes.php';
$consultas = 0;
function DBRead($tabela,$params,$fields) { global $consultas; $consultas++; return array(array('id'=>1,'nome'=>$tabela === 'animais' ? 'MACHO ATUAL' : 'FÊMEA TERCEIRA')); }
$lotes = nomesLotesTransplante(array(
 array('id'=>1,'id_pai'=>1,'terceiro_pai'=>0,'pai'=>'1','id_mae'=>1,'terceiro_mae'=>1,'mae'=>'---'),
 array('id'=>2,'id_pai'=>99,'pai'=>'NOME ANTIGO','mae'=>'---','macho_complementar'=>'COMPLEMENTAR'),
 array('id'=>3,'pai'=>'1','mae'=>'')
));
if ($lotes[0]['nome_pai'] !== 'MACHO ATUAL' || $lotes[0]['nome_mae'] !== 'FÊMEA TERCEIRA' || $lotes[1]['nome_pai'] !== 'NOME ANTIGO' || $lotes[1]['nome_pai_complementar'] !== 'COMPLEMENTAR' || $lotes[2]['nome_pai'] !== 'Não informado' || $consultas !== 2) throw new RuntimeException('Resolução incorreta.');
echo "Nomes TE: IDs, origens, legado, ausentes e consultas em lote: OK\n";
