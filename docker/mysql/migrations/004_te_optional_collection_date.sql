-- Execute no schema da fazenda. Permite cadastrar lotes sem data de coleta.
-- A tabela legada contém datas zero; permite sua conversão nesta sessão.
SET @te_previous_sql_mode = @@SESSION.sql_mode;
SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '');
ALTER TABLE transplante MODIFY data_coleta DATE NULL DEFAULT NULL;
UPDATE transplante SET data_coleta = NULL WHERE CAST(data_coleta AS CHAR) = '0000-00-00';
SET SESSION sql_mode = @te_previous_sql_mode;
