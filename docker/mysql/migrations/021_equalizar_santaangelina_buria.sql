-- Revisão de 15/10/2026: atualização compatível, preservando os dados de santaangelina.
-- Substitui a versão anterior da migração 021, que interrompia com erro 1644.
-- Mantém controle_financeiro.id_categoria, venda_semen.animal e VARCHARs maiores.
-- Portanto, o destino preserva essas diferenças em relação a buria.
-- Faça backup e confira USE. Execute em manutenção sem gravações concorrentes.
-- Não use --force. DDL tem commit implícito, sem rollback completo do arquivo.
USE `siste870_santaangelina`;

-- Remove a procedure auxiliar deixada pela execução anterior interrompida.
DROP PROCEDURE IF EXISTS `migracao_021_equalizar_santaangelina`;

    ALTER TABLE `acasalamento`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `id_animal` int NOT NULL AFTER `id`,
        MODIFY COLUMN `indicacao1` int NOT NULL AFTER `id_animal`,
        MODIFY COLUMN `indicacao2` int NOT NULL AFTER `indicacao1`,
        MODIFY COLUMN `indicacao3` int NOT NULL AFTER `indicacao2`,
        MODIFY COLUMN `criterio1` int NOT NULL AFTER `indicacao3`,
        MODIFY COLUMN `criterio2` int NOT NULL AFTER `criterio1`,
        MODIFY COLUMN `criterio3` int NOT NULL AFTER `criterio2`,
        MODIFY COLUMN `status` int NOT NULL AFTER `criterio3`;

    ALTER TABLE `admin`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `login` varchar(20) NOT NULL AFTER `id`,
        MODIFY COLUMN `senha` varchar(12) NOT NULL AFTER `login`,
        MODIFY COLUMN `email` varchar(150) NOT NULL AFTER `senha`,
        MODIFY COLUMN `data_inicio` varchar(10) NOT NULL AFTER `email`,
        MODIFY COLUMN `data_proximo` varchar(10) NOT NULL AFTER `data_inicio`,
        MODIFY COLUMN `fazenda` varchar(100) NOT NULL AFTER `data_proximo`,
        MODIFY COLUMN `cidade` varchar(100) NOT NULL AFTER `fazenda`,
        MODIFY COLUMN `estado` varchar(50) NOT NULL AFTER `cidade`,
        MODIFY COLUMN `responsavel` varchar(100) NOT NULL AFTER `estado`,
        MODIFY COLUMN `cep` varchar(20) NOT NULL AFTER `responsavel`,
        MODIFY COLUMN `num` int NOT NULL AFTER `cep`,
        MODIFY COLUMN `end` varchar(150) NOT NULL AFTER `num`,
        MODIFY COLUMN `telefone` varchar(20) NOT NULL AFTER `end`,
        MODIFY COLUMN `celular` varchar(20) NOT NULL AFTER `telefone`,
        MODIFY COLUMN `tecnico` varchar(100) NOT NULL AFTER `celular`,
        MODIFY COLUMN `cod` int NOT NULL AFTER `tecnico`,
        MODIFY COLUMN `municipio` varchar(50) NOT NULL AFTER `cod`,
        MODIFY COLUMN `cod_rebanho` varchar(20) NOT NULL AFTER `municipio`,
        MODIFY COLUMN `prefixo` varchar(50) NOT NULL AFTER `cod_rebanho`,
        MODIFY COLUMN `raca` varchar(50) NOT NULL AFTER `prefixo`,
        MODIFY COLUMN `cpf` varchar(20) NOT NULL AFTER `raca`,
        MODIFY COLUMN `logo` varchar(20) NOT NULL AFTER `cpf`,
        MODIFY COLUMN `cod_tecnico` varchar(20) NOT NULL AFTER `logo`;

    ALTER TABLE `animais`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `nome` varchar(200) DEFAULT NULL AFTER `id`,
        MODIFY COLUMN `raca` varchar(30) DEFAULT NULL AFTER `nome`,
        MODIFY COLUMN `data_de_entrada` date DEFAULT NULL AFTER `raca`,
        MODIFY COLUMN `data_de_nascimento` date DEFAULT NULL AFTER `data_de_entrada`,
        MODIFY COLUMN `sexo` varchar(10) DEFAULT NULL AFTER `data_de_nascimento`,
        MODIFY COLUMN `pai` varchar(200) DEFAULT NULL AFTER `sexo`,
        MODIFY COLUMN `mae` varchar(200) DEFAULT NULL AFTER `pai`,
        MODIFY COLUMN `preco_de_compra` varchar(20) DEFAULT NULL AFTER `mae`,
        MODIFY COLUMN `data_de_saida` date DEFAULT NULL AFTER `preco_de_compra`,
        MODIFY COLUMN `causa_da_perda` varchar(50) DEFAULT NULL AFTER `data_de_saida`,
        MODIFY COLUMN `observacoes_de_saida` mediumtext AFTER `causa_da_perda`,
        MODIFY COLUMN `comprador` varchar(200) DEFAULT NULL AFTER `observacoes_de_saida`,
        MODIFY COLUMN `preco_de_venda` varchar(20) DEFAULT NULL AFTER `comprador`,
        MODIFY COLUMN `observacoes` mediumtext AFTER `preco_de_venda`,
        MODIFY COLUMN `fbb` varchar(20) DEFAULT NULL AFTER `observacoes`,
        MODIFY COLUMN `link_fbb` varchar(300) NOT NULL AFTER `fbb`,
        MODIFY COLUMN `fbb_img` varchar(200) NOT NULL AFTER `link_fbb`,
        MODIFY COLUMN `peso_inicial` float DEFAULT NULL AFTER `fbb_img`,
        MODIFY COLUMN `peso2` int NOT NULL AFTER `peso_inicial`,
        MODIFY COLUMN `peso3` varchar(20) DEFAULT NULL AFTER `peso2`,
        MODIFY COLUMN `data2` date DEFAULT NULL AFTER `peso3`,
        MODIFY COLUMN `data3` date DEFAULT NULL AFTER `data2`,
        MODIFY COLUMN `tipo_reproducao` varchar(30) DEFAULT NULL AFTER `data3`,
        MODIFY COLUMN `aplicacao` varchar(10) DEFAULT NULL AFTER `tipo_reproducao`,
        MODIFY COLUMN `forma_de_pagamento` varchar(30) DEFAULT NULL AFTER `aplicacao`,
        MODIFY COLUMN `parcelas` int NOT NULL AFTER `forma_de_pagamento`,
        MODIFY COLUMN `tipo_venda` varchar(50) NOT NULL AFTER `parcelas`,
        MODIFY COLUMN `tatuagem` varchar(15) DEFAULT NULL AFTER `tipo_venda`,
        MODIFY COLUMN `tipo` int NOT NULL AFTER `tatuagem`,
        MODIFY COLUMN `confirmacao` int NOT NULL AFTER `tipo`,
        MODIFY COLUMN `prolapso` varchar(10) NOT NULL AFTER `confirmacao`,
        MODIFY COLUMN `criador` varchar(50) NOT NULL AFTER `prolapso`,
        MODIFY COLUMN `terceiro_pai` int NOT NULL DEFAULT '0' AFTER `criador`,
        MODIFY COLUMN `terceiro_mae` int NOT NULL DEFAULT '0' AFTER `terceiro_pai`,
        MODIFY COLUMN `status` int NOT NULL AFTER `terceiro_mae`,
        MODIFY COLUMN `entrada` int NOT NULL AFTER `status`,
        MODIFY COLUMN `receptora` varchar(50) NOT NULL AFTER `entrada`,
        MODIFY COLUMN `chip` varchar(30) NOT NULL AFTER `receptora`,
        MODIFY COLUMN `id_receptora` int DEFAULT NULL AFTER `chip`;

    ALTER TABLE `controle_financeiro`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `categoria` varchar(50) NOT NULL AFTER `id`,
        MODIFY COLUMN `id_tipo` int NOT NULL AFTER `categoria`,
        MODIFY COLUMN `data` date NOT NULL AFTER `id_tipo`,
        MODIFY COLUMN `valor` float NOT NULL AFTER `data`,
        MODIFY COLUMN `obs` varchar(300) NOT NULL AFTER `valor`,
        MODIFY COLUMN `titulo` varchar(50) NOT NULL AFTER `obs`,
        MODIFY COLUMN `id_animal` int NOT NULL AFTER `titulo`,
        MODIFY COLUMN `status` int NOT NULL AFTER `id_animal`,
        MODIFY COLUMN `forma_de_pagamento` varchar(50) NOT NULL AFTER `status`,
        MODIFY COLUMN `tipo` int NOT NULL AFTER `forma_de_pagamento`,
        MODIFY COLUMN `id_comprador` int NOT NULL AFTER `tipo`,
        MODIFY COLUMN `id_embriao` int NOT NULL AFTER `id_comprador`,
        MODIFY COLUMN `id_semen` int NOT NULL AFTER `id_embriao`;


    ALTER TABLE `embriao`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `pai` int NOT NULL AFTER `id`,
        MODIFY COLUMN `mae` int NOT NULL AFTER `pai`,
        MODIFY COLUMN `qtd` int NOT NULL AFTER `mae`,
        MODIFY COLUMN `data` date NOT NULL AFTER `qtd`,
        MODIFY COLUMN `terceiro` int NOT NULL AFTER `data`,
        MODIFY COLUMN `terceiro_mae` int NOT NULL AFTER `terceiro`,
        MODIFY COLUMN `botijao` varchar(30) NOT NULL AFTER `terceiro_mae`,
        MODIFY COLUMN `palheta` varchar(30) NOT NULL AFTER `botijao`,
        MODIFY COLUMN `qualidade` varchar(30) NOT NULL AFTER `palheta`;

    ALTER TABLE `monta`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `codigo` varchar(100) NOT NULL AFTER `id`,
        MODIFY COLUMN `data_inicio` date NOT NULL AFTER `codigo`,
        MODIFY COLUMN `data_fim` date NOT NULL AFTER `data_inicio`,
        MODIFY COLUMN `macho` varchar(200) NOT NULL AFTER `data_fim`,
        MODIFY COLUMN `raca` varchar(30) NOT NULL AFTER `macho`,
        MODIFY COLUMN `notificacao` varchar(5) NOT NULL AFTER `raca`,
        MODIFY COLUMN `id_animal` int NOT NULL AFTER `notificacao`,
        MODIFY COLUMN `terceiro` int NOT NULL AFTER `id_animal`;

    ALTER TABLE `monta_controle`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `id_monta` int NOT NULL AFTER `id`,
        MODIFY COLUMN `nome` varchar(200) NOT NULL AFTER `id_monta`,
        MODIFY COLUMN `status_nascimento` int NOT NULL DEFAULT '0' AFTER `nome`,
        MODIFY COLUMN `id_animal` int NOT NULL AFTER `status_nascimento`,
        MODIFY COLUMN `terceiro` int NOT NULL AFTER `id_animal`,
        MODIFY COLUMN `ultrassom` int NOT NULL AFTER `terceiro`,
        MODIFY COLUMN `data_ultrassom` date DEFAULT NULL AFTER `ultrassom`;

    ALTER TABLE `semen`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `id_animal` int NOT NULL AFTER `id`,
        MODIFY COLUMN `data` date NOT NULL AFTER `id_animal`,
        MODIFY COLUMN `qtd` int NOT NULL AFTER `data`,
        MODIFY COLUMN `vigor` varchar(10) NOT NULL AFTER `qtd`,
        MODIFY COLUMN `partida` varchar(20) NOT NULL AFTER `vigor`,
        MODIFY COLUMN `motilidade` varchar(20) NOT NULL AFTER `partida`,
        MODIFY COLUMN `congelado` varchar(100) NOT NULL AFTER `motilidade`,
        MODIFY COLUMN `botijao` varchar(100) NOT NULL AFTER `congelado`,
        MODIFY COLUMN `terceiro` int NOT NULL AFTER `botijao`,
        MODIFY COLUMN `palheta` varchar(30) NOT NULL AFTER `terceiro`,
        MODIFY COLUMN `qualidade` varchar(30) NOT NULL AFTER `palheta`;

    ALTER TABLE `terceiros`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `nome` varchar(100) NOT NULL AFTER `id`,
        MODIFY COLUMN `raca` varchar(50) NOT NULL AFTER `nome`,
        MODIFY COLUMN `fbb` varchar(50) NOT NULL AFTER `raca`,
        MODIFY COLUMN `sexo` varchar(20) NOT NULL AFTER `fbb`,
        MODIFY COLUMN `pai` varchar(100) NOT NULL AFTER `sexo`,
        MODIFY COLUMN `mae` varchar(100) NOT NULL AFTER `pai`,
        MODIFY COLUMN `tatuagem` varchar(20) NOT NULL AFTER `mae`,
        MODIFY COLUMN `ativo` tinyint(1) NOT NULL DEFAULT '1' AFTER `tatuagem`;

    ALTER TABLE `transplante`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `pai` varchar(50) NOT NULL AFTER `id`,
        MODIFY COLUMN `mae` varchar(50) NOT NULL AFTER `pai`,
        MODIFY COLUMN `data` date NOT NULL AFTER `mae`,
        MODIFY COLUMN `codigo` varchar(30) NOT NULL AFTER `data`,
        MODIFY COLUMN `qtd` int NOT NULL AFTER `codigo`,
        MODIFY COLUMN `terceiro_mae` int NOT NULL AFTER `qtd`,
        MODIFY COLUMN `terceiro_pai` int NOT NULL AFTER `terceiro_mae`,
        MODIFY COLUMN `id_pai` int NOT NULL AFTER `terceiro_pai`,
        MODIFY COLUMN `id_mae` int NOT NULL AFTER `id_pai`,
        MODIFY COLUMN `usados` int NOT NULL AFTER `id_mae`,
        MODIFY COLUMN `congelados` int NOT NULL AFTER `usados`,
        MODIFY COLUMN `data_coleta` date DEFAULT NULL AFTER `congelados`,
        MODIFY COLUMN `raca` varchar(20) NOT NULL AFTER `data_coleta`,
        MODIFY COLUMN `tipo_semen` varchar(20) NOT NULL AFTER `raca`,
        MODIFY COLUMN `n_embrioes` varchar(20) NOT NULL AFTER `tipo_semen`,
        MODIFY COLUMN `id_pai_2` int NOT NULL AFTER `n_embrioes`,
        MODIFY COLUMN `terceiro_pai_2` int NOT NULL DEFAULT '0' AFTER `id_pai_2`;

    ALTER TABLE `transplante_controle`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `receptora` varchar(50) NOT NULL AFTER `id`,
        MODIFY COLUMN `id_lote` int NOT NULL AFTER `receptora`,
        MODIFY COLUMN `status_nascimento` int NOT NULL DEFAULT '0' AFTER `id_lote`,
        MODIFY COLUMN `ultrassom` int NOT NULL AFTER `status_nascimento`,
        MODIFY COLUMN `data_ultrassom` date DEFAULT NULL AFTER `ultrassom`,
        MODIFY COLUMN `n_embrioes` varchar(20) NOT NULL AFTER `data_ultrassom`,
        MODIFY COLUMN `id_receptora` int DEFAULT NULL AFTER `n_embrioes`;

    ALTER TABLE `venda_semen`
        MODIFY COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST,
        MODIFY COLUMN `doses` int NOT NULL AFTER `id`,
        MODIFY COLUMN `comprador` int NOT NULL AFTER `doses`,
        MODIFY COLUMN `valor` decimal(10,2) NOT NULL AFTER `comprador`,
        MODIFY COLUMN `data` date NOT NULL AFTER `valor`,
        MODIFY COLUMN `parcelas` varchar(10) NOT NULL AFTER `data`,
        MODIFY COLUMN `obs` varchar(200) NOT NULL AFTER `parcelas`,
        MODIFY COLUMN `tipo_venda` varchar(50) NOT NULL AFTER `obs`,
        MODIFY COLUMN `forma_de_pagamento` varchar(100) NOT NULL AFTER `tipo_venda`,
        MODIFY COLUMN `id_semen` int NOT NULL AFTER `forma_de_pagamento`;


