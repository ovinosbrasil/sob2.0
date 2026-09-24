-- Iguala siste870_romanda a siste870_buria conforme a comparação de estrutura.
-- Não cria nem altera a tabela avaliacao. Não copia registros entre bancos.
-- Execute este arquivo inteiro na mesma conexão MySQL.

USE `siste870_romanda`;

-- Permite executar novamente sem tentar adicionar uma coluna já existente.
SET @coluna_existe = (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'siste870_romanda'
    AND TABLE_NAME = 'transplante'
    AND COLUMN_NAME = 'macho_complementar'
);
SET @sql_equalizacao = IF(
  @coluna_existe = 0,
  'ALTER TABLE `siste870_romanda`.`transplante` ADD COLUMN `macho_complementar` varchar(255) NOT NULL DEFAULT '''' AFTER `id_pai_2`',
  'SELECT ''Coluna macho_complementar já existe'' AS resultado'
);
PREPARE equalizar_romanda FROM @sql_equalizacao;
EXECUTE equalizar_romanda;
DEALLOCATE PREPARE equalizar_romanda;

-- Mantém os valores e reproduz a ordem e as definições das colunas da Buria.
ALTER TABLE `siste870_romanda`.`transplante`
  MODIFY COLUMN `terceiro_pai_2` int NOT NULL DEFAULT '0' AFTER `terceiro_pai`,
  MODIFY COLUMN `id_pai_2` int NOT NULL AFTER `id_pai`,
  MODIFY COLUMN `macho_complementar` varchar(255) NOT NULL DEFAULT '' AFTER `id_pai_2`;

SHOW CREATE TABLE `siste870_romanda`.`transplante`;
