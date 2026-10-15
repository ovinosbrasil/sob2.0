-- Limpa somente registros de progene com qtd zero ou negativa nas cinco fazendas.
-- Confira os nomes abaixo e faça backup antes de executar.
-- Não altera inscrições de animais nem registros com qtd positiva.
-- Reexecutável. Não usar --force: interromper e executar ROLLBACK se ocorrer erro.
START TRANSACTION;

DELETE FROM `siste870_buria`.`progene` WHERE qtd <= 0;
SELECT 'siste870_buria' AS banco, ROW_COUNT() AS registros_excluidos;

DELETE FROM `siste870_novaesperanca`.`progene` WHERE qtd <= 0;
SELECT 'siste870_novaesperanca' AS banco, ROW_COUNT() AS registros_excluidos;

DELETE FROM `siste870_romana`.`progene` WHERE qtd <= 0;
SELECT 'siste870_romana' AS banco, ROW_COUNT() AS registros_excluidos;

DELETE FROM `siste870_santaangelina`.`progene` WHERE qtd <= 0;
SELECT 'siste870_santaangelina' AS banco, ROW_COUNT() AS registros_excluidos;

DELETE FROM `siste870_teste`.`progene` WHERE qtd <= 0;
SELECT 'siste870_teste' AS banco, ROW_COUNT() AS registros_excluidos;

COMMIT;
