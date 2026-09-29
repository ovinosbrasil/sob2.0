-- Execute no schema de cada fazenda.
-- NULL representa um ultrassom ainda não datado.
SET @monta_data_ultrassom = IF(
  NOT EXISTS(SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'monta_controle')
  OR EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'monta_controle' AND COLUMN_NAME = 'data_ultrassom'),
  'SELECT 1',
  'ALTER TABLE monta_controle ADD COLUMN data_ultrassom DATE NULL DEFAULT NULL AFTER ultrassom'
);
PREPARE stmt FROM @monta_data_ultrassom; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @inseminacao_data_ultrassom = IF(
  NOT EXISTS(SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inseminacao_controle')
  OR EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inseminacao_controle' AND COLUMN_NAME = 'data_ultrassom'),
  'SELECT 1',
  'ALTER TABLE inseminacao_controle ADD COLUMN data_ultrassom DATE NULL DEFAULT NULL AFTER ultrassom'
);
PREPARE stmt FROM @inseminacao_data_ultrassom; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @transplante_data_ultrassom = IF(
  NOT EXISTS(SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante_controle')
  OR EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante_controle' AND COLUMN_NAME = 'data_ultrassom'),
  'SELECT 1',
  'ALTER TABLE transplante_controle ADD COLUMN data_ultrassom DATE NULL DEFAULT NULL AFTER ultrassom'
);
PREPARE stmt FROM @transplante_data_ultrassom; EXECUTE stmt; DEALLOCATE PREPARE stmt;
