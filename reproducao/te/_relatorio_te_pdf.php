<?php
require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

class RelatorioTransplantePDF extends FPDF
{
    private $usuario;
    private $lote;
    private $macho;
    private $machoComplementar;
    private $femea;

    public function __construct(array $usuario, array $lote, array $macho, array $machoComplementar, array $femea)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->usuario = $usuario;
        $this->lote = $lote;
        $this->macho = $macho;
        $this->machoComplementar = $machoComplementar;
        $this->femea = $femea;
        $this->SetMargins(18, 7, 18);
        $this->SetAutoPageBreak(false);
        $this->AliasNbPages();
        $this->SetTitle($this->texto('Relatório de Colheita e Transferência de Embriões - ' . ($lote['codigo'] ?? '')), true);
        $this->SetAuthor('Sistema Ovinos Brasil');
    }

    private function texto($valor)
    {
        $convertido = iconv('UTF-8', 'Windows-1252//TRANSLIT', (string)$valor);
        return $convertido === false ? (string)$valor : $convertido;
    }

    private function valor(array $dados, $chave, $padrao = '--')
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

    private function cabecalho($pagina)
    {
        $this->SetTextColor(70, 70, 70);
        $this->SetDrawColor(195, 195, 195);
        $this->Image(__DIR__ . '/../../img/topo_relatorio.png', 60, 7, 90);
        $this->SetY(38);
        $this->SetFont('Arial', '', 11);
        $this->Cell(174, 5, $this->texto('RELATÓRIO DE COLHEITA E'), 0, 1, 'C');
        $this->Cell(174, 5, $this->texto('TRANSFERÊNCIA DE EMBRIÕES OU FIV'), 0, 1, 'C');

        $sufixo = $pagina > 1 ? '-' . $pagina : '';
        $this->SetY(51);
        $this->SetFont('Arial', 'B', 7.5);
        $this->Cell(174, 5, $this->texto('FORMULÁRIO Nº: ') . $this->texto($this->valor($this->lote, 'codigo')) . $sufixo, 0, 1);
        $this->Cell(174, 5, $this->texto('RAÇA: ') . $this->texto($this->valor($this->lote, 'raca')), 0, 1);

        $this->SetFillColor(248, 248, 248);
        $this->Cell(174, 5, $this->texto('IDENTIFICAÇÃO'), 1, 1, 'C', true);
        $this->celula(134, 5, 'CRIADOR: ' . $this->valor($this->usuario, 'responsavel'));
        $this->celula(40, 5, 'CÓDIGO: ' . $this->valor($this->usuario, 'cod'), 1, 1);

        $this->Ln(4);
        $this->SetFont('Arial', 'B', 7.5);
        $this->Cell(174, 5, $this->texto('RELATÓRIO DE TRANSFERÊNCIA DE EMBRIÕES'), 1, 1, 'C', true);
        $this->Cell(30, 10, 'COBERTURA', 1, 0, 'C');
        $this->Cell(40, 5, 'MONTA NATURAL', 1, 0, 'C');
        $this->Cell(60, 5, $this->texto('INSEMINAÇÃO ARTIFICIAL'), 1, 0, 'C');
        $this->Cell(44, 5, $this->texto('DOSES DE SÊMEN:'), 1, 0, 'C');
        $this->SetXY(48, $this->GetY() + 5);
        $this->Cell(40, 5, 'DATA:', 1, 0, 'C');
        $this->Cell(60, 5, 'DATA: ' . $this->texto($this->data($this->lote['data'] ?? '')), 1, 0, 'C');
        $this->Cell(44, 5, $this->texto($this->valor($this->lote, 'tipo_semen')), 1, 1, 'C');

        $this->Ln(2);
        $linhasIdentificacao = $this->machoComplementar ? 3 : 2;
        $alturaIdentificacao = 5 * $linhasIdentificacao;
        $y = $this->GetY();
        $this->SetFont('Arial', 'B', 7.5);
        $this->Cell(52, $alturaIdentificacao, $this->texto('IDENTIFICAÇÃO'), 1, 0, 'C');
        $this->SetXY(70, $y);
        $this->celula(87, 5, 'DOADORA: ' . $this->valor($this->femea, 'nome'));
        $this->celula(35, 5, 'FBB: ' . $this->valor($this->femea, 'fbb'), 1, 1);
        $this->SetX(70);
        $this->celula(87, 5, 'DOADOR: ' . $this->valor($this->macho, 'nome'));
        $this->celula(35, 5, 'FBB: ' . $this->valor($this->macho, 'fbb'), 1, 1);
        if ($this->machoComplementar) {
            $this->SetX(70);
            $this->celula(87, 5, 'DOADOR: ' . $this->valor($this->machoComplementar, 'nome'));
            $this->celula(35, 5, 'FBB: ' . $this->valor($this->machoComplementar, 'fbb'), 1, 1);
        }

        $this->Ln(2);
        $larguras = array(24, 32, 36, 28, 54);
        $valores = array(
            'EMBRIÕES',
            'COLETADOS: ' . $this->valor($this->lote, 'qtd', '0'),
            'CONGELADOS: ' . $this->valor($this->lote, 'congelados', '0'),
            'USADOS: ' . $this->valor($this->lote, 'usados', '0'),
            'DATA COLHEITA: ' . $this->data($this->lote['data_coleta'] ?? '')
        );
        foreach ($valores as $i => $valor) {
            $this->celula($larguras[$i], 5, $valor, 1, $i === 4 ? 1 : 0, 'C', true);
        }
    }

    private function tabelaReceptoras(array $receptoras)
    {
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 7.5);
        $this->Cell(174, 5, $this->texto('TRANSFERÊNCIA DE EMBRIÕES'), 1, 1, 'C', true);
        $this->Cell(174, 5, $this->texto('DATA TRANSFERÊNCIA: ') . $this->texto($this->data($this->lote['data_coleta'] ?? '')), 1, 1);

        $larguraReceptora = 19.5;
        $larguraEmbrioes = 21.75;
        $espacoEntreConjuntos = 3;
        for ($coluna = 0; $coluna < 4; $coluna++) {
            $this->Cell($larguraReceptora, 9, 'RECEPTORA', 1, 0, 'C');
            $this->Cell($larguraEmbrioes, 9, $this->texto("Nº\nEMBRIÕES"), 1, 0, 'C');
            if ($coluna < 3) {
                $this->Cell($espacoEntreConjuntos, 9, '', 0, 0);
            }
        }
        $this->Ln();

        for ($linha = 0; $linha < 18; $linha++) {
            for ($coluna = 0; $coluna < 4; $coluna++) {
                $indice = $linha * 4 + $coluna;
                $item = $receptoras[$indice] ?? array();
                $this->celula($larguraReceptora, 5, $item['receptora'] ?? '', 1, 0, 'C');
                $this->celula($larguraEmbrioes, 5, $item ? ($item['n_embrioes'] ?? '0') : '', 1, 0, 'C');
                if ($coluna < 3) {
                    $this->Cell($espacoEntreConjuntos, 5, '', 0, 0);
                }
            }
            $this->Ln();
        }
    }

    private function rodape()
    {
        $this->SetTextColor(70, 70, 70);
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetXY(18, 238);
        $this->Cell(87, 5, '__________________________________', 0, 0, 'C');
        $this->Cell(87, 5, '__________________________________', 0, 1, 'C');
        $this->Cell(87, 4, $this->texto($this->valor($this->usuario, 'responsavel')), 0, 0, 'C');
        $this->Cell(87, 4, $this->texto($this->valor($this->usuario, 'tecnico')), 0, 1, 'C');
        $this->Cell(87, 4, $this->texto($this->valor($this->usuario, 'cod')), 0, 0, 'C');
        $this->Cell(87, 4, $this->texto($this->valor($this->usuario, 'cod_tecnico')), 0, 1, 'C');

        $this->SetY(266);
        $this->SetFont('Arial', 'B', 6.5);
        $this->Cell(174, 4, $this->texto('AVENIDA 7 DE SETEMBRO, 1159 - CX POSTAL, 145 - BAGÉ/RS - CEP 96400-970'), 0, 1, 'C');
        $this->Cell(174, 4, $this->texto('FONE: (53) 3242-8422 - FAX: (53) 3242-9522 - E-MAIL: REGISTRO@ARCOOVINOS.COM.BR'), 0, 1, 'C');
    }

    public function paginaReceptoras(array $receptoras, $pagina)
    {
        $this->AddPage();
        $this->cabecalho($pagina);
        $this->tabelaReceptoras($receptoras);
        $this->rodape();
    }
}
