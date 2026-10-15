-- Referência: siste870_buria; comparação local em 15/10/2026.
-- Somente transplante difere: ordem de duas colunas e coluna extra.
-- Confira o nome do banco de produção e faça backup antes de executar.
-- Execute em janela de manutenção, sem gravações concorrentes em transplante.
-- A coluna macho_complementar será removida SOMENTE se não contiver dados.
-- DDL tem commit implícito. Interrompa ao primeiro erro; não use --force.
-- Requer CREATE ROUTINE, ALTER ROUTINE, EXECUTE e ALTER.
USE `siste870_romana`;

DROP PROCEDURE IF EXISTS `migracao_020_equalizar_romana`;
DELIMITER $$
CREATE PROCEDURE `migracao_020_equalizar_romana`()
BEGIN
    DECLARE coluna_extra INT DEFAULT 0;
    DECLARE nomes_preenchidos BIGINT DEFAULT 0;

    SELECT COUNT(*) INTO coluna_extra
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transplante'
      AND COLUMN_NAME = 'macho_complementar';

    IF coluna_extra > 0 THEN
        SELECT COUNT(*) INTO nomes_preenchidos
        FROM transplante
        WHERE OCTET_LENGTH(macho_complementar) > 0;

        IF nomes_preenchidos > 0 THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Equalizacao interrompida: macho_complementar contem dados. Revise e migre os vinculos antes de remover a coluna.';
        END IF;
    END IF;

    ALTER TABLE transplante
        MODIFY COLUMN id_pai_2 INT NOT NULL AFTER n_embrioes,
        MODIFY COLUMN terceiro_pai_2 INT NOT NULL DEFAULT 0 AFTER id_pai_2;

    IF coluna_extra > 0 THEN
        ALTER TABLE transplante DROP COLUMN macho_complementar;
    END IF;
END$$
DELIMITER ;

CALL `migracao_020_equalizar_romana`();
DROP PROCEDURE `migracao_020_equalizar_romana`;
