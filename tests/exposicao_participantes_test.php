<?php
// Executar no contêiner app. Utiliza apenas um banco descartável próprio.
if (($argv[1] ?? '') === '--concorrente') {
    $bancoConcorrente = $argv[2];
    if (!preg_match('/^teste_exposicao_[a-f0-9]{12}$/', $bancoConcorrente)) exit(2);
    function DBConnect() { global $bancoConcorrente; return new mysqli(getenv('DB_HOST'),getenv('DB_USER'),getenv('DB_PASSWORD'),$bancoConcorrente); }
    function DBClose($db) { $db->close(); }
    require __DIR__.'/../exposicao/participantes.php';
    echo alterarParticipanteExposicao(7,16,'adicionar_julgamento') ? 'criado' : 'existente';
    exit;
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$admin = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASSWORD'));
$bancoTeste = 'teste_exposicao_' . bin2hex(random_bytes(6));
$admin->query("CREATE DATABASE `$bancoTeste`");
register_shutdown_function(function () use ($admin, $bancoTeste) { $admin->query("DROP DATABASE `$bancoTeste`"); });
$admin->select_db($bancoTeste); $admin->set_charset('utf8mb4');
foreach (array('julgamento','animais','animais_evento','julgamento_controle','progene','terceiros') as $tabela) $admin->query("CREATE TABLE `$tabela` LIKE siste870_buria.`$tabela`");
// Valida a migração e sua reexecução sem tocar nos bancos originais.
$sqlMigracao = file_get_contents(__DIR__.'/../docker/mysql/migrations/023_remover_classificacao_progene.sql');
for ($execucao = 0; $execucao < 2; $execucao++) {
    $admin->multi_query($sqlMigracao);
    do { if ($resultado = $admin->store_result()) $resultado->free(); }
    while ($admin->more_results() && $admin->next_result());
}
if ((int)$admin->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='progene' AND COLUMN_NAME IN ('classe','categoria')")->fetch_row()[0] !== 0) throw new RuntimeException('Colunas legadas não removidas.');
$sqlProgene = file_get_contents(__DIR__.'/../docker/mysql/migrations/024_julgamento_controle_progene.sql');
for ($execucao = 0; $execucao < 2; $execucao++) {
    $admin->multi_query($sqlProgene);
    do { if ($resultado = $admin->store_result()) $resultado->free(); }
    while ($admin->more_results() && $admin->next_result());
}
if (!function_exists('DBConnect')) {
function DBConnect() { global $bancoTeste; $db=new mysqli(getenv('DB_HOST'),getenv('DB_USER'),getenv('DB_PASSWORD'),$bancoTeste); $db->set_charset('utf8mb4'); return $db; }
function DBClose($db) { $db->close(); }
}
require __DIR__.'/../exposicao/participantes.php';
function exigirExpo($ok,$mensagem) { if(!$ok) throw new RuntimeException($mensagem); }
function inserirExpo($tabela,$dados) {
 global $admin;
 foreach($admin->query("SHOW COLUMNS FROM `$tabela`")->fetch_all(MYSQLI_ASSOC) as $col) {
  if(array_key_exists($col['Field'],$dados) || $col['Extra']==='auto_increment' || $col['Null']==='YES' || $col['Default']!==null) continue;
  $dados[$col['Field']]=strpos($col['Type'],'date')!==false?'2026-01-01':(preg_match('/int|float|double|decimal/',$col['Type'])?0:'');
 }
 $campos=implode(',',array_map(function($col){return '`'.$col.'`';},array_keys($dados)));
 $valores=implode(',',array_map(function($v)use($admin){return "'".$admin->real_escape_string((string)$v)."'";},$dados));
 $admin->query("INSERT INTO `$tabela` ($campos) VALUES ($valores)");
}
function qtdExpo($id,$sexo,$evento=7) { global $admin; return (int)($admin->query("SELECT qtd FROM progene WHERE id_lote=$evento AND id_animal=$id AND sexo='$sexo' LIMIT 1")->fetch_row()[0]??0); }
function totalExpo($tabela,$id,$evento=7) { global $admin; return (int)$admin->query("SELECT COUNT(*) FROM `$tabela` WHERE id_julgamento=$evento AND id_animal=$id")->fetch_row()[0]; }
function gruposExpo($sexo) { global $admin; $sql=sqlProgeniesExposicao(7); return $admin->query("SELECT id_animal FROM ($sql) r WHERE sexo='$sexo' GROUP BY id_animal HAVING COUNT(DISTINCT cria_id)>=2")->fetch_all(MYSQLI_NUM); }
inserirExpo('julgamento',array('id'=>7,'data'=>'2026-10-15'));
inserirExpo('julgamento',array('id'=>8,'data'=>'2026-10-15'));
foreach(array(1,2,3) as $id) inserirExpo('animais',array('id'=>$id,'nome'=>'Progenitor '.$id));
foreach(array(10,11,12,13,14,15) as $id) inserirExpo('animais',array('id'=>$id,'nome'=>'Cria '.$id,'pai'=>1,'mae'=>2,'terceiro_pai'=>0,'terceiro_mae'=>0,'data_de_nascimento'=>'2026-01-01'));
exigirExpo(alterarParticipanteExposicao(7,10,'adicionar'),'Inclusão na exposição');
exigirExpo(!alterarParticipanteExposicao(7,10,'adicionar') && totalExpo('animais_evento',10)===1,'Cadastro repetido duplicou');
exigirExpo(alterarParticipanteExposicao(7,10,'julgamento') && qtdExpo(1,'M')===1 && qtdExpo(2,'F')===1,'Contadores iniciais');
exigirExpo(!alterarParticipanteExposicao(7,10,'julgamento') && qtdExpo(1,'M')===1,'Julgamento repetido');
alterarParticipanteExposicao(7,11,'adicionar_julgamento');
exigirExpo(qtdExpo(1,'M')===2 && count(gruposExpo('M'))===1,'Duas crias');
// Remoção direta da exposição: corrige o furo original.
alterarParticipanteExposicao(7,10,'remover');
exigirExpo(totalExpo('animais_evento',10)===0 && totalExpo('julgamento_controle',10)===0 && qtdExpo(1,'M')===1 && !gruposExpo('M'),'Remoção direta não sincronizou');
alterarParticipanteExposicao(7,11,'remover_julgamento');
exigirExpo(totalExpo('animais_evento',11)===1 && totalExpo('julgamento_controle',11)===0 && qtdExpo(1,'M')===0,'Remoção de pista');
exigirExpo(!alterarParticipanteExposicao(7,11,'remover_julgamento') && qtdExpo(1,'M')===0,'Exclusão repetida negativou');
exigirExpo((int)$admin->query('SELECT COUNT(*) FROM progene WHERE id_lote=7 AND qtd<=0')->fetch_row()[0]===0,'Grupos sem crias permaneceram no banco');
// Um registro inválido de outra exposição não deve ser removido pelo recálculo.
inserirExpo('progene',array('id_lote'=>999,'id_animal'=>1,'sexo'=>'M','qtd'=>-1));
// Corrige contadores antigos a partir das inscrições reais.
$admin->query('UPDATE progene SET qtd=99 WHERE id_lote=7');
alterarParticipanteExposicao(7,11,'adicionar_julgamento');
exigirExpo(qtdExpo(1,'M')===1,'Contador legado incorreto');
// Filiação atual: corrige a contagem mesmo que o snapshot de julgamento seja antigo.
$admin->query('UPDATE animais SET pai=3 WHERE id=11');
alterarParticipanteExposicao(7,12,'adicionar_julgamento');
exigirExpo(qtdExpo(3,'M')===1 && qtdExpo(1,'M')===1,'Mudança de filiação');
$admin->query('UPDATE animais SET pai=1, terceiro_pai=1, mae=0 WHERE id=13');
alterarParticipanteExposicao(7,13,'adicionar_julgamento');
exigirExpo(qtdExpo(1,'M')===1 && qtdExpo(0,'F')===0,'Terceiro ou pai/mãe zero contaminou grupo');
// Snapshot órfão e flags divergentes são reparados ao adicionar o participante.
inserirExpo('julgamento_controle',array('id_animal'=>14,'id_julgamento'=>7,'pai'=>1,'mae'=>2));
alterarParticipanteExposicao(7,14,'adicionar');
exigirExpo(totalExpo('animais_evento',14)===1 && totalExpo('julgamento_controle',14)===1,'Órfão não reparado');
$admin->query('UPDATE animais_evento SET julgamento=0 WHERE id_animal=14 AND id_julgamento=7');
alterarParticipanteExposicao(7,14,'julgamento');
exigirExpo((int)$admin->query('SELECT julgamento FROM animais_evento WHERE id_animal=14 AND id_julgamento=7')->fetch_row()[0]===1,'Flag de julgamento incorreta');
alterarParticipanteExposicao(8,15,'adicionar_julgamento');
alterarParticipanteExposicao(7,14,'remover');
exigirExpo(qtdExpo(1,'M',8)===1,'Afetou outra exposição');
// Falha na sincronização deve reverter inscrição, julgamento e flags juntos.
$admin->query("CREATE TRIGGER teste_falha_progene BEFORE UPDATE ON progene FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='falha simulada'");
try { alterarParticipanteExposicao(7,15,'adicionar_julgamento'); throw new RuntimeException('Falha não propagada'); }
catch(mysqli_sql_exception $e) { exigirExpo($e->getCode()===1644,'Falha inesperada'); }
exigirExpo(totalExpo('animais_evento',15)===0 && totalExpo('julgamento_controle',15)===0,'Rollback incompleto');
$admin->query('DROP TRIGGER teste_falha_progene');
try { alterarParticipanteExposicao(999,15,'adicionar'); throw new RuntimeException('Evento inexistente aceito'); } catch(DomainException $e) {}
// Duas requisições simultâneas para o mesmo animal não duplicam inscrições.
inserirExpo('animais',array('id'=>16,'nome'=>'Concorrente','pai'=>1,'mae'=>2,'terceiro_pai'=>0,'terceiro_mae'=>0,'data_de_nascimento'=>'2026-01-01'));
$processos=array();
for($i=0;$i<2;$i++) {
 $pipes=array();
 $proc=proc_open(array(PHP_BINARY,__FILE__,'--concorrente',$bancoTeste),array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w')),$pipes);
 fclose($pipes[0]); $processos[]=array($proc,$pipes);
}
$respostas=array();
foreach($processos as $processo) {
 $respostas[]=stream_get_contents($processo[1][1]); $erros=stream_get_contents($processo[1][2]);
 fclose($processo[1][1]);fclose($processo[1][2]);
 exigirExpo(proc_close($processo[0])===0,'Falha no teste concorrente: '.$erros);
}
sort($respostas);
exigirExpo($respostas===array('criado','existente') && totalExpo('animais_evento',16)===1 && totalExpo('julgamento_controle',16)===1,'Requisições simultâneas duplicaram');
// Legado duplicado não duplica a progênie; a exclusão remove todos os vínculos.
inserirExpo('julgamento_controle',array('id_animal'=>16,'id_julgamento'=>7,'pai'=>1,'mae'=>2));
alterarParticipanteExposicao(7,16,'julgamento');
exigirExpo(qtdExpo(1,'M')===2,'Inscrição legada duplicada inflou a contagem');
alterarParticipanteExposicao(7,16,'remover');
exigirExpo(totalExpo('julgamento_controle',16)===0 && qtdExpo(1,'M')===1,'Exclusão de vínculos legados duplicados');
// A seleção é independente do grupo de pai/mãe, mas exclusiva da exposição.
exigirExpo((int)$admin->query('SELECT progene FROM julgamento_controle WHERE id_julgamento=7 AND id_animal=11')->fetch_row()[0]===0,'Default de progene não é false');
exigirExpo(definirProgeneExposicao(7,11,1)===1,'Não ativou');
alterarParticipanteExposicao(7,11,'adicionar_julgamento');
exigirExpo((int)$admin->query('SELECT progene FROM julgamento_controle WHERE id_julgamento=7 AND id_animal=11')->fetch_row()[0]===1,'Repetição apagou seleção');
alterarParticipanteExposicao(8,11,'adicionar_julgamento');
exigirExpo((int)$admin->query('SELECT progene FROM julgamento_controle WHERE id_julgamento=8 AND id_animal=11')->fetch_row()[0]===0,'Seleção vazou para outro evento');
exigirExpo(definirProgeneExposicao(7,11,0)===0,'Não desativou');
try { definirProgeneExposicao(7,999,1); throw new RuntimeException('Animal inexistente aceito'); } catch(DomainException $e) {}
try { definirProgeneExposicao(7,11,2); throw new RuntimeException('Estado inválido aceito'); } catch(DomainException $e) {}
definirProgeneExposicao(7,11,1);
// Mesmo com contadores legados errados, a tela considera as crias reais.
$admin->query('UPDATE progene SET qtd=99 WHERE id_lote=7');
// Exercita a consulta e o HTML das duas abas, incluindo pais com origem externa.
function DBRead($tabela,$params='',$fields='*') { global $admin; return $admin->query("SELECT $fields FROM $tabela $params")->fetch_all(MYSQLI_ASSOC); }
foreach(array('progenie_pai','progenie_mae') as $abaTeste) {
 $_GET=array('id_exposicao'=>7,'aba'=>$abaTeste); ob_start();
 $codigo=file_get_contents(__DIR__.'/../exposicao/aba_exposicao.php');
 $codigo=str_replace("require __DIR__ . '/../_config.php';",'',$codigo);
 $codigo=str_replace('__DIR__',var_export(realpath(__DIR__.'/../exposicao'),true),$codigo);
 eval('?>'.$codigo); $html=ob_get_clean();
 exigirExpo(strpos($html,'Progênie de')!==false,'Aba não renderizada');
 if($abaTeste==='progenie_pai') exigirExpo(strpos($html,'Nenhuma progênie com duas ou mais crias')!==false,'Contador antigo exibiu grupo de uma cria');
 else exigirExpo(strpos($html, 'data-progene-linha="11" class="is-progene"')!==false && strpos($html, 'data-progene-linha="12" class=""')!==false && strpos($html,'Cria 11')!==false && strpos($html,'Cria 12')!==false && strpos($html,'Cria 13')===false,'Grupo materno ou origem incorretos');
}
// O mesmo indicador deve aparecer na progênie paterna e materna.
$admin->query('UPDATE animais SET pai=3 WHERE id=12');
$_GET=array('id_exposicao'=>7,'aba'=>'progenie_pai'); ob_start(); eval('?>'.$codigo); $htmlPai=ob_get_clean();
exigirExpo(strpos($htmlPai,'data-progene-linha="11" class="is-progene"')!==false,'Seleção não apareceu na aba paterna');
$admin->query("CREATE TRIGGER teste_falha_selecao BEFORE UPDATE ON julgamento_controle FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='falha simulada'");
try { definirProgeneExposicao(7,11,0); throw new RuntimeException('Falha não propagada'); } catch(mysqli_sql_exception $e) { exigirExpo($e->getCode()===1644,'Falha inesperada'); }
exigirExpo((int)$admin->query('SELECT progene FROM julgamento_controle WHERE id_animal=11 AND id_julgamento=7')->fetch_row()[0]===1,'Erro alterou a seleção');
$admin->query('DROP TRIGGER teste_falha_selecao');
alterarParticipanteExposicao(7,11,'remover_julgamento');
alterarParticipanteExposicao(7,11,'adicionar_julgamento');
exigirExpo((int)$admin->query('SELECT progene FROM julgamento_controle WHERE id_animal=11 AND id_julgamento=7')->fetch_row()[0]===0,'Reinscrição não iniciou desativada');
// A ordem persistida também prioriza selecionados, antes da ordem alfabética.
definirProgeneExposicao(7,11,1);
$admin->query("UPDATE animais SET nome='Zulu selecionado' WHERE id=11");
$_GET=array('id_exposicao'=>7,'aba'=>'progenie_mae'); ob_start(); eval('?>'.$codigo); $htmlOrdenado=ob_get_clean();
exigirExpo(strpos($htmlOrdenado,'Zulu selecionado') < strpos($htmlOrdenado,'Cria 12'),'Selecionado não ficou no topo ao carregar a lista');
// Limpeza dos bancos: exercita o arquivo completo em cinco schemas descartáveis.
$bancosLimpeza=array();
foreach(array('buria','novaesperanca','romana','santaangelina','teste') as $fazenda) {
 $schemaLimpeza=$bancoTeste.'_'.$fazenda;
 $admin->query("CREATE DATABASE `$schemaLimpeza`");
 $bancosLimpeza['siste870_'.$fazenda]=$schemaLimpeza;
 register_shutdown_function(function()use($admin,$schemaLimpeza){$admin->query("DROP DATABASE `$schemaLimpeza`");});
 $admin->query("CREATE TABLE `$schemaLimpeza`.progene LIKE progene");
 $admin->query("INSERT INTO `$schemaLimpeza`.progene (id_lote,id_animal,sexo,qtd) VALUES (7,1,'M',0),(7,2,'F',-2),(8,3,'M',1),(9,4,'F',3)");
}
$sqlLimpeza=strtr(file_get_contents(__DIR__.'/../docker/mysql/migrations/025_limpar_progene_sem_crias.sql'),$bancosLimpeza);
for($execucao=0;$execucao<2;$execucao++) {
 $admin->multi_query($sqlLimpeza);
 do {if($resultado=$admin->store_result())$resultado->free();} while($admin->more_results() && $admin->next_result());
}
foreach($bancosLimpeza as $schemaLimpeza) {
 $restantes=$admin->query("SELECT id_lote,id_animal,sexo,qtd FROM `$schemaLimpeza`.progene ORDER BY id_animal")->fetch_all(MYSQLI_ASSOC);
 exigirExpo(count($restantes)===2 && (float)$restantes[0]['qtd']===1.0 && (float)$restantes[1]['qtd']===3.0,'Limpeza não preservou grupos positivos');
}
exigirExpo((int)$admin->query('SELECT COUNT(*) FROM progene WHERE id_lote=999 AND qtd=-1')->fetch_row()[0]===1,'Recálculo afetou outra exposição');
echo "Exposição: cadastro/exclusão, repetição, contadores reais, origem, filiação, flags, isolamento, rollback e abas: OK\n";
