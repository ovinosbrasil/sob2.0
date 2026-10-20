<?
require_once __DIR__ . '/../_config.php';

$data_inicial = ($_POST['data_inicial'] ?? ''); if(!$data_inicial){ $data_inicial = ($_GET['data_inicial'] ?? ''); } if(!$data_inicial){ $data_inicial = date('01/01/Y'); }
$data_final = ($_POST['data_final'] ?? ''); if(!$data_final){ $data_final = ($_GET['data_final'] ?? ''); } if(!$data_final){ $data_final = date('31/12/Y'); }
$status = $_POST['status_filtro'] ?? 'Todas';
$tipo = $_POST['tipo_filtro'] ?? 'Todas';
$categoria_filtro = $_POST['categoria_filtro'] ?? '';
$receber = $receber_ = $pagar = $pagar_ = 0;

$data = $data_inicial;
$data_atual = $data;
$data = '0';
$data['0'] = $data_atual['6'];
$data['1'] = $data_atual['7'];
$data['2'] = $data_atual['8'];
$data['3'] = $data_atual['9'];
$data['4'] = "-";
$data['5'] = $data_atual['3'];
$data['6'] = $data_atual['4'];
$data['7'] = "-";
$data['8'] = $data_atual['0'];
$data['9'] = $data_atual['1'];
$data_inicial_ = $data;


$data = $data_final;
$data_atual = $data;
$data = '0';
$data['0'] = $data_atual['6'];
$data['1'] = $data_atual['7'];
$data['2'] = $data_atual['8'];
$data['3'] = $data_atual['9'];
$data['4'] = "-";
$data['5'] = $data_atual['3'];
$data['6'] = $data_atual['4'];
$data['7'] = "-";
$data['8'] = $data_atual['0'];
$data['9'] = $data_atual['1'];
$data_final_ = $data;



$sql = DBRead("controle_financeiro", "WHERE data >= '$data_inicial_' and data <= '$data_final_'  ORDER BY data asc ");

$categorias = array();
foreach (($sql ?: array()) as $registro) {
  if (!empty($registro['categoria'])) { $categorias[$registro['categoria']] = $registro['categoria']; }
}
sort($categorias);
$sql = array_values(array_filter($sql ?: array(), function ($registro) use ($tipo, $status, $categoria_filtro) {
  return (($tipo !== '0' && $tipo !== '1') || (string)$registro['tipo'] === $tipo)
    && ($status !== 'Confirmado' || (bool)$registro['status'])
    && ($status !== 'Aguardando' || !$registro['status'])
    && ($categoria_filtro === '' || ($registro['categoria'] ?? '') === $categoria_filtro);
}));
$meses_grafico = array();
$inicio_grafico = new DateTime(substr($data_inicial_, 0, 7) . '-01');
$fim_grafico = new DateTime(substr($data_final_, 0, 7) . '-01');
for ($mes = clone $inicio_grafico; $mes <= $fim_grafico; $mes->modify('+1 month')) {
  $meses_grafico[$mes->format('Y-m')] = array('receitas' => 0, 'despesas' => 0);
}
foreach ($sql as $registro) {
  $chave = substr($registro['data'], 0, 7);
  if (isset($meses_grafico[$chave])) {
    $serie = $registro['tipo'] ? 'despesas' : 'receitas';
    $meses_grafico[$chave][$serie] += (float)$registro['valor'];
  }
}

foreach(($sql ?: array()) as $linha){
  if((!$linha['tipo']) && ($linha['status']) ){
    $receber = $receber+$linha['valor'];
  }

  if((!$linha['tipo']) && (!$linha['status']) ){
    $receber_ = $receber_+$linha['valor'];
  }

  if(($linha['tipo']) && ($linha['status']) ){ $pagar = $pagar+$linha['valor']; }
  if(($linha['tipo']) && (!$linha['status']) ){ $pagar_ = $pagar_+$linha['valor']; }
}


?>

<script type="text/javascript">
function atualizar_financeiro(tipo,id_financeiro){
  if(tipo === ""){ return; }
  if(tipo == 0){ confirmar(id_financeiro); }
  if(tipo == 1){ cancelar(id_financeiro); }
  if(tipo == 2){ alterar(id_financeiro); }
  if(tipo == 3){ abrir_excluir(id_financeiro); }
}


function confirmar(id_financeiro){
		data_inicial = document.getElementById("data_inicial").value;
		data_final = document.getElementById("data_final").value;
    window.location.href = "financeiro/_confirmar.php?id_financeiro="+id_financeiro+"&data_inicial="+data_inicial+"&data_final="+data_final;
}

function cancelar(id_financeiro){
		data_inicial = document.getElementById("data_inicial").value;
		data_final = document.getElementById("data_final").value;
    window.location.href = "financeiro/_cancelar.php?id_financeiro="+id_financeiro+"&data_inicial="+data_inicial+"&data_final="+data_final;
}

function abrir_excluir(id_financeiro){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
 var url = "financeiro/palco_excluir.php?id_financeiro="+id_financeiro;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_excluir").innerHTML = resposta; document.getElementById("palco_excluir").style.display = 'block';
}
}
PP.send(null);
document.getElementById("transparencia").style.display = 'block';
}

function excluir(id_financeiro){
		data_inicial = document.getElementById("data_inicial").value;
		data_final = document.getElementById("data_final").value;
    window.location.href = "financeiro/_excluir.php?id_financeiro="+id_financeiro+"&data_inicial="+data_inicial+"&data_final="+data_final;
}


function fechar_excluir(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function faturamento(){
	document.getElementById("transparencia").style.display = 'block';
	document.getElementById("palco_faturamento").style.display = 'block';
}

function fechar_faturamento(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_faturamento").style.display = 'none';
}

function debito(){
	document.getElementById("transparencia").style.display = 'block';
	document.getElementById("palco_debito").style.display = 'block';
}

function fechar_debito(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_debito").style.display = 'none';
}

function alterar(id_financeiro){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "financeiro/palco_alterar.php?id_financeiro="+id_financeiro;

// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_alterar").innerHTML = resposta; document.getElementById("palco_alterar").style.display = 'block';
}
}
PP.send(null);
document.getElementById("transparencia").style.display = 'block';
}

function fechar_alterar(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_alterar").style.display = 'none';
}
</script>


<div class="row" id="palco_excluir" style=" z-index:9999999999;  left:40%; top:5%;  position:absolute; position:fixed;"></div>
<div class="row" id="palco_alterar" style=" z-index:999999999;  left:20%; top:5%;  position:absolute; position:fixed; display:none;"></div>
<div class="row" id="palco_faturamento" style=" z-index:999999999999;  left:20%; top:5%; position:absolute; position:fixed; display:none;">
    <!-- left column -->
    <div class="col-md-9">
      <!-- general form elements -->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Cadastrar faturamento</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form method="post" action="financeiro/_faturamento.php">
        <div class="box-body">
          <div class="col-md-4 ">
            <div class="form-group">
              <label for="exampleInputEmail1">Descrição<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="descricao_faturamento" name="descricao_faturamento" placeholder="Digite aqui...">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Forma de pagamento<span style="color:#F00;">*</span></label>
                <select class="form-control select" name="forma_faturamento" id="forma_faturamento">
                  <option value="">Selecionar forma</option>
                  <option value=""></option>
                  <option value="Boleto">Boleto</option>
                  <option value="Dinheiro">Dinheiro</option>
                  <option value="Depósito">Depósito</option>
                  <option value="Cartão">Cartão</option>
                  <option value="Transação bancária">Transação bancária</option>
                </select>
            </div>
          </div>

          <div class="col-md-4 " style="margin-left:-12px;">
            <div class="form-group">
                <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_faturamento" name="data_faturamento" value="<?=date('d/m/Y');?>">
                </div>
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Repetir parcelas</label>
              <select class="form-control select" name="parcelas_faturamento" id="parcelas_faturamento">
                <option value="1">1x</option>
                <option value=""></option>
                <option value="2">2x</option>
                <option value="3">3x</option>
                <option value="4">4x</option>
                <option value="5">5x</option>
                <option value="6">6x</option>
                <option value="7">7x</option>
                <option value="8">8x</option>
                <option value="9">9x</option>
                <option value="10">10x</option>
              </select>
            </div>

        </div>

      <div class="col-md-4">
        <div class="form-group">
          <label for="exampleInputPassword1">Valor<span style="color:#F00;">*</span></label>
          <input type="text" id="valor_faturamento" name="valor_faturamento" class="form-control" placeholder="R$ 00,00">
        </div>


      </div>
      <button type="submit" class="btn btn-success" style="margin-left:1.5%;">Cadastrar Faturamento</button>
    </form>
    <button type="button" class="btn btn-danger" style="float:right; margin-right:2.5%;" onclick="fechar_faturamento()">Fechar</button>
  </div></div></div></div>

<div class="row" id="palco_debito" style=" z-index:99999999;  left:20%; top:5%;  position:absolute; position:fixed; display:none;">
      <!-- left column -->
      <div class="col-md-9">
        <!-- general form elements -->
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Cadastrar Débito</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <form method="post" action="financeiro/_debito.php">
          <div class="box-body">
            <div class="col-md-4 ">
              <div class="form-group">
                <label for="exampleInputEmail1">Descrição<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="descricao_debito" name="descricao_debito" placeholder="Digite aqui...">
              </div>

              <div class="form-group">
                  <label for="exampleInputPassword1">Forma de pagamento<span style="color:#F00;">*</span></label>
                  <select class="form-control select" name="forma_debito" id="forma_debito">
                    <option value="">Selecionar forma</option>
                    <option value=""></option>
                    <option value="Boleto">Boleto</option>
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão">Cartão</option>
                    <option value="Transação bancária">Transação bancária</option>
                  </select>
              </div>
            </div>

            <div class="col-md-4 " style="margin-left:-12px;">
              <div class="form-group">
                  <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="data_debito" name="data_debito" value="<?=date('d/m/Y');?>">
                  </div>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Categoria<span style="color:#F00;">*</span></label>
                <select class="form-control select" name="categoria_debito" id="categoria_debito">
                  <option value="">Selecionar</option>
                  <option value=""></option>
                  <option value="Custos fixos (Água, luz, telefone)">Custos fixos (Água, luz, telefone)</option>
                  <option value="Infraestrutura">Infraestrutura</option>
                  <option value="Pagamento Profissional">Pagamento Profissional</option>
                  <option value="Encargos de funcionários">Encargos de funcionários</option>
                  <option value="Contabilidade">Contabilidade</option>
                  <option value="Marketing">Marketing</option>
                  <option value="Outros">Outros</option>
                </select>
              </div>

          </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="exampleInputPassword1">Valor<span style="color:#F00;">*</span></label>
            <input type="text" id="valor_debito" name="valor_debito" class="form-control" placeholder="R$ 00,00">
          </div>

          <div class="form-group">
            <label for="exampleInputPassword1">Parcelas</label>
            <select class="form-control select" name="parcelas_debito" id="parcelas_debito">
              <option value="1">1x</option>
              <option value=""></option>
              <option value="2">2x</option>
              <option value="3">3x</option>
              <option value="4">4x</option>
              <option value="5">5x</option>
              <option value="6">6x</option>
              <option value="7">7x</option>
              <option value="8">8x</option>
              <option value="9">9x</option>
              <option value="10">10x</option>
            </select>
        </div>

      </div>
    <button type="submit" class="btn btn-success" style="margin-left:1.5%;">Cadastrar Débito</button>
  </form>
<button type="button" class="btn btn-danger" style="float:right; margin-right:2.5%;" onclick="fechar_debito()">Fechar</button>
</div></div></div></div>



<link rel="stylesheet" href="dist/css/financeiro.css?v=<?=filemtime(__DIR__ . '/../dist/css/financeiro.css')?>">

<section class="content-header">
  <h1>
    Extrato financeiro
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-money"></i> Extrato financeiro</a></li>
    <li><a href="#">Lista</a></li>
  </ol>
</section>

  <section class="content financeiro-content">
    <div class="row financeiro-resumo" aria-label="Resumo financeiro do período filtrado">
      <? foreach (array(
          array('Receitas (pagas)', $receber, 'text-success', $receber_),
          array('Despesas (pagas)', $pagar, 'text-danger', $pagar_),
          array('Saldo', $receber - $pagar, $receber >= $pagar ? 'text-success' : 'text-danger', null)
      ) as $resumo) { ?>
      <div class="col-sm-4">
        <div class="box financeiro-card"><div class="box-body">
          <span class="text-muted financeiro-caption"><?=$resumo[0]?></span>
          <strong class="financeiro-total <?=$resumo[2]?>">R$ <?=number_format($resumo[1], 2, ',', '.')?></strong>
          <? if ($resumo[3] !== null) { ?><small class="text-muted">Pendentes: R$ <?=number_format($resumo[3], 2, ',', '.')?></small><? } else { ?><small class="text-muted">Receitas pagas − despesas pagas</small><? } ?>
        </div></div>
      </div>
      <? } ?>
    </div>
    <div class="box financeiro-chart-panel">
      <div class="box-header"><h2 class="box-title">Receitas e despesas por mês</h2><p class="help-block">Valores pagos e pendentes do período filtrado</p></div>
      <div class="box-body">
        <div class="financeiro-legend"><span><i class="financeiro-dot receita"></i> Receitas</span><span><i class="financeiro-dot despesa"></i> Despesas</span></div>
        <div class="financeiro-chart-scroll">
          <?
          $maximo = 1;
          foreach ($meses_grafico as $valores) { $maximo = max($maximo, $valores['receitas'], $valores['despesas']); }
          $largura = max(760, count($meses_grafico) * 85 + 90);
          $passo = ($largura - 100) / max(1, count($meses_grafico));
          $nomes_meses = array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
          ?>
          <svg class="financeiro-chart" viewBox="0 0 <?=$largura?> 240" style="min-width:<?=$largura?>px" role="img" aria-labelledby="financeiro-chart-title">
            <title id="financeiro-chart-title">Receitas e despesas mensais em reais, incluindo valores pagos e pendentes</title>
            <? for ($i = 0; $i <= 4; $i++) { $y = 20 + $i * 45; ?>
            <line x1="80" y1="<?=$y?>" x2="<?=$largura - 10?>" y2="<?=$y?>" stroke="#e7ecef" />
            <text x="72" y="<?=$y + 4?>" text-anchor="end"><?=number_format($maximo * (4 - $i) / 4, 2, ',', '.')?></text>
            <? } ?>
            <? $indice = 0; foreach ($meses_grafico as $chave => $valores) { $x = 85 + $indice++ * $passo; ?>
              <? foreach (array('receitas', 'despesas') as $serie_indice => $serie) { $altura = $valores[$serie] / $maximo * 180; ?>
              <rect x="<?=$x + $passo * (.18 + $serie_indice * .30)?>" y="<?=200 - $altura?>" width="<?=$passo * .27?>" height="<?=$altura?>" class="<?=$serie?>"><title><?=$chave?> — <?=ucfirst($serie)?>: R$ <?=number_format($valores[$serie], 2, ',', '.')?></title></rect>
              <? } ?>
              <text x="<?=$x + $passo * .48?>" y="225" text-anchor="middle"><?=$nomes_meses[(int)substr($chave, 5, 2) - 1]?>/<?=substr($chave, 2, 2)?></text>
            <? } ?>
          </svg>
        </div>
        <? if (!$sql) { ?><p class="text-center text-muted">Nenhum lançamento no período filtrado.</p><? } ?>
      </div>
    </div>
    <div class="box" style="border-top:0;">
      <div class="box-header with-border financeiro-toolbar">
        <h2 class="box-title">Lançamentos</h2>
        <div class="dropdown">
          <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-plus" aria-hidden="true"></i> Novo lançamento <span class="caret"></span></button>
          <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="#" onclick="faturamento(); return false;">Adicionar receita</a></li>
            <li><a href="#" onclick="debito(); return false;">Adicionar despesa</a></li>
          </ul>
        </div>
      </div>
      <div class="box-body">
        <form method="post" action="geral.php?pg=financeiro">
          <div class="row">
            <div class="form-group col-sm-6 col-md-2">
              <label for="data_inicial">Data Inicial</label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                <input type="text" class="form-control" id="data_inicial" name="data_inicial" value="<?=htmlspecialchars($data_inicial, ENT_QUOTES, 'UTF-8')?>">
              </div>
            </div>
            <div class="form-group col-sm-6 col-md-2">
              <label for="data_final">Data Final</label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                <input type="text" class="form-control" id="data_final" name="data_final" value="<?=htmlspecialchars($data_final, ENT_QUOTES, 'UTF-8')?>">
              </div>
            </div>
            <div class="form-group col-sm-6 col-md-2">
              <label for="tipo_filtro">Tipo</label>
              <select class="form-control" name="tipo_filtro" id="tipo_filtro">
                <option value="Todas">Todos</option>
                <option value="0" <?=$tipo === '0' ? 'selected' : ''?>>Receita</option>
                <option value="1" <?=$tipo === '1' ? 'selected' : ''?>>Despesa</option>
              </select>
            </div>
            <div class="form-group col-sm-6 col-md-2">
              <label for="status_filtro">Status</label>
              <select class="form-control" name="status_filtro" id="status_filtro">
                <option value="Todas">Todos</option>
                <option value="Confirmado" <?=$status == 'Confirmado' ? 'selected' : ''?>>Confirmado</option>
                <option value="Aguardando" <?=$status == 'Aguardando' ? 'selected' : ''?>>Pendente</option>
              </select>
            </div>
            <div class="form-group col-sm-6 col-md-2">
              <label for="categoria_filtro">Categoria</label>
              <select class="form-control" id="categoria_filtro" name="categoria_filtro">
                <option value="">Todas</option>
                <? foreach ($categorias as $categoria) { ?>
                <option value="<?=htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8')?>" <?=$categoria_filtro === $categoria ? 'selected' : ''?>><?=htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8')?></option>
                <? } ?>
              </select>
            </div>
            <div class="form-group col-sm-6 col-md-2">
              <label class="hidden-xs hidden-sm" aria-hidden="true">&nbsp;</label>
              <div class="financeiro-filter-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Pesquisar</button>
                <a href="geral.php?pg=financeiro" class="btn btn-default">Limpar</a>
              </div>
            </div>
          </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped financeiro-table">
              <thead>
              <tr>
                <th>Vencimento</th>
                <th>Descrição</th>
                <th>Comprador</th>
                <th>Categoria</th>
                <th>Valor</th>
                <th>Pagamento</th>
                <th>Tipo</th>
                <th>Status</th>
                <th style="width:1%; white-space:nowrap;"><span class="sr-only">Ações</span></th>
              </tr></thead>
              <tbody>
							<?
              $quantidade_lancamentos = 0;
              foreach(($sql ?: array()) as $linha){
                $comprador = $venda = array();
                $id_animal = $linha['id_animal'];
                if($id_animal){
                $venda = DBRead('vendas', "WHERE id_animal = '$id_animal'");
                $id_comprador = $venda[0]['comprador'];
                if($id_comprador){
                    $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
                }
                }

                if(($linha['tipo'] == 0) && ($linha['id_animal'] == 0)){
                  $id_comprador = $linha['id_comprador'];
                  $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
                }


                $data = $linha['data'];
                $data_atual = $data;
                $data = '0';
                $data['0'] = $data_atual['8'];
                $data['1'] = $data_atual['9'];
                $data['2'] = "/";
                $data['3'] = $data_atual['5'];
                $data['4'] = $data_atual['6'];
                $data['5'] = "/";
                $data['6'] = $data_atual['0'];
                $data['7'] = $data_atual['1'];
                $data['8'] = $data_atual['2'];
                $data['9'] = $data_atual['3'];

                $quantidade_lancamentos++;
                ?>
                <tr>
                  <td><?=$data?></td>
                  <td><?=htmlspecialchars($linha['titulo'], ENT_QUOTES, 'UTF-8')?></td>
                  <td>
                  <?
                  if(($linha['tipo'] == 0) || ($linha['tipo'] == 2) ){
                    echo htmlspecialchars($comprador[0]['nome'] ?? '', ENT_QUOTES, 'UTF-8');
                  }

                  ?>
                </td>
                  <td><?=htmlspecialchars($linha['categoria'] ?? '—', ENT_QUOTES, 'UTF-8')?></td>
                  <td class="financeiro-valor <?=$linha['tipo'] == 1 ? 'text-danger' : 'text-success'?>">R$ <? if($linha['tipo'] == 1){ echo "- ";} ?> <?=number_format($linha['valor'],2,",",".");?></td>
                  <td><?
                  if(($linha['tipo'] == 0) && ($linha['id_animal'] > 1)){
                    echo $venda[0]['tipo_venda']," - ", $linha['forma_de_pagamento'];
                  }

                  if($linha['tipo'] == 1){
                    echo $linha['forma_de_pagamento'];
                  }

                  if(($linha['tipo'] == 0) && ($linha['id_animal'] == 0)){
                    echo $linha['forma_de_pagamento'];
                  }
                  ?></td>
                  <td><span class="financeiro-badge <?=$linha['tipo'] ? 'despesa' : 'receita'?>"><?=$linha['tipo'] ? 'Despesa' : 'Receita'?></span></td>
                  <td><span class="label <?=$linha['status'] ? 'label-success' : 'financeiro-pendente'?>"><?=$linha['status'] ? 'Confirmado' : 'Pendente'?></span></td>
                  <td class="financeiro-acoes">
                    <? foreach (array(array('confirmar', 'Confirmar', 'check', 'success'), array('cancelar', 'Cancelar', 'undo', 'warning'), array('alterar', 'Abrir registro', 'search', 'primary'), array('abrir_excluir', 'Excluir', 'trash-o', 'danger')) as $acao) { ?>
                    <button type="button" class="text-<?=$acao[3]?>" onclick="<?=$acao[0]?>(<?=(int)$linha['id']?>)" title="<?=$acao[1]?>" aria-label="<?=$acao[1]?> lançamento"><i class="fa fa-<?=$acao[2]?>" aria-hidden="true"></i></button>
                    <? } ?>
                  </td>
                </tr>
              <? } ?>
              <? if (!$quantidade_lancamentos) { ?>
                <tr><td colspan="9" class="text-center text-muted">Nenhum lançamento encontrado para os filtros selecionados.</td></tr>
              <? } ?>
              </tbody>
            </table>
        </div>
      </div>
    </div>
  </section>
