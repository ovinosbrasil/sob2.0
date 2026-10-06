<?php
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

class RelatorioTerceirosPDF extends FPDF
{
    private $larguras = array(9, 57, 17, 45, 45, 17);
    private $geradoEm;

    public function __construct()
    {
        parent::__construct('P', 'mm', 'A4');
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(false);
        $this->AliasNbPages();
        $this->geradoEm = (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('d/m/Y H:i:s');
        $this->SetTitle('Animais de Terceiros - Sistema Ovinos Brasil', true);
        $this->SetAuthor('Sistema Ovinos Brasil');
    }

    private function texto($valor)
    {
        return iconv('UTF-8', 'Windows-1252//TRANSLIT', (string)$valor);
    }

    public function Header()
    {
        $this->Image(__DIR__ . '/../img/logo.png', 65, 10, 80);
        $this->SetY(35);
        $this->SetDrawColor(210, 210, 210);
        $this->SetTextColor(90, 90, 90);
        $this->SetFont('Arial', 'B', 8);
        foreach (array('Nº', 'Animal', 'Sexo', 'Pai', 'Mãe', 'Situação') as $i => $titulo) {
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

    // Quebra inclusive nomes longos sem espaços, sem cortar o texto.
    private function linhas($texto, $largura)
    {
        $linhas = array();
        $linha = '';
        foreach (preg_split('/\s+/', trim($texto)) as $palavra) {
            $candidata = $linha === '' ? $palavra : $linha . ' ' . $palavra;
            if ($this->GetStringWidth($candidata) <= $largura) {
                $linha = $candidata;
                continue;
            }
            if ($linha !== '') { $linhas[] = $linha; }
            $linha = '';
            foreach (str_split($palavra) as $letra) {
                if ($linha !== '' && $this->GetStringWidth($linha . $letra) > $largura) {
                    $linhas[] = $linha;
                    $linha = '';
                }
                $linha .= $letra;
            }
        }
        $linhas[] = $linha;
        return $linhas;
    }

    public function animal(array $animal, $numero)
    {
        $valores = array($numero);
        foreach (array('nome', 'sexo', 'pai', 'mae') as $campo) {
            $valor = trim((string)($animal[$campo] ?? ''));
            $valores[] = $valor === '' ? '--' : $valor;
        }
        $valores[] = !empty($animal['ativo']) ? 'Ativo' : 'Inativo';
        $this->SetFont('Arial', '', 8);
        $celulas = array();
        $altura = 4.5;
        foreach ($valores as $i => $valor) {
            $celulas[$i] = $this->linhas($this->texto($valor), $this->larguras[$i] - 2);
            $altura = max($altura, count($celulas[$i]) * 4.5);
        }
        if ($this->GetY() + $altura > 277) {
            $this->AddPage();
            $this->SetFont('Arial', '', 8);
        }
        $this->SetTextColor(90, 90, 90);
        $this->SetDrawColor(210, 210, 210);
        $x = 10;
        $y = $this->GetY();
        foreach ($celulas as $i => $linhas) {
            $this->Rect($x, $y, $this->larguras[$i], $altura);
            foreach ($linhas as $j => $linha) {
                $this->SetXY($x, $y + $j * 4.5);
                $this->Cell($this->larguras[$i], 4.5, $linha, 0, 0, 'C');
            }
            $x += $this->larguras[$i];
        }
        $this->SetXY(10, $y + $altura);
    }

    public function vazio()
    {
        $this->SetDrawColor(210, 210, 210);
        $this->SetTextColor(90, 90, 90);
        $this->SetFont('Arial', '', 9);
        $this->Cell(190, 8, $this->texto('Nenhum animal de terceiros encontrado para estes filtros.'), 1, 1, 'C');
    }
}
