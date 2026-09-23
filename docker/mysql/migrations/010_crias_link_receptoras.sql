-- Execute no schema da fazenda após 009_crias_id_receptora.sql.
-- Importa somente nomes usados em embrionagem, sem duplicar ou reativar receptoras.
START TRANSACTION;

INSERT INTO receptora (nome, ativo)
SELECT DISTINCT TRIM(receptora), 1
FROM crias
WHERE tipo = 'Embrionagem'
  AND receptora IS NOT NULL
  AND TRIM(receptora) <> ''
ON DUPLICATE KEY UPDATE id = receptora.id;

-- Preenche os vínculos ausentes pelo nome, preservando o texto histórico.
-- Não altera registros de outros tipos de reprodução nem vínculos já preenchidos.
UPDATE crias AS c
JOIN receptora AS r ON r.nome = TRIM(c.receptora)
SET c.id_receptora = r.id
WHERE c.tipo = 'Embrionagem'
  AND c.receptora IS NOT NULL
  AND TRIM(c.receptora) <> ''
  AND c.id_receptora IS NULL;

COMMIT;
