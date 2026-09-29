-- Vacinas passam a ser registradas individualmente por animal.
-- Registros antigos mantêm o vínculo com lote; novos registros usam NULL.
ALTER TABLE vacinas MODIFY id_lote INT NULL DEFAULT NULL;
