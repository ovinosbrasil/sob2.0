-- Execute no schema da fazenda após 011_animais_id_receptora.sql.
-- Usa os nomes preenchidos em animais.receptora, mantendo zeros à esquerda.
START TRANSACTION;

-- Cadastra nomes ausentes sem duplicar nem reativar receptoras existentes.
INSERT INTO receptora (nome, ativo)
SELECT DISTINCT TRIM(receptora), 1
FROM animais
WHERE receptora IS NOT NULL
  AND TRIM(receptora) <> ''
ON DUPLICATE KEY UPDATE id = receptora.id;

-- Preserva o nome histórico e os vínculos já preenchidos.
UPDATE animais AS a
JOIN receptora AS r ON r.nome = TRIM(a.receptora)
SET a.id_receptora = r.id
WHERE a.receptora IS NOT NULL
  AND TRIM(a.receptora) <> ''
  AND a.id_receptora IS NULL;

COMMIT;
