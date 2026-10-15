-- Execute no schema da fazenda que deseja atualizar.
-- Nascimento confirmado: status_nascimento = 1. Ultrassom positivo: ultrassom = 1.
-- Abrange monta natural, inseminação artificial e transplante de embriões.
-- Pode ser reexecutado. Preserva as datas de ultrassom registradas.

START TRANSACTION;

UPDATE monta_controle
SET ultrassom = 1
WHERE status_nascimento = 1
  AND (ultrassom IS NULL OR ultrassom <> 1);

UPDATE inseminacao_controle
SET ultrassom = 1
WHERE status_nascimento = 1
  AND (ultrassom IS NULL OR ultrassom <> 1);

UPDATE transplante_controle
SET ultrassom = 1
WHERE status_nascimento = 1
  AND (ultrassom IS NULL OR ultrassom <> 1);

COMMIT;
