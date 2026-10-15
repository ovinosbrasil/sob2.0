-- Referência: estrutura local de siste870_buria comparada em 15/10/2026.
-- Destino de produção: siste870_novaesperanca. Ajuste USE se o nome for diferente.
-- Faça backup antes de executar. DDL no MySQL efetua commit implícito.
-- Não copia registros de buria, não redefine AUTO_INCREMENT e não exclui tabelas.
-- Não executar com a opção --force: interromper em caso de erro.
-- Reexecutável para a estrutura comparada; requer permissão CREATE/ALTER/INDEX.
USE `siste870_novaesperanca`;

ALTER TABLE `animais` MODIFY COLUMN `data2` date DEFAULT NULL;

ALTER TABLE `animais` MODIFY COLUMN `data3` date DEFAULT NULL;

SET @sql_equalizacao = IF(
    NOT EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'animais' AND COLUMN_NAME = 'id_receptora'),
    'ALTER TABLE `animais` ADD COLUMN `id_receptora` int DEFAULT NULL AFTER `chip`',
    'SELECT 1'
);
PREPARE equalizacao FROM @sql_equalizacao;
EXECUTE equalizacao;
DEALLOCATE PREPARE equalizacao;

SET @sql_equalizacao = IF(
    NOT EXISTS(SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'animais' AND INDEX_NAME = 'idx_animais_id_receptora'),
    'ALTER TABLE `animais` ADD KEY `idx_animais_id_receptora` (`id_receptora`)',
    'SELECT 1'
);
PREPARE equalizacao FROM @sql_equalizacao;
EXECUTE equalizacao;
DEALLOCATE PREPARE equalizacao;

SET @sql_equalizacao = IF(
    NOT EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'crias' AND COLUMN_NAME = 'id_receptora'),
    'ALTER TABLE `crias` ADD COLUMN `id_receptora` int DEFAULT NULL AFTER `receptora`',
    'SELECT 1'
);
PREPARE equalizacao FROM @sql_equalizacao;
EXECUTE equalizacao;
DEALLOCATE PREPARE equalizacao;

SET @sql_equalizacao = IF(
    NOT EXISTS(SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'crias' AND INDEX_NAME = 'idx_crias_id_receptora'),
    'ALTER TABLE `crias` ADD KEY `idx_crias_id_receptora` (`id_receptora`)',
    'SELECT 1'
);
PREPARE equalizacao FROM @sql_equalizacao;
EXECUTE equalizacao;
DEALLOCATE PREPARE equalizacao;

CREATE TABLE IF NOT EXISTS `receptora` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_receptora_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `transplante` MODIFY COLUMN `data_coleta` date DEFAULT NULL;

SET @sql_equalizacao = IF(
    NOT EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante' AND COLUMN_NAME = 'id_pai_2'),
    'ALTER TABLE `transplante` ADD COLUMN `id_pai_2` int NOT NULL AFTER `n_embrioes`',
    'SELECT 1'
);
PREPARE equalizacao FROM @sql_equalizacao;
EXECUTE equalizacao;
DEALLOCATE PREPARE equalizacao;

SET @sql_equalizacao = IF(
    NOT EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante' AND COLUMN_NAME = 'terceiro_pai_2'),
    'ALTER TABLE `transplante` ADD COLUMN `terceiro_pai_2` int NOT NULL DEFAULT ''0'' AFTER `id_pai_2`',
    'SELECT 1'
);
PREPARE equalizacao FROM @sql_equalizacao;
EXECUTE equalizacao;
DEALLOCATE PREPARE equalizacao;

SET @sql_equalizacao = IF(
    NOT EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante_controle' AND COLUMN_NAME = 'id_receptora'),
    'ALTER TABLE `transplante_controle` ADD COLUMN `id_receptora` int DEFAULT NULL AFTER `n_embrioes`',
    'SELECT 1'
);
PREPARE equalizacao FROM @sql_equalizacao;
EXECUTE equalizacao;
DEALLOCATE PREPARE equalizacao;

SET @sql_equalizacao = IF(
    NOT EXISTS(SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante_controle' AND INDEX_NAME = 'idx_te_id_receptora'),
    'ALTER TABLE `transplante_controle` ADD KEY `idx_te_id_receptora` (`id_receptora`)',
    'SELECT 1'
);
PREPARE equalizacao FROM @sql_equalizacao;
EXECUTE equalizacao;
DEALLOCATE PREPARE equalizacao;

SELECT DATABASE() AS banco_atualizado, 'Estrutura atualizada; os registros existentes foram preservados.' AS resultado;
