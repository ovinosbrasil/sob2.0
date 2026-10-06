-- Executar no schema de cada fazenda antes de publicar os novos fluxos.
SET @terceiros_ativo_sql = IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'terceiros' AND COLUMN_NAME = 'ativo'),
  'SELECT 1',
  'ALTER TABLE terceiros ADD COLUMN ativo TINYINT(1) NOT NULL DEFAULT 1, ADD INDEX idx_terceiros_ativo (ativo)'
);
PREPARE terceiros_ativo_stmt FROM @terceiros_ativo_sql;
EXECUTE terceiros_ativo_stmt;
DEALLOCATE PREPARE terceiros_ativo_stmt;
