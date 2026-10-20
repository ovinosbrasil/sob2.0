-- Execute no banco da fazenda desejada.
-- Confirma receitas e despesas com 30 dias ou mais de antecedência.
-- Data de referência no fuso America/Bahia (UTC-03:00).
SET @data_corte = DATE_SUB(DATE(CONVERT_TZ(UTC_TIMESTAMP(), '+00:00', '-03:00')), INTERVAL 30 DAY);

-- Confira o corte, a quantidade e o valor antes de executar a atualização abaixo.
SELECT @data_corte AS data_limite_inclusiva,
       COUNT(*) AS registros_a_confirmar,
       COALESCE(SUM(valor), 0) AS valor_total
FROM controle_financeiro
WHERE data <= @data_corte
  AND (status = 0 OR status IS NULL);

START TRANSACTION;

UPDATE controle_financeiro
SET status = 1
WHERE data <= @data_corte
  AND (status = 0 OR status IS NULL);

SELECT ROW_COUNT() AS registros_confirmados;

COMMIT;
