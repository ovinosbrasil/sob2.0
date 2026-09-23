-- Execute no schema da fazenda.
-- Define ultrassom positivo para registros com nascimento confirmado.
-- Pode ser reexecutado sem alterar registros já positivos.
UPDATE transplante_controle
SET ultrassom = 1
WHERE status_nascimento = 1
  AND (ultrassom IS NULL OR ultrassom <> 1);
