<?php
require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

class RelatorioInseminacaoPDF extends FPDF
{
    private $usuario;
    private $lote;
    private $macho;

    public function __construct(array $usuario, array $lote, array $macho)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->usuario = $usuario;
        $this->lote = $lote;
        $this->macho = $macho;
        $this->SetMargins(8, 8, 8);
        $this->SetAutoPageBreak(false);
        $this->AliasNbPages();
        $this->SetTitle($this->texto('Relatório de Inseminação Artificial - ' . ($lote['codigo'] ?? '')), true);
        $this->SetAuthor('Sistema Ovinos Brasil');
    }

    private function texto($valor)
    {
        $convertido = iconv('UTF-8', 'Windows-1252//TRANSLIT', (string)$valor);
        return $convertido === false ? (string)$valor : $convertido;
    }

    private function valor($dados, $chave, $padrao = '--')
    {
        $valor = trim((string)($dados[$chave] ?? ''));
        return $valor === '' ? $padrao : $valor;
    }

    private function data($valor)
    {
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$valor, 0, 10));
        return $data ? $data->format('d/m/Y') : '--';
    }

    private function celula($largura, $altura, $texto, $borda = 1, $quebra = 0, $alinhamento = 'L', $negrito = false)
    {
        $this->SetFont('Arial', $negrito ? 'B' : '', 7.5);
        $this->Cell($largura, $altura, $this->texto($texto), $borda, $quebra, $alinhamento);
    }

    private function cabecalhoDocumento($numeroPagina)
    {
        $this->Image(__DIR__ . '/../../img/topo_relatorio.png', 60, 7, 90);
        $this->SetY(37);
        $this->SetTextColor(70, 70, 70);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(194, 6, $this->texto('RELATÓRIO DE INSEMINAÇÃO ARTIFICIAL'), 0, 1, 'C');

        $sufixo = $numeroPagina > 1 ? '-' . $numeroPagina : '';
        $this->SetFont('Arial', '', 8);
        $this->SetX(8);
        $this->Cell(97, 6, $this->texto('FORMULÁRIO N°: ' . ($this->lote['codigo'] ?? '--') . $sufixo), 0, 0);
        $this->Cell(97, 6, $this->texto('RAÇA: ' . ($this->lote['raca'] ?? '--')), 0, 1);

        $this->SetFillColor(245, 245, 245);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(194, 5, $this->texto('IDENTIFICAÇÃO'), 1, 1, 'C', true);
        $this->celula(145, 5, 'Criador: ' . $this->valor($this->usuario, 'responsavel'));
        $this->celula(49, 5, 'Código: ' . $this->valor($this->usuario, 'cod'), 1, 1);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(194, 5, $this->texto('TÉCNICA DE INSEMINAÇÃO'), 1, 1, 'C', true);
        $this->celula(64, 6, '(  ) Inseminação Vaginal', 1, 0, 'C');
        $this->celula(64, 6, '(  ) Inseminação Cervical', 1, 0, 'C');
        $this->celula(66, 6, '(  ) Inseminação por Laparoscopia***', 1, 1, 'C');

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(194, 5, $this->texto('INFORMAÇÕES DO CARNEIRO DOADOR'), 1, 1, 'C', true);
        $this->celula(145, 5, '*Carneiro: ' . $this->valor($this->macho, 'nome'));
        $this->celula(49, 5, 'FBB: ' . $this->valor($this->macho, 'fbb'), 1, 1);
    }

    private function tabelaOvelhas(array $ovelhas)
    {
        $larguras = array(18, 70, 26, 27, 27, 26);
        $this->SetDrawColor(190, 190, 190);
        $this->SetTextColor(70, 70, 70);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell($larguras[0], 12, $this->texto('TIPO SÊMEN'), 1, 0, 'C');
        $this->Cell($larguras[1], 12, $this->texto('OVELHAS'), 1, 0, 'C');
        $this->Cell($larguras[2], 12, 'FBB', 1, 0, 'C');
        $this->Cell($larguras[3] + $larguras[4], 6, $this->texto('IA REALIZADA'), 1, 0, 'C');
        $this->Cell($larguras[5], 12, $this->texto('QTD. DOSES'), 1, 0, 'C');
        $this->SetXY(8 + $larguras[0] + $larguras[1] + $larguras[2], $this->GetY() + 6);
        $this->Cell($larguras[3], 6, $this->texto('INÍCIO'), 1, 0, 'C');
        $this->Cell($larguras[4], 6, 'FIM', 1, 1, 'C');

        $tipoSemen = array('A fresco' => 'F', 'Congelado' => 'C', 'Refrigerado' => 'R');
        $sigla = $tipoSemen[$this->lote['semen'] ?? ''] ?? '--';
        $data = $this->data($this->lote['data'] ?? '');
        $this->SetFont('Arial', '', 7.5);

        foreach ($ovelhas as $item) {
            $animal = $item['animal'];
            $nome = $this->valor($animal, 'nome');
            if (!empty($item['terceiro'])) { $nome .= ' (Terceiro)'; }
            $valores = array(
                $sigla,
                $nome,
                $this->valor($animal, 'fbb'),
                $data,
                $data,
                '1'
            );
            foreach ($valores as $indice => $valor) {
                $this->Cell($larguras[$indice], 6, $this->texto($valor), 1, 0, 'C');
            }
            $this->Ln();
        }
    }

    private function rodapeDocumento()
    {
        $this->SetTextColor(70, 70, 70);
        $this->SetFont('Arial', '', 7.5);
        $this->SetXY(8, 254);
        $this->Cell(97, 5, $this->texto('__________________________________'), 0, 0, 'C');
        $this->Cell(97, 5, $this->texto('__________________________________'), 0, 1, 'C');
        $this->Cell(97, 4, $this->texto($this->valor($this->usuario, 'responsavel')), 0, 0, 'C');
        $this->Cell(97, 4, $this->texto($this->valor($this->usuario, 'tecnico') . ' ***'), 0, 1, 'C');
        $this->Cell(97, 4, $this->texto('Cod.: ___________________'), 0, 0, 'C');
        $this->Cell(97, 4, $this->texto('CRM: ____________________'), 0, 1, 'C');

        $this->SetFont('Arial', 'B', 6.5);
        $this->SetY(274);
        $this->Cell(194, 4, $this->texto('Avenida 7 de Setembro, 1159 - CX Postal, 145 - Bagé/RS - CEP 96400-970'), 0, 1, 'C');
        $this->Cell(194, 4, $this->texto('Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br'), 0, 1, 'C');
    }

    public function paginaOvelhas(array $ovelhas, $numeroPagina)
    {
        $this->AddPage();
        $this->cabecalhoDocumento($numeroPagina);
        $this->SetY(82);
        $this->tabelaOvelhas($ovelhas);
        $this->rodapeDocumento();
    }
}
