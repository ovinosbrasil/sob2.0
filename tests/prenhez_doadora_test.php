<?php
// Executar no contêiner app: utiliza somente tabelas em banco descartável.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db=new mysqli(getenv('DB_HOST'),getenv('DB_USER'),getenv('DB_PASSWORD'));
$banco='teste_doadora_'.bin2hex(random_bytes(6));$db->query("CREATE DATABASE `$banco`");
register_shutdown_function(function()use($db,$banco){$db->query("DROP DATABASE `$banco`");});
$db->select_db($banco);$db->set_charset('utf8mb4');
$db->query('CREATE TABLE animais(id INT PRIMARY KEY,nome VARCHAR(100),sexo VARCHAR(20))');
$db->query('CREATE TABLE terceiros(id INT PRIMARY KEY,nome VARCHAR(100),sexo VARCHAR(20))');
$db->query('CREATE TABLE transplante(id INT PRIMARY KEY,codigo VARCHAR(30),id_mae INT,terceiro_mae INT,id_pai INT,terceiro_pai INT,data DATE)');
$db->query('CREATE TABLE transplante_controle(id INT PRIMARY KEY,id_lote INT,receptora VARCHAR(50),n_embrioes INT,ultrassom INT,status_nascimento INT)');
$db->query("INSERT INTO animais VALUES(1,'Doadora A','Fêmea'),(2,'Doadora B','Fêmea'),(10,'Reprodutor','Macho')");
$db->query("INSERT INTO terceiros VALUES(1,'Doadora externa','Fêmea')");
$data=(new DateTimeImmutable('today',new DateTimeZone('America/Bahia')))->modify('-30 days')->format('Y-m-d');
$db->query("INSERT INTO transplante VALUES(1,'Lote A',1,0,10,0,'$data'),(2,'Lote externo',1,1,10,0,'$data'),(3,'Lote B',2,0,10,0,'$data')");
for($i=1;$i<=17;$i++)$db->query("INSERT INTO transplante_controle VALUES($i,1,'Receptora $i',1,1,0)");
$db->query("INSERT INTO transplante_controle VALUES(18,2,'Receptora externa',1,1,0),(19,3,'Outra receptora',1,1,0),(20,1,'Negativo',1,2,0),(21,1,'Nascido',1,1,1)");
function DBRead($tabela,$params='',$fields='*'){global $db;return $db->query("SELECT $fields FROM $tabela $params")->fetch_all(MYSQLI_ASSOC);}
function exigirDoadora($ok,$mensagem){if(!$ok)throw new RuntimeException($mensagem);}
require __DIR__.'/../reproducao/prenhez/_prenhez_atual.php';
$todos=consultarPrenhezAtual(array('reproducao'=>'te'));exigirDoadora(count($todos)===19,'Listagem sem filtro mudou');
$filtros=array('reproducao'=>'te','doadora'=>'Doadora A','doadora_id'=>'1','doadora_origem'=>'rebanho');
$internos=consultarPrenhezAtual($filtros);exigirDoadora(count($internos)===17 && count(array_unique(array_column($internos,'nome_doadora')))===1,'Filtro da doadora interna');
$externos=consultarPrenhezAtual(array('reproducao'=>'te','doadora_id'=>'1','doadora_origem'=>'terceiros'));
exigirDoadora(count($externos)===1 && $externos[0]['nome_doadora']==='Doadora externa','IDs iguais de origens distintas misturados');
$outros=consultarPrenhezAtual(array('reproducao'=>'te','doadora_id'=>'2','doadora_origem'=>'rebanho'));
exigirDoadora(count($outros)===1 && $outros[0]['nome_doadora']==='Doadora B','Segunda doadora');
$foraPeriodo=consultarPrenhezAtual($filtros+array('data_inicio'=>'01/01/2000','data_fim'=>'02/01/2000'));exigirDoadora(!$foraPeriodo,'Filtro ignorou período');
$limpos=validarFiltrosPrenhezAtual(array('reproducao'=>'monta','doadora_id'=>'1','doadora_origem'=>'terceiros'));exigirDoadora($limpos['doadora_id']===0,'Filtro vazou para monta');
foreach(array(array('doadora'=>'Texto sem seleção'),array('doadora_id'=>'1','doadora_origem'=>'receptora'),array('doadora_id'=>"1 OR 1=1",'doadora_origem'=>'rebanho')) as $invalido){
 try{validarFiltrosPrenhezAtual(array('reproducao'=>'te')+$invalido);throw new RuntimeException('Filtro inválido aceito');}catch(InvalidArgumentException $e){}
}
$_GET=$filtros+array('pag'=>2);ob_start();require __DIR__.'/../reproducao/prenhez/prenhez_atual.php';$html=ob_get_clean();
foreach(array('id="doadora-prenhez"','data-tipo="femeas"','name="doadora_id"','name="doadora_origem"','doadora_id=1','doadora_origem=rebanho','Exibindo 16 a 17 de 17') as $trecho)exigirDoadora(strpos($html,$trecho)!==false,'Campo/paginação incompleto: '.$trecho);
require __DIR__.'/../reproducao/prenhez/_relatorio_prenhez_pdf.php';
$pdf=new RelatorioPrenhezPDF($filtros+array('data_inicio'=>'','data_fim'=>''));$pdf->SetCompression(false);$pdf->gerar($internos);
$texto=$pdf->Output('S');
exigirDoadora(strpos($texto,'%PDF-')===0 && strpos($texto,'Doadora: Doadora A')!==false && strpos($texto,'Receptora 17')!==false && strpos($texto,'Doadora externa')===false,'PDF não preservou o filtro');
echo "Prenhez: doadora interna/terceira, IDs coincidentes, período, validação, paginação e PDF: OK\n";
