<?
set_time_limit(9999999999999);
$db = mysql_connect("localhost", "siste870_sob","sob123");
$dados = mysql_select_db("siste870_santaangelina",$db);

/*
mysql_query("ALTER TABLE animais ADD status INT NOT NULL");
mysql_query("ALTER TABLE animais DROP foto, DROP youtube, DROP fazenda, DROP pelagem, DROP origem, DROP grau_de_sangue, DROP categoria_de_sangue, DROP regime_alimentar");

//SETAR VALORES DE STATUS
mysql_query("UPDATE animais SET status = '1' WHERE forma_de_saida = 'Perda'");
mysql_query("UPDATE animais SET status = '2' WHERE forma_de_saida = 'Venda'");
mysql_query("UPDATE animais SET status = '3' WHERE forma_de_saida = 'Emprestimo'");
mysql_query("UPDATE animais SET status = '4' WHERE forma_de_saida = 'Doação'");
mysql_query("UPDATE animais SET status = '5' WHERE forma_de_saida = 'Abate'");
mysql_query("ALTER TABLE animais DROP forma_de_saida");
//FIM SETAR VALORES DE STATUS

mysql_query("ALTER TABLE admin ADD cpf VARCHAR(20) NOT NULL, DROP status");

mysql_query("ALTER TABLE animais ADD entrada INT NOT NULL");
mysql_query("UPDATE animais SET entrada = '0' WHERE forma_de_entrada = 'Nascimento'");
mysql_query("UPDATE animais SET entrada = '1' WHERE forma_de_entrada = 'Compra'");
mysql_query("ALTER TABLE animais DROP forma_de_entrada");


mysql_query("ALTER TABLE vendas ADD observacoes VARCHAR(200) NOT NULL");
$animais = mysql_query("SELECT * FROM animais WHERE observacoes_de_saida != ''");
while($animais_ = mysql_fetch_array($animais)){
  $id_animal = $animais_['id'];
  $observacao = $animais_['observacoes_de_saida'];
  mysql_query("UPDATE vendas SET observacoes = '$observacao' WHERE id_animal = '$id_animal'");
}


mysql_query("ALTER TABLE monta_controle CHANGE codigo id_monta INT NOT NULL");
mysql_query("ALTER TABLE monta CHANGE codigo codigo VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
mysql_query("ALTER TABLE monta_controle ADD ultrassom INT NOT NULL AFTER terceiro");
mysql_query("ALTER TABLE inseminacao_controle ADD ultrassom INT NOT NULL AFTER terceiro");
mysql_query("ALTER TABLE transplante_controle ADD ultrassom INT NOT NULL");
mysql_query("CREATE TABLE reprodutor (id INT NOT NULL AUTO_INCREMENT , id_macho INT NOT NULL , qtd_crias INT NOT NULL , qtd_avaliadas INT NOT NULL , tipo2 INT NOT NULL , tipo3 INT NOT NULL , tipo4 INT NOT NULL , tipo5 INT NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB;");
mysql_query("ALTER TABLE reprodutor ADD qtd_vendas INT NOT NULL AFTER tipo5, ADD venda_macho FLOAT NOT NULL AFTER qtd_vendas, ADD venda_femea FLOAT NOT NULL AFTER venda_macho, ADD venda_geral FLOAT NOT NULL AFTER venda_femea, ADD nota FLOAT NOT NULL AFTER venda_geral, ADD gmd FLOAT NOT NULL AFTER nota");
mysql_query("ALTER TABLE reprodutor ADD pesagem FLOAT NOT NULL AFTER gmd, ADD cabeca FLOAT NOT NULL AFTER pesagem, ADD pescoco FLOAT NOT NULL AFTER cabeca, ADD quarto_anterior FLOAT NOT NULL AFTER pescoco, ADD barril FLOAT NOT NULL AFTER quarto_anterior, ADD quarto_posterior FLOAT NOT NULL AFTER barril, ADD comprimento FLOAT NOT NULL AFTER quarto_posterior, ADD orgao FLOAT NOT NULL AFTER comprimento, ADD gordura FLOAT NOT NULL AFTER orgao, ADD cobertura FLOAT NOT NULL AFTER gordura, ADD cor FLOAT NOT NULL AFTER cobertura, ADD conformacao FLOAT NOT NULL AFTER cor");
mysql_query("CREATE TABLE matriz (id INT NOT NULL AUTO_INCREMENT , id_femea INT NOT NULL , qtd_crias INT NOT NULL , qtd_avaliadas INT NOT NULL , tipo2 INT NOT NULL , tipo3 INT NOT NULL , tipo4 INT NOT NULL , tipo5 INT NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB;");
mysql_query("ALTER TABLE matriz ADD qtd_vendas INT NOT NULL AFTER tipo5, ADD venda_macho FLOAT NOT NULL AFTER qtd_vendas, ADD venda_femea FLOAT NOT NULL AFTER venda_macho, ADD venda_geral FLOAT NOT NULL AFTER venda_femea, ADD nota FLOAT NOT NULL AFTER venda_geral, ADD qtd_mortes FLOAT NOT NULL AFTER nota, ADD peso_apartacao FLOAT NOT NULL AFTER qtd_mortes, ADD intervalo FLOAT NOT NULL AFTER peso_apartacao, ADD prolificidade FLOAT NOT NULL AFTER intervalo, ADD qtd_partos FLOAT NOT NULL AFTER prolificidade");
mysql_query("ALTER TABLE matriz ADD pesagem FLOAT NOT NULL AFTER prolificidade, ADD cabeca FLOAT NOT NULL AFTER pesagem, ADD pescoco FLOAT NOT NULL AFTER cabeca, ADD quarto_anterior FLOAT NOT NULL AFTER pescoco, ADD barril FLOAT NOT NULL AFTER quarto_anterior, ADD quarto_posterior FLOAT NOT NULL AFTER barril, ADD comprimento FLOAT NOT NULL AFTER quarto_posterior, ADD orgao FLOAT NOT NULL AFTER comprimento, ADD gordura FLOAT NOT NULL AFTER orgao, ADD cobertura FLOAT NOT NULL AFTER gordura, ADD cor FLOAT NOT NULL AFTER cobertura, ADD conformacao FLOAT NOT NULL AFTER cor");
mysql_query("ALTER TABLE controle_financeiro ADD forma_de_pagamento VARCHAR(20) NOT NULL AFTER status");
mysql_query("ALTER TABLE controle_financeiro ADD tipo INT NOT NULL AFTER forma_de_pagamento");
mysql_query("ALTER TABLE reprodutor ADD qtd_mortes INT NOT NULL AFTER qtd_vendas");
mysql_query("DROP TABLE album");
mysql_query("DROP TABLE fazenda");
mysql_query("DROP TABLE imagem");
mysql_query("DROP TABLE pelagem");
mysql_query("DROP TABLE video");
mysql_query("DROP TABLE qualidade");
mysql_query("DROP TABLE qualidade_macho");
*/
mysql_query("CREATE TABLE lotes_reproducao (id INT NOT NULL AUTO_INCREMENT , id_lote INT NOT NULL , ultrassom FLOAT NOT NULL , femeas INT NOT NULL , crias FLOAT NOT NULL , mortes FLOAT NOT NULL, tipo INT NOT NULL, vivos FLOAT NOT NULL, PRIMARY KEY (id)) ENGINE = InnoDB;");

mysql_query("ALTER TABLE transplante ADD usados INT NOT NULL AFTER id_mae, ADD congelados INT NOT NULL AFTER usados, ADD data_coleta DATE NOT NULL AFTER congelados, ADD n_embrioes VARCHAR(20) NOT NULL AFTER data_coleta, ADD raca VARCHAR(20) NOT NULL AFTER data_coleta, ADD tipo_semen VARCHAR(20) NOT NULL AFTER raca");

mysql_query("ALTER TABLE admin ADD cod_tecnico VARCHAR(20) NOT NULL");

mysql_close($db);
?>
