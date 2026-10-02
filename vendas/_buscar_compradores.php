<?php
require_once __DIR__ . '/../_config.php';
header('Content-Type: application/json; charset=UTF-8');
$termo = trim((string)($_GET['q'] ?? ''));
if ($termo === '') { echo json_encode(array('resultados'=>array())); exit; }
$link = DBConnect();
$termo = '%' . str_replace(array('!', '%', '_'), array('!!', '!%', '!_'), $termo) . '%';
$stmt = mysqli_prepare($link, "SELECT id, nome, cidade, estado, email FROM mercado WHERE nome LIKE ? ESCAPE '!' ORDER BY nome ASC LIMIT 6");
mysqli_stmt_bind_param($stmt, 's', $termo);
mysqli_stmt_execute($stmt);
$consulta = mysqli_stmt_get_result($stmt);
$resultados = array();
while ($registro = mysqli_fetch_assoc($consulta)) $resultados[] = array('id'=>(int)$registro['id'],'nome'=>$registro['nome'],'cidade'=>$registro['cidade'],'estado'=>$registro['estado'],'email'=>$registro['email']);
mysqli_stmt_close($stmt);
DBClose($link);
echo json_encode(array('resultados'=>$resultados), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
