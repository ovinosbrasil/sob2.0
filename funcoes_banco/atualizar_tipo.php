<?
set_time_limit(9999999999999);
$db = mysql_connect("localhost", "siste870_sob","sob123");
$dados = mysql_select_db("siste870_santaangelina",$db);
$sql = mysql_query("SELECT * FROM avaliacao");

//AVALIACAO PARA SEGUNDA ETAPA
while($avaliar = mysql_fetch_array($sql)){

$soma = $avaliar['cabeca'] + $avaliar['pescoco'] + $avaliar['quarto_anterior'] + $avaliar['quarto_posterior'] + $avaliar['barril'] + $avaliar['comprimento'] + $avaliar['orgao'] + $avaliar['pesagem'];
if($soma == 35){ $conformacao = 5;}
if($soma >= 28  & $soma < 35){ $conformacao = 4;}
if($soma >= 21 & $soma < 28){ $conformacao = 3;}
if($soma < 21){ $conformacao = 2;}

//$conformacao 2
if(($avaliar['cabeca'] == 2) ||($avaliar['pescoco'] == 2) || ($avaliar['quarto_anterior'] == 2) || ($avaliar['quarto_posterior'] == 2) || ($avaliar['barril'] == 2) || ($avaliar['comprimento'] == 2) ||($avaliar['orgao'] == 2)){ $conformacao = 2;}


//conformacao 5
if($conformacao == 5){
	if(($avaliar['tamanho'] >= 4) & ($avaliar['distribuicao'] >= 3) & ($avaliar['cobertura'] >= 3) & ($avaliar['cor'] >= 3)){$tipo = 5;}
	if($avaliar['tamanho'] == 3){ $tipo = 3; }
	if($avaliar['tamanho'] == 2){ $tipo = 2; }
	if($avaliar['distribuicao'] == 2){ $tipo = 2;}
	if($avaliar['cobertura'] == 2){ $tipo = 2;}
	if($avaliar['cor'] == 2){ $tipo = 2;}

}
//conformacao 4
if($conformacao == 4){
	if(($avaliar['tamanho'] >= 4) & ($avaliar['distribuicao'] >= 3) & ($avaliar['cobertura'] >= 3) & ($avaliar['cor'] >= 3)){$tipo = 4;}
	if(($avaliar['tamanho'] <= 3) || ($avaliar['distribuicao'] < 3) || ($avaliar['cobertura'] < 3) ||($avaliar['cor'] < 3)){$tipo = 3;}}
//conformacao 3
if($conformacao == 3){$tipo = 3;}
if(($avaliar['cabeca'] == 3) ||($avaliar['pescoco'] == 3) || ($avaliar['quarto_anterior'] == 3) || ($avaliar['quarto_posterior'] == 3) || ($avaliar['barril'] == 3) || ($avaliar['comprimento'] == 3) ||($avaliar['orgao'] == 3)){ $tipo = 3;}

//conformacao 2
if($conformacao == 2){$tipo = 2;}
if(($avaliar['cabeca'] == 2) || ($avaliar['pescoco'] == 2) || ($avaliar['quarto_anterior'] == 2) || ($avaliar['quarto_posterior'] == 2) || ($avaliar['barril'] == 2) || ($avaliar['comprimento'] == 2) ||($avaliar['orgao'] == 2)){ $tipo = 2;}

//TIPO 2
if(($conformacao == 2) || ($avaliar['tamanho'] == 2) || ($avaliar['distribuicao'] == 2) || ($avaliar['cobertura'] == 2) || ($avaliar['cor'] == 2)){ $tipo = 2;}


$id_linha = $avaliar['id'];
mysql_query("UPDATE avaliacao SET tipo = '$tipo' WHERE id = '$id_linha'");
}
?>
