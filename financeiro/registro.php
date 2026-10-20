<?php
function textoRegistroFinanceiro($valor) {
    $texto = (string)$valor;
    return preg_match('//u', $texto) ? $texto : iconv('Windows-1252', 'UTF-8', $texto);
}
function vendaRegistroFinanceiro(array $registro) {
    if ((int)($registro['tipo'] ?? 0) !== 0) return false;
    foreach (array('id_animal', 'id_semen', 'id_embriao') as $campo) {
        if (!empty($registro[$campo])) return true;
    }
    return (bool)preg_match('/^(?:Pacote de vendas|Venda conjunta|Venda animal|Venda de |Sêmen:|Embrião:)/iu', textoRegistroFinanceiro($registro['titulo'] ?? ''));
}
