-- Execute no banco da fazenda. Preserva as descrições e os lançamentos existentes.
-- Comporta material, nome dos animais, doses e identificação da parcela.
ALTER TABLE controle_financeiro MODIFY COLUMN titulo VARCHAR(255) NOT NULL;
