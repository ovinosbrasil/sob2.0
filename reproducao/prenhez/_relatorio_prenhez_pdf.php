<?php
require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

class RelatorioPrenhezPDF extends FPDF
{
    private $filtros;
    private $larguras = array(36, 60, 40, 22, 32);
    private $geradoEm;
    private $mostrarTransplante;

    public function __construct(array $filtros)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->filtros = $filtros;
        $this->mostrarTransplante = ($filtros['reproducao'] ?? '') === 'te';
        $this->larguras = $this->mostrarTransplante ? array(33, 48, 32, 30, 18, 29) : array(40,  70, 45, 35);
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(false);
        $this->AliasNbPages();
        $this->geradoEm = (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('d/m/Y H:i:s');
        $this->SetTitle('Prenhez atual - Sistema Ovinos Brasil', true);
        $this->SetAuthor('Sistema Ovinos Brasil');
    }

    private function texto($valor)
    {
        return iconv('UTF-8', 'Windows-1252//TRANSLIT', (string)$valor);
    }

    public function Header()
    {
        $this->Image(__DIR__ . '/../../img/logo.png', 65, 10, 80);
        $this->SetY(35);
        $this->SetDrawColor(210, 210, 210);
        $this->SetTextColor(90, 90, 90);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->texto('Prenhez atual'), 0, 1, 'C');
        $tipos = array(''=>'Todos', 'monta'=>'Monta natural', 'inseminacao'=>'Inseminação artificial', 'te'=>'Transplante de embriões');
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 4, $this->texto('Tipo: ' . $tipos[$this->filtros['reproducao'] ?? ''] . ' | Período: ' . ($this->filtros['data_inicio'] ?: 'Sem limite') . ' a ' . ($this->filtros['data_fim'] ?: 'Sem limite')), 0, 1, 'C');
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 7);
        $titulos = array('Tipo reprodução', 'Lote - Macho');
        if ($this->mostrarTransplante) { $titulos[] = 'Doadora'; }
        $titulos[] = 'Animal/Receptora';
        if ($this->mostrarTransplante) { $titulos[] = 'Qtd. embriões'; }
        $titulos[] = 'Nascimento previsto';
        foreach ($titulos as $i=>$titulo) {
            $this->Cell($this->larguras[$i], 5, $this->texto($titulo), 1, 0, 'C');
        }
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(170, 170, 170);
        $this->Cell(35, 5, $this->texto('Página ' . $this->PageNo() . '/{nb}'));
        $this->Cell(110, 5, $this->texto('Relatório gerado por: Sistema Ovinos Brasil'), 0, 0, 'C');
        $this->Cell(45, 5, $this->geradoEm, 0, 0, 'R');
    }

    private function alturaTexto($largura, $texto)
    {
        $limite = ($largura - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $texto = rtrim(str_replace("\r", '', $texto), "\n");
        $inicio = 0; $separador = -1; $soma = 0; $linhas = 1; $i = 0;
        while ($i < strlen($texto)) {
            $caractere = $texto[$i];
            if ($caractere === "\n") { ++$linhas; ++$i; $inicio = $i; $separador = -1; $soma = 0; continue; }
            if ($caractere === ' ') { $separador = $i; }
            $soma += $this->CurrentFont['cw'][$caractere] ?? 0;
            if ($soma > $limite) {
                $i = $separador >= $inicio ? $separador + 1 : ($i === $inicio ? $i + 1 : $i);
                ++$linhas; $inicio = $i; $separador = -1; $soma = 0;
            } else { ++$i; }
        }
        return $linhas * 4.5;
    }

    public function gerar(array $linhas)
    {
        $this->AddPage();
        foreach ($linhas as $linha) {
            $data = DateTimeImmutable::createFromFormat('!Y-m-d', (string)$linha['previsao_fim']);
            $valores = array($linha['tipo'], $linha['codigo'] . ' - ' . $linha['nome_macho']);
            if ($this->mostrarTransplante) { $valores[] = $linha['nome_doadora'] ?? '--'; }
            $valores[] = $linha['femea'] . (!empty($linha['terceiro']) ? ' (Terceiro)' : '');
            if ($this->mostrarTransplante) { $valores[] = $linha['n_embrioes'] ?? '--'; }
            $valores[] = $data ? $data->format('d/m/Y') : '--';
            $this->SetFont('Arial', '', 8);
            $y = $this->GetY();
            $altura = 4.5;
            foreach ($valores as $i=>$valor) {
                $altura = max($altura, $this->alturaTexto($this->larguras[$i], $this->texto($valor)));
            }
            if ($y + $altura > 277) { $this->AddPage(); $y = $this->GetY(); $this->SetFont('Arial', '', 8); }
            $this->SetDrawColor(210, 210, 210);
            $this->SetTextColor(90, 90, 90);
            $x = 10;
            foreach ($valores as $i=>$valor) {
                $this->Rect($x, $y, $this->larguras[$i], $altura);
                $this->SetXY($x, $y);
                $this->MultiCell($this->larguras[$i], 4.5, $this->texto($valor), 0, 'C');
                $x += $this->larguras[$i];
            }
            $this->SetXY(10, $y + $altura);
        }
        if ($this->GetY() > 269) { $this->AddPage(); }
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(90, 90, 90);
        $this->Cell(0, 8, $this->texto($linhas ? 'Total: ' . count($linhas) . ' registros' : 'Nenhuma prenhez encontrada com esses critérios.'), 0, 1);
    }
}
