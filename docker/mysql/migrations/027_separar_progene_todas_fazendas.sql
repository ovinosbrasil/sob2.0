-- Executa a separação de progênie nas sete fazendas da hospedagem.
-- O banco central siste870_sob NÃO é acessado nem alterado.
-- Os sete bancos precisam existir e conter a tabela julgamento_controle.
-- Faça backup e execute em manutenção. Não utilizar --force.
-- DDL tem commit implícito: uma falha pode deixar bancos anteriores atualizados.
-- Pode reexecutar após corrigir o erro; preserva escolhas independentes existentes.
-- Conteúdo de cada bloco: migração 026_separar_progene_pai_mae.sql.

USE `siste870_buria`;

-- Execute no schema de cada fazenda, em manutenção, antes de publicar o código.
-- Renomeia progene para progene_pai e cria progene_mae.
-- Preserva a seleção antiga nas duas abas. Novas inscrições começam em false.
-- Reexecutável: não sobrescreve escolhas independentes já gravadas.
-- Faça backup. DDL tem commit implícito; interrompa em caso de erro.
SET @sql_progene_pai = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_pai'),
    'SELECT 1',
    IF(
        EXISTS(SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene'),
        'ALTER TABLE julgamento_controle CHANGE COLUMN progene progene_pai BOOLEAN NOT NULL DEFAULT FALSE',
        'ALTER TABLE julgamento_controle ADD COLUMN progene_pai BOOLEAN NOT NULL DEFAULT FALSE'
    )
);
PREPARE separar_progene FROM @sql_progene_pai;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

-- NULL é temporário para permitir retomar uma execução interrompida sem
-- sobrescrever valores 0/1 de uma migração anterior já concluída.
SET @sql_progene_mae = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_mae'),
    'SELECT 1',
    'ALTER TABLE julgamento_controle ADD COLUMN progene_mae BOOLEAN NULL DEFAULT NULL'
);
PREPARE separar_progene FROM @sql_progene_mae;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

UPDATE julgamento_controle SET progene_mae = progene_pai WHERE progene_mae IS NULL;
ALTER TABLE julgamento_controle MODIFY COLUMN progene_mae BOOLEAN NOT NULL DEFAULT FALSE;

SELECT DATABASE() AS banco_atualizado;

USE `siste870_dorperking`;

-- Execute no schema de cada fazenda, em manutenção, antes de publicar o código.
-- Renomeia progene para progene_pai e cria progene_mae.
-- Preserva a seleção antiga nas duas abas. Novas inscrições começam em false.
-- Reexecutável: não sobrescreve escolhas independentes já gravadas.
-- Faça backup. DDL tem commit implícito; interrompa em caso de erro.
SET @sql_progene_pai = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_pai'),
    'SELECT 1',
    IF(
        EXISTS(SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene'),
        'ALTER TABLE julgamento_controle CHANGE COLUMN progene progene_pai BOOLEAN NOT NULL DEFAULT FALSE',
        'ALTER TABLE julgamento_controle ADD COLUMN progene_pai BOOLEAN NOT NULL DEFAULT FALSE'
    )
);
PREPARE separar_progene FROM @sql_progene_pai;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

-- NULL é temporário para permitir retomar uma execução interrompida sem
-- sobrescrever valores 0/1 de uma migração anterior já concluída.
SET @sql_progene_mae = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_mae'),
    'SELECT 1',
    'ALTER TABLE julgamento_controle ADD COLUMN progene_mae BOOLEAN NULL DEFAULT NULL'
);
PREPARE separar_progene FROM @sql_progene_mae;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

UPDATE julgamento_controle SET progene_mae = progene_pai WHERE progene_mae IS NULL;
ALTER TABLE julgamento_controle MODIFY COLUMN progene_mae BOOLEAN NOT NULL DEFAULT FALSE;

SELECT DATABASE() AS banco_atualizado;

USE `siste870_kaka`;

-- Execute no schema de cada fazenda, em manutenção, antes de publicar o código.
-- Renomeia progene para progene_pai e cria progene_mae.
-- Preserva a seleção antiga nas duas abas. Novas inscrições começam em false.
-- Reexecutável: não sobrescreve escolhas independentes já gravadas.
-- Faça backup. DDL tem commit implícito; interrompa em caso de erro.
SET @sql_progene_pai = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_pai'),
    'SELECT 1',
    IF(
        EXISTS(SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene'),
        'ALTER TABLE julgamento_controle CHANGE COLUMN progene progene_pai BOOLEAN NOT NULL DEFAULT FALSE',
        'ALTER TABLE julgamento_controle ADD COLUMN progene_pai BOOLEAN NOT NULL DEFAULT FALSE'
    )
);
PREPARE separar_progene FROM @sql_progene_pai;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

-- NULL é temporário para permitir retomar uma execução interrompida sem
-- sobrescrever valores 0/1 de uma migração anterior já concluída.
SET @sql_progene_mae = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_mae'),
    'SELECT 1',
    'ALTER TABLE julgamento_controle ADD COLUMN progene_mae BOOLEAN NULL DEFAULT NULL'
);
PREPARE separar_progene FROM @sql_progene_mae;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

UPDATE julgamento_controle SET progene_mae = progene_pai WHERE progene_mae IS NULL;
ALTER TABLE julgamento_controle MODIFY COLUMN progene_mae BOOLEAN NOT NULL DEFAULT FALSE;

SELECT DATABASE() AS banco_atualizado;

USE `siste870_novaesperanca`;

-- Execute no schema de cada fazenda, em manutenção, antes de publicar o código.
-- Renomeia progene para progene_pai e cria progene_mae.
-- Preserva a seleção antiga nas duas abas. Novas inscrições começam em false.
-- Reexecutável: não sobrescreve escolhas independentes já gravadas.
-- Faça backup. DDL tem commit implícito; interrompa em caso de erro.
SET @sql_progene_pai = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_pai'),
    'SELECT 1',
    IF(
        EXISTS(SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene'),
        'ALTER TABLE julgamento_controle CHANGE COLUMN progene progene_pai BOOLEAN NOT NULL DEFAULT FALSE',
        'ALTER TABLE julgamento_controle ADD COLUMN progene_pai BOOLEAN NOT NULL DEFAULT FALSE'
    )
);
PREPARE separar_progene FROM @sql_progene_pai;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

-- NULL é temporário para permitir retomar uma execução interrompida sem
-- sobrescrever valores 0/1 de uma migração anterior já concluída.
SET @sql_progene_mae = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_mae'),
    'SELECT 1',
    'ALTER TABLE julgamento_controle ADD COLUMN progene_mae BOOLEAN NULL DEFAULT NULL'
);
PREPARE separar_progene FROM @sql_progene_mae;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

UPDATE julgamento_controle SET progene_mae = progene_pai WHERE progene_mae IS NULL;
ALTER TABLE julgamento_controle MODIFY COLUMN progene_mae BOOLEAN NOT NULL DEFAULT FALSE;

SELECT DATABASE() AS banco_atualizado;

USE `siste870_romana`;

-- Execute no schema de cada fazenda, em manutenção, antes de publicar o código.
-- Renomeia progene para progene_pai e cria progene_mae.
-- Preserva a seleção antiga nas duas abas. Novas inscrições começam em false.
-- Reexecutável: não sobrescreve escolhas independentes já gravadas.
-- Faça backup. DDL tem commit implícito; interrompa em caso de erro.
SET @sql_progene_pai = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_pai'),
    'SELECT 1',
    IF(
        EXISTS(SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene'),
        'ALTER TABLE julgamento_controle CHANGE COLUMN progene progene_pai BOOLEAN NOT NULL DEFAULT FALSE',
        'ALTER TABLE julgamento_controle ADD COLUMN progene_pai BOOLEAN NOT NULL DEFAULT FALSE'
    )
);
PREPARE separar_progene FROM @sql_progene_pai;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

-- NULL é temporário para permitir retomar uma execução interrompida sem
-- sobrescrever valores 0/1 de uma migração anterior já concluída.
SET @sql_progene_mae = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_mae'),
    'SELECT 1',
    'ALTER TABLE julgamento_controle ADD COLUMN progene_mae BOOLEAN NULL DEFAULT NULL'
);
PREPARE separar_progene FROM @sql_progene_mae;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

UPDATE julgamento_controle SET progene_mae = progene_pai WHERE progene_mae IS NULL;
ALTER TABLE julgamento_controle MODIFY COLUMN progene_mae BOOLEAN NOT NULL DEFAULT FALSE;

SELECT DATABASE() AS banco_atualizado;

USE `siste870_santaangelina`;

-- Execute no schema de cada fazenda, em manutenção, antes de publicar o código.
-- Renomeia progene para progene_pai e cria progene_mae.
-- Preserva a seleção antiga nas duas abas. Novas inscrições começam em false.
-- Reexecutável: não sobrescreve escolhas independentes já gravadas.
-- Faça backup. DDL tem commit implícito; interrompa em caso de erro.
SET @sql_progene_pai = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_pai'),
    'SELECT 1',
    IF(
        EXISTS(SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene'),
        'ALTER TABLE julgamento_controle CHANGE COLUMN progene progene_pai BOOLEAN NOT NULL DEFAULT FALSE',
        'ALTER TABLE julgamento_controle ADD COLUMN progene_pai BOOLEAN NOT NULL DEFAULT FALSE'
    )
);
PREPARE separar_progene FROM @sql_progene_pai;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

-- NULL é temporário para permitir retomar uma execução interrompida sem
-- sobrescrever valores 0/1 de uma migração anterior já concluída.
SET @sql_progene_mae = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_mae'),
    'SELECT 1',
    'ALTER TABLE julgamento_controle ADD COLUMN progene_mae BOOLEAN NULL DEFAULT NULL'
);
PREPARE separar_progene FROM @sql_progene_mae;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

UPDATE julgamento_controle SET progene_mae = progene_pai WHERE progene_mae IS NULL;
ALTER TABLE julgamento_controle MODIFY COLUMN progene_mae BOOLEAN NOT NULL DEFAULT FALSE;

SELECT DATABASE() AS banco_atualizado;

USE `siste870_teste`;

-- Execute no schema de cada fazenda, em manutenção, antes de publicar o código.
-- Renomeia progene para progene_pai e cria progene_mae.
-- Preserva a seleção antiga nas duas abas. Novas inscrições começam em false.
-- Reexecutável: não sobrescreve escolhas independentes já gravadas.
-- Faça backup. DDL tem commit implícito; interrompa em caso de erro.
SET @sql_progene_pai = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_pai'),
    'SELECT 1',
    IF(
        EXISTS(SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene'),
        'ALTER TABLE julgamento_controle CHANGE COLUMN progene progene_pai BOOLEAN NOT NULL DEFAULT FALSE',
        'ALTER TABLE julgamento_controle ADD COLUMN progene_pai BOOLEAN NOT NULL DEFAULT FALSE'
    )
);
PREPARE separar_progene FROM @sql_progene_pai;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

-- NULL é temporário para permitir retomar uma execução interrompida sem
-- sobrescrever valores 0/1 de uma migração anterior já concluída.
SET @sql_progene_mae = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene_mae'),
    'SELECT 1',
    'ALTER TABLE julgamento_controle ADD COLUMN progene_mae BOOLEAN NULL DEFAULT NULL'
);
PREPARE separar_progene FROM @sql_progene_mae;
EXECUTE separar_progene;
DEALLOCATE PREPARE separar_progene;

UPDATE julgamento_controle SET progene_mae = progene_pai WHERE progene_mae IS NULL;
ALTER TABLE julgamento_controle MODIFY COLUMN progene_mae BOOLEAN NOT NULL DEFAULT FALSE;

SELECT DATABASE() AS banco_atualizado;

