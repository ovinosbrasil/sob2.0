-- Execute no schema da fazenda após 005_create_receptora.sql.
-- Importa as receptoras de todos os lotes, removendo espaços nas extremidades.
-- Ignora nomes nulos/vazios e mantém zeros à esquerda (nome é texto).
-- Pode ser reexecutado: nomes existentes preservam seu id e estado ativo.
INSERT INTO receptora (nome, ativo)
SELECT DISTINCT TRIM(receptora), 1
FROM transplante_controle
WHERE receptora IS NOT NULL
  AND TRIM(receptora) <> ''
ON DUPLICATE KEY UPDATE id = receptora.id;
