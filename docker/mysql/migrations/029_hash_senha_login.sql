-- Executar SOMENTE no banco central de login (DB_DATABASE), antes do novo PHP.
-- Exemplo padrão: siste870_sob. Não executar nos bancos das fazendas.
-- Preserva os valores existentes. O login converte senhas antigas em hash.
ALTER TABLE `user` MODIFY COLUMN `senha` VARCHAR(255) NOT NULL;
