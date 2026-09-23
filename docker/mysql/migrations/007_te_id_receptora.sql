-- Execute no schema da fazenda após 005 e 006.
-- NULL preserva registros legados sem receptora identificável.
SET @te_add_id_receptora = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante_controle' AND COLUMN_NAME = 'id_receptora'),
    'SELECT 1',
    'ALTER TABLE transplante_controle ADD COLUMN id_receptora INT NULL DEFAULT NULL, ADD INDEX idx_te_id_receptora (id_receptora)'
);
PREPARE te_column FROM @te_add_id_receptora;
EXECUTE te_column;
DEALLOCATE PREPARE te_column;
