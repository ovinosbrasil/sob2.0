-- Execute no schema de cada fazenda antes de publicar a seleção de progênie.
-- Cria progene com false/0 como padrão. Pode ser reexecutado.
SET @sql_progene = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'julgamento_controle' AND COLUMN_NAME = 'progene'),
    'SELECT 1',
    'ALTER TABLE julgamento_controle ADD COLUMN progene BOOLEAN NOT NULL DEFAULT FALSE'
);
PREPARE coluna_progene FROM @sql_progene;
EXECUTE coluna_progene;
DEALLOCATE PREPARE coluna_progene;
