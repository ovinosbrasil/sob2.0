-- Execute no schema da fazenda a atualizar, após conferir SELECT DATABASE().
-- Remove SOMENTE progene.classe e progene.categoria, sem uso funcional.
-- Não altera julgamento_controle.categoria nem qualquer outra tabela.
-- Faça backup: os valores destas duas colunas serão descartados.
-- Aplicar junto da versão do código que não grava mais estas colunas,
-- antes de utilizar cadastro/exclusão de participantes nessa versão.
-- Pode ser reexecutado. DDL tem commit implícito; não usar --force.

SET @sql_progene = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'progene' AND COLUMN_NAME = 'classe'),
    'ALTER TABLE progene DROP COLUMN classe',
    'SELECT 1'
);
PREPARE remover_coluna_progene FROM @sql_progene;
EXECUTE remover_coluna_progene;
DEALLOCATE PREPARE remover_coluna_progene;

SET @sql_progene = IF(
    EXISTS(SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'progene' AND COLUMN_NAME = 'categoria'),
    'ALTER TABLE progene DROP COLUMN categoria',
    'SELECT 1'
);
PREPARE remover_coluna_progene FROM @sql_progene;
EXECUTE remover_coluna_progene;
DEALLOCATE PREPARE remover_coluna_progene;
