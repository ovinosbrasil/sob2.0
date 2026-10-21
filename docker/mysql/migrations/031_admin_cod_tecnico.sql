-- Executar no banco da FAZENDA que não possui admin.cod_tecnico.
-- No ambiente local, a ausência foi identificada em siste870_teste.
-- Selecione o banco correto antes de executar; não usar o banco central de login.
-- Migração aditiva e repetível. Registros existentes recebem string vazia.
SET @sql_cod_tecnico = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin'
             AND COLUMN_NAME = 'cod_tecnico'),
    'SELECT 1',
    'ALTER TABLE admin ADD COLUMN cod_tecnico VARCHAR(20) NOT NULL DEFAULT '''''
);
PREPARE adicionar_cod_tecnico FROM @sql_cod_tecnico;
EXECUTE adicionar_cod_tecnico;
DEALLOCATE PREPARE adicionar_cod_tecnico;
