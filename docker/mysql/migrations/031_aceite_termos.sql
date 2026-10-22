-- Executar no banco CENTRAL de login (DB_DATABASE), antes de publicar o PHP.
-- Usuários existentes e novos precisam confirmar o aceite individualmente.
ALTER TABLE `user` ADD COLUMN `termos_aceitos` TINYINT(1) NOT NULL DEFAULT 0;
