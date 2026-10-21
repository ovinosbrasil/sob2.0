-- Executar no banco CENTRAL de login (DB_DATABASE), antes de publicar o PHP.
CREATE TABLE IF NOT EXISTS auth_login_limits (
    chave CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    inicio BIGINT UNSIGNED NOT NULL,
    falhas INT UNSIGNED NOT NULL DEFAULT 0,
    bloqueado_ate BIGINT UNSIGNED NOT NULL DEFAULT 0,
    atualizado_em BIGINT UNSIGNED NOT NULL,
    INDEX idx_auth_login_limits_atualizado (atualizado_em)
) ENGINE=InnoDB;
-- Agendar limpeza diária no mesmo banco (retenção de 24 horas):
-- DELETE FROM auth_login_limits WHERE atualizado_em < UNIX_TIMESTAMP() - 86400;
