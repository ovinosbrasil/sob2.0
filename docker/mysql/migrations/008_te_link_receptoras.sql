-- Execute no schema da fazenda após 007_te_id_receptora.sql.
-- Inclui nomes utilizados desde a importação inicial, sem reativar cadastros.
INSERT INTO receptora (nome, ativo)
SELECT DISTINCT TRIM(receptora), 1
FROM transplante_controle
WHERE receptora IS NOT NULL AND TRIM(receptora) <> ''
ON DUPLICATE KEY UPDATE id = receptora.id;

-- Preenche somente vínculos ausentes; preserva o texto histórico.
UPDATE transplante_controle AS tc
JOIN receptora AS r ON r.nome = TRIM(tc.receptora)
SET tc.id_receptora = r.id
WHERE tc.id_receptora IS NULL;
