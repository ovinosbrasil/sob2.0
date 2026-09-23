-- Execute no schema da fazenda, não no banco central de usuários.
ALTER TABLE animais
    MODIFY data2 DATE NULL DEFAULT NULL,
    MODIFY data3 DATE NULL DEFAULT NULL;
