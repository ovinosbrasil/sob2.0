-- Comparação local em 15/10/2026 com siste870_buria.
-- Adiciona somente colunas ausentes; preserva admin.cod como VARCHAR(20).
-- Faça backup e confira o nome do banco antes de executar.
-- DDL tem commit implícito. Interrompa em caso de erro; não use --force.
-- Registros existentes recebem string vazia nas duas novas colunas.
USE `siste870_teste`;

SET @sql_teste = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin' AND COLUMN_NAME = 'cod_tecnico'),
    'SELECT 1',
    'ALTER TABLE admin ADD COLUMN cod_tecnico VARCHAR(20) NOT NULL AFTER logo'
);
PREPARE adicionar_coluna_teste FROM @sql_teste;
EXECUTE adicionar_coluna_teste;
DEALLOCATE PREPARE adicionar_coluna_teste;

SET @sql_teste = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante' AND COLUMN_NAME = 'n_embrioes'),
    'SELECT 1',
    'ALTER TABLE transplante ADD COLUMN n_embrioes VARCHAR(20) NOT NULL AFTER tipo_semen'
);
PREPARE adicionar_coluna_teste FROM @sql_teste;
EXECUTE adicionar_coluna_teste;
DEALLOCATE PREPARE adicionar_coluna_teste;
