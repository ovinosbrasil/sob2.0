-- Execute no schema da fazenda.
-- Zero representa um macho complementar não informado.
SET @te_add_id_pai_2 = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante' AND COLUMN_NAME = 'id_pai_2'),
    'SELECT 1',
    'ALTER TABLE transplante ADD COLUMN id_pai_2 INT NOT NULL DEFAULT 0 AFTER id_pai'
);
PREPARE te_column FROM @te_add_id_pai_2;
EXECUTE te_column;
DEALLOCATE PREPARE te_column;

SET @te_add_terceiro_pai_2 = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante' AND COLUMN_NAME = 'terceiro_pai_2'),
    'SELECT 1',
    'ALTER TABLE transplante ADD COLUMN terceiro_pai_2 INT NOT NULL DEFAULT 0 AFTER terceiro_pai'
);
PREPARE te_column FROM @te_add_terceiro_pai_2;
EXECUTE te_column;
DEALLOCATE PREPARE te_column;

-- Preserva os nomes eventualmente gravados pela versão anterior.
UPDATE transplante AS t
JOIN terceiros AS a ON a.nome = t.macho_complementar AND a.sexo = 'Macho'
SET t.id_pai_2 = a.id, t.terceiro_pai_2 = 1
WHERE t.id_pai_2 = 0 AND t.macho_complementar <> '';

UPDATE transplante AS t
JOIN animais AS a ON a.nome = t.macho_complementar AND a.sexo = 'Macho'
SET t.id_pai_2 = a.id, t.terceiro_pai_2 = 0
WHERE t.id_pai_2 = 0 AND t.macho_complementar <> '';
