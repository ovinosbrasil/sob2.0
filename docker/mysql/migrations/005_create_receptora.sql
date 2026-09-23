-- Execute no schema da fazenda, não no banco central de usuários.
-- Mantém tamanho, charset e collation da coluna legada transplante_controle.receptora.
-- A unicidade segue latin1_swedish_ci (sem diferenciar maiúsculas de minúsculas).
CREATE TABLE IF NOT EXISTS receptora (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_receptora_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
