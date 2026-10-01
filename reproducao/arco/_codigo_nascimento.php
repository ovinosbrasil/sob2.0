<?php
function codigoNascimentoArco(array $animal)
{
    $morto = (int)($animal['status'] ?? 0) === 1;
    $causa = trim((string)($animal['causa_da_perda'] ?? ''));
    return $morto && strcasecmp($causa, 'Nascimento') === 0 ? 'Óbito' : '';
}
