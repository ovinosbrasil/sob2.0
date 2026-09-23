-- Execute no schema da fazenda após 005_create_receptora.sql.
-- NULL representa uma cria sem vínculo com receptora.
SET @crias_add_id_receptora = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'crias' AND COLUMN_NAME = 'id_receptora'),
    'SELECT 1',
    'ALTER TABLE crias ADD COLUMN id_receptora INT NULL DEFAULT NULL, ADD INDEX idx_crias_id_receptora (id_receptora)'
);
PREPARE crias_column FROM @crias_add_id_receptora;
EXECUTE crias_column;
DEALLOCATE PREPARE crias_column;
