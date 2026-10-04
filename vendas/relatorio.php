<?
require_once __DIR__ . '/../includes/paginacao_vendas.php';
$nomeComprador = $_POST['comprador'] ?? $_GET['comprador'] ?? '';
$id_comprador = null;
$total = 0;
$sql = array();
$filtro = $_GET['filtro'] ?? '';
if (!$filtro) { 
  $filtro = $_POST['filtro'] ?? '';
}

if (!$filtro) {
  $filtro = "Data";
}

if ($filtro == 'Data') {
  $data_inicial = ($_POST['data_inicial'] ?? '');
  if (!$data_inicial) {
    $data_inicial = ($_GET['data_inicial'] ?? '');
  }

  if (!$data_inicial) {
    $data_inicial = date('01/m/Y');
  }

  $data_final = ($_POST['data_final'] ?? '');
  if (!$data_final) {
    $data_final = ($_GET['data_final'] ?? '');
  }

  if (!$data_final) {
    $data_final = date('t/m/Y');
  }
  
  $comprador = $nomeComprador;
  if ($comprador) {
    $comprador = DBEscape($comprador);
    $comprador = DBRead('mercado', "WHERE nome = '$comprador'");
    $id_comprador = $comprador[0]['id'] ?? null;
  }

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


  $condicaoVendas = "WHERE data >= '$data_inicial_' AND data <= '$data_final_'";
  if ($id_comprador) {
    $condicaoVendas .= " AND comprador = '" . (int)$id_comprador . "'";
  }
  $resumoVendas = DBRead('vendas', $condicaoVendas, 'COUNT(*) AS quantidade, COALESCE(SUM(preco_de_venda), 0) AS total')[0] ?? array();
  $total = (float)($resumoVendas['total'] ?? 0);
}


if ($filtro == 'Anual') {
  $ano = ($_POST['ano'] ?? '');
  if (!$ano) { 
    $ano = date('Y');
  }
  $data_inicial_ = $ano."-01-01";
  $data_final_ = $ano."-12-31";

  $sql = DBRead("vendas", "WHERE data >= '$data_inicial_' and data <= '$data_final_' ORDER BY data asc ");
  $venda_janeiro = $venda_fevereiro = $venda_marco = $venda_abril = $venda_maio = $venda_junho = $venda_julho = $venda_agosto = $venda_setembro = $venda_outubro = $venda_novembro = $venda_dezembro = 0;

  foreach (($sql ?: array()) as $vendas){
    list($ano, $mes, $dia) = explode('-', $vendas['data']);
    if ($mes == 01) {
       $venda_janeiro = $venda_janeiro + $vendas['preco_de_venda']; 
    }
    if ($mes == 02) {
       $venda_fevereiro = $venda_fevereiro + $vendas['preco_de_venda']; 
    }
    if ($mes == 03) {
       $venda_marco = $venda_marco + $vendas['preco_de_venda']; 
    }
    if ($mes == 04) {
       $venda_abril = $venda_abril + $vendas['preco_de_venda']; 
    }
    if ($mes == 05) {
       $venda_maio = $venda_maio + $vendas['preco_de_venda']; 
    }
    if ($mes == 06) {
       $venda_junho = $venda_junho + $vendas['preco_de_venda']; 
    }
    if ($mes == 07) {
       $venda_julho = $venda_julho + $vendas['preco_de_venda']; 
    }
    if ($mes == 8) {
        $venda_agosto = $venda_agosto + $vendas['preco_de_venda']; 
    }
    if ($mes == 9) {
        $venda_setembro = $venda_setembro + $vendas['preco_de_venda'];  
    }
    if ($mes == 10) {
       $venda_outubro = $venda_outubro + $vendas['preco_de_venda']; 
    }
    if ($mes == 11) {
       $venda_novembro = $venda_novembro + $vendas['preco_de_venda']; 
    }
    if ($mes == 12) {
       $venda_dezembro = $venda_dezembro + $vendas['preco_de_venda']; 
    }
  }
}

foreach(($sql ?: array()) as $linha){
  $total = $linha['preco_de_venda']+$total;
}

if ($filtro == 'Data') {
  $paginacao = paginacaoVendas((int)($resumoVendas['quantidade'] ?? 0), array(
    'pg' => 'relatorio_venda', 'filtro' => 'Data', 'data_inicial' => $data_inicial,
    'data_final' => $data_final, 'comprador' => $nomeComprador
  ));
  $vendasPagina = DBRead('vendas', $condicaoVendas . ' ORDER BY data ASC, id ASC LIMIT ' . $paginacao['inicio'] . ', ' . $paginacao['por_pagina']) ?: array();
}


?>

<script type="text/javascript">

function excluir_venda(botao){
  var id_animal = botao.getAttribute('data-id');
  if (!/^\d+$/.test(String(id_animal)) || Number(id_animal) < 1) { return; }
  confirmarExclusao({
    titulo: 'Excluir venda?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'Confirme se deseja excluir esta venda. Esta ação não pode ser desfeita. Os lançamentos financeiros vinculados ao animal serão removidos e ele retornará ao rebanho.',
    aoConfirmar: function () {
      window.location.href = 'vendas/_excluir_venda.php?tipo=1&id_animal=' + encodeURIComponent(id_animal);
    }
  });
}

function atualizar(filtro){
    window.location.href = "geral.php?pg=relatorio_venda&filtro="+filtro;
}

function abrir_venda(id_animal){
  window.open("geral.php?pg=animal&id_animal="+id_animal+"&aba=vender", '_blank');
}
</script>



<section class="content-header">
  <h1>
    Relatório de Vendas
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-shopping-cart"></i> Vendas</a></li>
    <li><a href="#">Relatório</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box" style="border-top:0;">
          <div class="box-body">
            <form method="post" action="geral.php?pg=relatorio_venda&amp;filtro=<?=$filtro?>">
              <input type="hidden" name="por_pagina" value="<?=$paginacao['por_pagina'] ?? 10?>">
              <div class="row">
                <div class="form-group col-sm-3 col-md-2">
                  <label for="filtro">Tipo</label>
                  <select class="form-control" onchange="atualizar(this.value)" name="filtro" id="filtro">
                    <option value="Data" <?=$filtro == 'Data' ? 'selected' : ''?>>Data</option>
                    <option value="Anual" <?=$filtro == 'Anual' ? 'selected' : ''?>>Anual</option>
                  </select>
                </div>
                <? if($filtro == 'Data'){ ?>
                <div class="form-group col-sm-3 col-md-2">
                  <label for="data_inicial">Data Inicial</label>
                  <div class="input-group date">
                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="data_inicial" name="data_inicial" value="<?=htmlspecialchars($data_inicial, ENT_QUOTES, 'UTF-8')?>">
                  </div>
                </div>
                <div class="form-group col-sm-3 col-md-2">
                  <label for="data_final">Data Final</label>
                  <div class="input-group date">
                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="data_final" name="data_final" value="<?=htmlspecialchars($data_final, ENT_QUOTES, 'UTF-8')?>">
                  </div>
                </div>
                <div class="form-group col-sm-3" style="position:relative;">
                  <label for="comprador">Pesquisar comprador</label>
                  <input type="text" class="form-control" id="comprador" name="comprador" value="<?=htmlspecialchars($nomeComprador, ENT_QUOTES, 'UTF-8')?>" onkeyup="pesquisar_comprador(this.value)" autocomplete="off">
                  <div id="lista_comprador" style="border:1px solid #bab1b4; position:absolute; z-index:99999; background:#fff; left:15px; right:15px; display:none;"></div>
                </div>
                <? } ?>
                <? if($filtro == 'Anual'){ ?>
                <div class="form-group col-sm-3 col-md-2">
                  <label for="ano">Ano</label>
                  <select class="form-control" name="ano" id="ano">
                    <? $anoSelecionado = $_POST['ano'] ?? date('Y'); ?>
                    <? for ($anoOpcao = max((int)date('Y'), (int)$anoSelecionado); $anoOpcao >= min((int)date('Y') - 6, (int)$anoSelecionado); $anoOpcao--) { ?>
                    <option value="<?=$anoOpcao?>" <?=$anoOpcao == $anoSelecionado ? 'selected' : ''?>><?=$anoOpcao?></option>
                    <? } ?>
                  </select>
                </div>
                <? } ?>
                <div class="form-group col-sm-6 col-md-3">
                  <label class="hidden-xs" aria-hidden="true">&nbsp;</label>
                  <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;">Pesquisar Extrato</button>
                    <a href="geral.php?pg=relatorio_venda" class="btn btn-default" style="flex:1;">Limpar</a>
                  </div>
                </div>
              </div>
            </form>
            <p class="help-block" style="margin-bottom:0;">Faturamento: <strong class="text-success">R$ <?=number_format($total,2,",",".")?></strong></p>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="box" style="border-top:0;">
          <div class="box-body">
            <? if($filtro == 'Data'){ ?>
              <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead><tr>
                  <th>Nº</th>
                  <th>Animal</th>
                  <th>Comprador</th>
                  <th>Data</th>
                  <th>Tipo de venda</th>
                  <th>Valor</th>
                  <th>Parcelas</th>
                  <th style="width:1%; white-space:nowrap;"><span class="sr-only">Ações</span></th>
                </tr></thead>
                <tbody>
                <? if (!$vendasPagina) { ?><tr><td colspan="8" class="text-center">Nenhuma venda encontrada para estes filtros.</td></tr><? } ?>

                <?
                $x=$paginacao['inicio'] + 1;
                foreach ($vendasPagina as $vendas){
                  $id_animal = $vendas['id_animal'];
                  $animal = DBRead('animais', "WHERE id = '$id_animal'");
                  $data = $vendas['data'];
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

                  $id_comprador = $vendas['comprador'];
                  $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");

                ?>
                  <tr>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$x?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$comprador[0]['nome']?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$data?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$vendas['tipo_venda']." - ".$vendas['forma_de_pagamento']?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" >R$ <?=number_format($vendas['preco_de_venda'],2,",",".");?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$vendas['parcelas']."x"?></td>
                      <td style="white-space:nowrap;">
                        <button type="button" class="text-primary" style="background:none; border:0; padding:0; margin-right:10px; cursor:pointer;" onclick="abrir_venda(<?=$id_animal?>)" title="Abrir venda" aria-label="Abrir venda"><i class="fa fa-search" aria-hidden="true"></i></button>
                        <button type="button" class="text-danger" style="background:none; border:0; padding:0; cursor:pointer;" data-id="<?=(int)$id_animal?>" data-nome="<?=htmlspecialchars($animal[0]['nome'], ENT_QUOTES, 'UTF-8')?>" onclick="excluir_venda(this)" title="Excluir venda" aria-label="Excluir venda"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                      </td>
                    </tr>
                  <? $x++; } ?>
                </tbody></table>
              </div>
              <?php renderPaginacaoVendas($paginacao); ?>

            <? } ?>

            <? if($filtro == 'Anual'){ ?>
              <div class="col-md-12">
                    <div class="box-header with-border">
                      <h3 class="box-title">Gráfico anual</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <div class="box-body chart-responsive">
                      <div class="chart" id="bar-chart3" style="height: 550px;"></div>
                    </div>
                    <!-- /.box-body -->


              <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead><tr>
                  <th>Jan</th>
                  <th>Fev</th>
                  <th>Mar</th>
                  <th>Abr</th>
                  <th>Mai</th>
                  <th>Jun</th>
                  <th>Jul</th>
                  <th>Ago</th>
                  <th>Set</th>
                  <th>Out</th>
                  <th>Nov</th>
                  <th>Dez</th>
                </tr></thead>
                <tbody>
                  <tr>
                      <td>R$ <?=number_format($venda_janeiro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_fevereiro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_marco,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_abril,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_maio,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_junho,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_julho,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_agosto,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_setembro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_outubro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_novembro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_dezembro,2,",",".");?></td>
                    </tr>
                </tbody></table>
              </div>
          </div>
        <? } ?>

      </div>
    </div>
  </div>
  </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
