<?php
require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

class RelatorioArcoPDF extends FPDF
{
    private $usuario;
    private $numeracao;
    private $raca;
    private $tipo;

    public function __construct(array $usuario, $numeracao, $raca, $tipo)
    {
        parent::__construct('P','mm','A4');
        $this->usuario=$usuario; $this->numeracao=(string)$numeracao; $this->raca=(string)$raca; $this->tipo=(string)$tipo;
        $this->SetMargins(10,8,10); $this->SetAutoPageBreak(false);
        $this->SetTitle($this->texto('Notificação de nascimento - '.$this->numeracao),true);
        $this->SetAuthor('Sistema Ovinos Brasil');
    }
    private function texto($valor) { $r=iconv('UTF-8','Windows-1252//TRANSLIT',(string)$valor); return $r===false?(string)$valor:$r; }
    private function valor($campo,$padrao='--') { $v=trim((string)($this->usuario[$campo]??'')); return $v===''?$padrao:$v; }
    private function campo($x,$y,$rotulo,$valor,$largura=80) {
        $this->SetXY($x,$y); $this->SetTextColor(70,70,70); $this->SetFont('Arial','B',7.2);
        $this->Cell($this->GetStringWidth($this->texto($rotulo.': '))+1,3.8,$this->texto($rotulo.':'));
        $this->SetFont('Arial','',7.2); $this->Cell($largura,3.8,$this->texto($valor));
    }
    public function Header()
    {
        $this->Image(__DIR__.'/../../img/topo_relatorio.png',65,8,80);
        $this->SetY(37); $this->SetTextColor(75,75,75); $this->SetFont('Arial','',8.5);
        $this->Cell(190,4,$this->texto('ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O'),0,1,'C');
        $this->SetTextColor(255,0,102); $this->SetFont('Arial','',12);
        $this->Cell(190,5,$this->texto('NOTIFICAÇÃO DE NASCIMENTO'),0,1,'C');
        $numero=$this->numeracao!==''?$this->numeracao:'--';
        $this->campo(18,52,'NÚMERO',$numero.'-'.$this->PageNo());
        $this->campo(18,59,'CRIADOR',$this->valor('responsavel')); $this->campo(105,59,'COD',$this->valor('cod'));
        $this->campo(18,63,'END',trim($this->valor('end','').' '.$this->valor('num',''))?:'--');
        $this->campo(18,67,'CIDADE',$this->valor('cidade').' - '.$this->valor('estado','')); $this->campo(105,67,'CEP',$this->valor('cep'));
        $this->campo(18,71,'ESTABELECIMENTO',$this->valor('fazenda'));
        $this->campo(18,75,'TELEFONE',$this->valor('telefone')); $this->campo(78,75,'CELULAR',$this->valor('celular'));
        $this->campo(18,79,'MUNICÍPIO',$this->valor('municipio')); $this->campo(105,79,'TÉCNICO',$this->valor('tecnico'));
        $this->SetXY(18,85); $this->SetTextColor(255,0,102); $this->SetFont('Arial','B',7.2);
        $this->Cell(174,4,$this->texto('OBS.: É INDISPENSÁVEL A INDICAÇÃO DO Nº DE REGISTRO (FBB) DO CARNEIRO E DAS OVELHAS.'));
        $this->campo(18,91,'RAÇA',$this->raca); $this->campo(108,91,'CATEGORIA','PO (X)  PC ( )');
        $monta=$this->tipo==='Monta Natural'?'X':' '; $te=$this->tipo==='Embrionagem'?'X':' '; $ia=$this->tipo==='Inseminação Artificial'?'X':' ';
        $this->SetXY(18,96); $this->SetTextColor(70,70,70); $this->SetFont('Arial','B',7);
        $this->Cell(174,4,$this->texto("MONTA NATURAL($monta)   TRANSPLANTE DE EMBRIÕES($te)   INSEMINAÇÃO ARTIFICIAL($ia)   SÊMEN CONGELADO( )   SÊMEN A FRESCO( )"));
    }
    private function cabecalhoTabela()
    {
        $titulos=array('Fbb','Nome','Tat.','Sexo','Nascimento','COD.','Pai','Fbb','Mãe','Fbb');
        $larguras=array(10,28,14,13,20,12,28,18,28,18);
        $this->SetXY(10,104); $this->SetDrawColor(195,195,195); $this->SetTextColor(65,65,65); $this->SetFont('Arial','B',6.5);
        foreach($titulos as $i=>$titulo)$this->Cell($larguras[$i],5,$this->texto($titulo),1,0,'C');
        $this->Ln();
        return $larguras;
    }
    public function paginaAnimais(array $animais)
    {
        $this->AddPage(); $larguras=$this->cabecalhoTabela(); $this->SetFont('Arial','',6.3); $this->SetTextColor(75,75,75);
        foreach($animais as $item){
            $valores=array($item['fbb'],$item['nome'],$item['tatuagem'],$item['sexo'],$item['nascimento'],$item['cod']??'',$item['pai_nome'],$item['pai_fbb'],$item['mae_nome'],$item['mae_fbb']);
            $this->SetX(10);
            foreach($valores as $i=>$valor){ $valor=trim((string)$valor)===''?'':$valor; $this->Cell($larguras[$i],4.6,$this->texto($valor),1,0,'C'); }
            $this->Ln();
        }
    }
    public function Footer()
    {
        $this->SetTextColor(75,75,75); $this->SetFont('Arial','B',6.5); $this->SetXY(25,266);
        $this->Cell(160,4,$this->texto('AVENIDA 7 DE SETEMBRO, 1159 - CX POSTAL, 145 - BAGÉ/RS - CEP 96400-970'),0,1,'C');
        $this->SetX(25); $this->Cell(160,4,$this->texto('FONE: (53) 3242-8422 - FAX: (53) 3242-9522 - E-MAIL: REGISTRO@ARCOOVINOS.COM.BR'),0,0,'C');
    }
}
