<?php
require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

class RelatorioMontaPDF extends FPDF
{
    private $usuario;
    private $lote;
    private $macho;
    private $geradoEm;

    public function __construct(array $usuario, array $lote, array $macho)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->usuario = $usuario;
        $this->lote = $lote;
        $this->macho = $macho;
        $this->geradoEm = (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('d/m/Y');
        $this->SetMargins(10, 8, 10);
        $this->SetAutoPageBreak(false);
        $this->AliasNbPages();
        $this->SetTitle($this->texto('Notificação de cobertura - ' . ($lote['codigo'] ?? '')), true);
        $this->SetAuthor('Sistema Ovinos Brasil');
    }

    private function texto($valor)
    {
        $convertido = iconv('UTF-8', 'Windows-1252//TRANSLIT', (string)$valor);
        return $convertido === false ? (string)$valor : $convertido;
    }

    private function data($valor)
    {
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$valor, 0, 10));
        return $data ? $data->format('d/m/Y') : '--';
    }

    private function valor($chave, $padrao = '--')
    {
        $valor = trim((string)($this->usuario[$chave] ?? ''));
        return $valor === '' ? $padrao : $valor;
    }

    private function campo($x, $y, $rotulo, $valor, $largura = 85)
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetTextColor(75, 75, 75);
        $this->Cell($this->GetStringWidth($this->texto($rotulo . ': ')) + 1, 4, $this->texto($rotulo . ':'));
        $this->SetFont('Arial', '', 7.5);
        $this->Cell($largura, 4, $this->texto($valor));
    }

    public function Header()
    {
        $this->Image(__DIR__ . '/../../img/topo_relatorio.png', 60, 8, 90);
        $this->SetY(39);
        $this->SetTextColor(75, 75, 75);
        $this->SetFont('Arial', '', 10);
        $this->Cell(190, 5, $this->texto('ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O'), 0, 1, 'C');

        $this->SetFont('Arial', '', 13);
        $larguraTitulo = $this->GetStringWidth($this->texto('NOTIFICAÇÃO DE COBERTURA '));
        $inicio = (210 - ($larguraTitulo + 34)) / 2;
        $this->SetX($inicio);
        $this->Cell($larguraTitulo, 6, $this->texto('NOTIFICAÇÃO DE COBERTURA '), 0, 0);
        $this->SetTextColor(255, 0, 102);
        $marcacao = ($this->lote['notificacao'] ?? '') === 'PO' ? 'PO (  x  )   PC (   )' : 'PO (   )   PC (  x  )';
        $this->Cell(34, 6, $this->texto($marcacao), 0, 1);

        $sufixo = $this->PageNo() > 1 ? '-' . $this->PageNo() : '';
        $this->campo(18, 55, 'NÚMERO', ($this->lote['codigo'] ?? '--') . $sufixo);
        $this->campo(18, 62, 'CRIADOR', $this->valor('responsavel'));
        $this->campo(105, 62, 'COD', $this->valor('cod'));
        $this->campo(18, 66.5, 'END', trim($this->valor('end', '') . ' ' . $this->valor('num', '')) ?: '--');
        $this->campo(18, 71, 'CIDADE', $this->valor('cidade') . ' - ' . $this->valor('estado', ''));
        $this->campo(105, 71, 'CEP', $this->valor('cep'));
        $this->campo(18, 75.5, 'ESTABELECIMENTO', $this->valor('fazenda'));
        $this->campo(18, 80, 'TELEFONE', $this->valor('telefone'));
        $this->campo(83, 80, 'CELULAR', $this->valor('celular'));
        $this->campo(18, 84.5, 'MUNICÍPIO', $this->valor('municipio'));
        $this->campo(105, 84.5, 'TÉCNICO', $this->valor('tecnico'));

        $this->SetXY(18, 92);
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetTextColor(255, 0, 102);
        $this->Cell(174, 4, $this->texto('OBS.: É INDISPENSÁVEL A INDICAÇÃO DO Nº DE REGISTRO (FBB) DO CARNEIRO E DAS OVELHAS.'));

        $this->campo(18, 101, 'RAÇA', $this->lote['raca'] ?? '--');
        $this->campo(18, 105.5, 'CARNEIRO PAI', $this->macho['nome'] ?? '--');
        $this->campo(18, 110, 'TAT', $this->macho['tatuagem'] ?? '--');
        $this->campo(18, 114.5, 'REG', $this->macho['fbb'] ?? '--');
        $this->campo(18, 119, 'PERÍODO/COB', $this->data($this->lote['data_inicio'] ?? '') . ' a ' . $this->data($this->lote['data_fim'] ?? ''));
        $this->SetXY(18, 127);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(75, 75, 75);
        $this->Cell(80, 4, $this->texto('MONTA NATURAL'));
    }

    private function tabela($x, $y, array $animais)
    {
        $larguras = array(44, 17, 24);
        $this->SetDrawColor(205, 205, 205);
        $this->SetTextColor(80, 80, 80);
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell(array_sum($larguras), 5.5, $this->texto('Relação das ovelhas'), 1, 1, 'C');
        $this->SetX($x);
        foreach (array('Nome', 'Tat', 'FBB') as $indice => $titulo) {
            $this->Cell($larguras[$indice], 4.5, $this->texto($titulo), 1, 0, 'C');
        }
        $this->Ln();

        $this->SetFont('Arial', '', 8);
        foreach ($animais as $item) {
            $animal = $item['animal'];
            $nome = ($animal['nome'] ?? '--') . ($item['terceiro'] ? ' (Terceiro)' : '');
            $valores = array($nome, $animal['tatuagem'] ?? '--', $animal['fbb'] ?? '--');
            $this->SetX($x);
            foreach ($valores as $indice => $valor) {
                $valor = trim((string)$valor) === '' ? '--' : $valor;
                $this->Cell($larguras[$indice], 4.5, $this->texto($valor), 1, 0, 'C');
            }
            $this->Ln();
        }
    }

    public function paginaOvelhas(array $animais)
    {
        $this->AddPage();
        $this->tabela(18, 134, array_slice($animais, 0, 15));
        $this->tabela(107, 134, array_slice($animais, 15, 15));
    }

    public function Footer()
    {
        $this->SetTextColor(75, 75, 75);
        $this->SetFont('Arial', 'B', 7);
        $this->SetXY(50, 261);
        $this->Cell(45, 5, $this->texto('DATA: ' . $this->geradoEm), 0, 0, 'C');
        $this->Cell(75, 5, $this->texto('ASSINATURA: __________________________________'), 0, 1, 'C');
        $this->SetXY(35, 269);
        $this->Cell(140, 4, $this->texto('AVENIDA 7 DE SETEMBRO, 1159 - CX POSTAL, 145 - BAGÉ/RS - CEP 96400-970'), 0, 1, 'C');
        $this->SetX(35);
        $this->Cell(140, 4, $this->texto('FONE: (53) 3242-8422 - FAX: (53) 3242-9522 - E-MAIL: REGISTRO@ARCOOVINOS.COM.BR'), 0, 0, 'C');
    }
}
