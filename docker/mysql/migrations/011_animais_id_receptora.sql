-- Execute no schema da fazenda após 005_create_receptora.sql.
-- NULL representa um animal sem vínculo com receptora.
SET @animais_add_id_receptora = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'animais' AND COLUMN_NAME = 'id_receptora'),
    'SELECT 1',
    'ALTER TABLE animais ADD COLUMN id_receptora INT NULL DEFAULT NULL, ADD INDEX idx_animais_id_receptora (id_receptora)'
);
PREPARE animais_column FROM @animais_add_id_receptora;
EXECUTE animais_column;
DEALLOCATE PREPARE animais_column;
