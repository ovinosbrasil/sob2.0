<?php require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/../includes/paginacao_financeiro.php';
$hFinanceiro = function ($valor) {
  $texto = (string)$valor;
  // Conexões legadas retornam Latin-1; vendas de materiais usam UTF-8.
  if (!preg_match('//u', $texto)) {
    $texto = iconv('Windows-1252', 'UTF-8', $texto);
  }
  return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$data_inicial = ($_POST['data_inicial'] ?? ''); if(!$data_inicial){ $data_inicial = ($_GET['data_inicial'] ?? ''); } if(!$data_inicial){ $data_inicial = date('01/01/Y'); }
$data_final = ($_POST['data_final'] ?? ''); if(!$data_final){ $data_final = ($_GET['data_final'] ?? ''); } if(!$data_final){ $data_final = date('31/12/Y'); }
$status = $_POST['status_filtro'] ?? $_GET['status_filtro'] ?? 'Todas';
$tipo = $_POST['tipo_filtro'] ?? $_GET['tipo_filtro'] ?? 'Todas';
$categoria_filtro = $_POST['categoria_filtro'] ?? $_GET['categoria_filtro'] ?? '';
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



$sql = DBRead("controle_financeiro", "WHERE data >= '$data_inicial_' and data <= '$data_final_'  ORDER BY data asc, id asc ");

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
$paginacaoFinanceiro = paginacaoFinanceiro(count($sql), array(
  'pg' => 'financeiro', 'data_inicial' => $data_inicial, 'data_final' => $data_final,
  'tipo_filtro' => $tipo, 'status_filtro' => $status, 'categoria_filtro' => $categoria_filtro
));
$lancamentosPagina = array_slice($sql, $paginacaoFinanceiro['inicio'], $paginacaoFinanceiro['por_pagina']);
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
  pedirConfirmacaoFinanceiro('confirmar', id_financeiro);
}

function cancelar(id_financeiro){
  pedirConfirmacaoFinanceiro('cancelar', id_financeiro);
}

function abrir_excluir(id_financeiro){
  pedirConfirmacaoFinanceiro('excluir', id_financeiro);
}

function excluir(id_financeiro){
  pedirConfirmacaoFinanceiro('excluir', id_financeiro);
}

function fechar_excluir(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function faturamento(){
  jQuery('#palco_faturamento').modal('show');
}

function fechar_faturamento(){
  jQuery('#palco_faturamento').modal('hide');
}

function debito(){
  jQuery('#palco_debito').modal('show');
}

function fechar_debito(){
  jQuery('#palco_debito').modal('hide');
}

function alterar(id_financeiro){
  var requisicao = new XMLHttpRequest();
  requisicao.open('GET', 'financeiro/palco_alterar.php?id_financeiro=' + encodeURIComponent(id_financeiro), true);
  requisicao.onload = function () {
    if (requisicao.status !== 200) {
      SobAlertas.erro({mensagem: 'Não foi possível carregar o lançamento. Atualize a página e tente novamente.'});
      return;
    }
    document.getElementById('financeiro-alterar-conteudo').innerHTML = requisicao.responseText;
    document.dispatchEvent(new Event('financeiro:edicao-carregada'));
    if (jQuery.fn.priceFormat) jQuery('#valor_alterar').priceFormat();
    jQuery('#palco_alterar').modal('show');
  };
  requisicao.onerror = function () { SobAlertas.erro({mensagem: 'Não foi possível carregar o lançamento. Tente novamente.'}); };
  requisicao.send();
}

function fechar_alterar(){
  jQuery('#palco_alterar').modal('hide');
}
</script>


<div class="row" id="palco_excluir" style=" z-index:9999999999;  left:40%; top:5%;  position:absolute; position:fixed;"></div>
<div class="modal fade financeiro-cadastro-modal" id="palco_alterar" tabindex="-1" role="dialog" aria-labelledby="titulo-alterar-financeiro">
  <div class="modal-dialog" role="document"><div class="modal-content" id="financeiro-alterar-conteudo"></div></div>
</div>
<? foreach (array('debito' => 'despesa', 'faturamento' => 'receita') as $cadastro => $rotulo) { ?>
<div class="modal fade financeiro-cadastro-modal" id="palco_<?=$cadastro?>" tabindex="-1" role="dialog" aria-labelledby="titulo-<?=$cadastro?>">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post" action="financeiro/_<?=$cadastro?>.php" data-alertas-financeiro="<?=$cadastro?>" novalidate>
        <?=campoCsrfSob()?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="titulo-<?=$cadastro?>">Cadastrar <?=$rotulo?></h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="descricao_<?=$cadastro?>">Descrição <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="descricao_<?=$cadastro?>" name="descricao_<?=$cadastro?>" placeholder="Digite a descrição" required>
          </div>
          <div class="row">
            <div class="col-sm-6 form-group">
              <label for="data_<?=$cadastro?>">Vencimento <span class="text-danger">*</span></label>
              <div class="input-group date">
                <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                <input type="text" class="form-control" id="data_<?=$cadastro?>" name="data_<?=$cadastro?>" value="<?=date('d/m/Y')?>" required>
              </div>
            </div>
            <div class="col-sm-6 form-group">
              <label for="valor_<?=$cadastro?>">Valor total <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-addon">R$</span>
                <input type="text" class="form-control" id="valor_<?=$cadastro?>" name="valor_<?=$cadastro?>" placeholder="0,00" inputmode="decimal" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 form-group">
              <label for="forma_<?=$cadastro?>">Forma de pagamento <span class="text-danger">*</span></label>
              <select class="form-control" id="forma_<?=$cadastro?>" name="forma_<?=$cadastro?>" required>
                <option value="">Selecionar forma</option>
                <? foreach (array('Pix', 'Boleto', 'Dinheiro', 'Depósito', 'Cartão', 'Transação bancária') as $forma_cadastro) { ?>
                <option value="<?=$forma_cadastro?>"><?=$forma_cadastro?></option>
                <? } ?>
              </select>
            </div>
            <div class="col-sm-6 form-group">
              <label for="parcelas_<?=$cadastro?>">Parcelas</label>
              <select class="form-control" id="parcelas_<?=$cadastro?>" name="parcelas_<?=$cadastro?>">
                <? for ($parcela_cadastro = 1; $parcela_cadastro <= 10; $parcela_cadastro++) { ?>
                <option value="<?=$parcela_cadastro?>"><?=$parcela_cadastro?>x</option>
                <? } ?>
              </select>
              <small class="help-block">O valor total será dividido entre os meses.</small>
            </div>
          </div>
          <? if ($cadastro === 'debito') { ?>
          <div class="form-group">
            <label for="categoria_debito">Categoria <span class="text-danger">*</span></label>
            <select class="form-control" id="categoria_debito" name="categoria_debito" required>
              <option value="">Selecionar categoria</option>
              <? foreach (array('Custos fixos (Água, luz, telefone)', 'Infraestrutura', 'Pagamento Profissional', 'Encargos de funcionários', 'Contabilidade', 'Marketing', 'Outros') as $categoria_cadastro) { ?>
              <option value="<?=$categoria_cadastro?>"><?=$categoria_cadastro?></option>
              <? } ?>
            </select>
          </div>
          <? } ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Cadastrar <?=$rotulo?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<? } ?>

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
        <button type="button" class="btn btn-success" id="abrir-novo-lancamento" data-toggle="modal" data-target="#novo-lancamento-modal"><i class="fa fa-plus" aria-hidden="true"></i> Novo lançamento</button>
      </div>
      <div class="box-body">
        <form method="post" action="geral.php?pg=financeiro">
        <?=campoCsrfSob()?>
          <input type="hidden" name="por_pagina" value="<?=$paginacaoFinanceiro['por_pagina']?>">
          <div class="row">
            <div class="form-group col-sm-6 col-md-2">
              <label for="data_inicial">Data Inicial</label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                <input type="text" class="form-control" id="data_inicial" name="data_inicial" value="<?=$hFinanceiro($data_inicial)?>">
              </div>
            </div>
            <div class="form-group col-sm-6 col-md-2">
              <label for="data_final">Data Final</label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                <input type="text" class="form-control" id="data_final" name="data_final" value="<?=$hFinanceiro($data_final)?>">
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
                <option value="<?=$hFinanceiro($categoria)?>" <?=$categoria_filtro === $categoria ? 'selected' : ''?>><?=$hFinanceiro($categoria)?></option>
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
                <th>Comprador / Categoria</th>
                <th>Valor</th>
                <th>Pagamento</th>
                <th>Tipo</th>
                <th>Status</th>
                <th style="width:1%; white-space:nowrap;"><span class="sr-only">Ações</span></th>
              </tr></thead>
              <tbody>
							<?
              $quantidade_lancamentos = 0;
              foreach($lancamentosPagina as $linha){
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
                <tr data-financeiro-id="<?=(int)$linha['id']?>" data-financeiro-nome="<?=$hFinanceiro($linha['titulo'])?>">
                  <td><?=$data?></td>
                  <td><?=$hFinanceiro($linha['titulo'])?></td>
                  <td><?=$hFinanceiro(($linha['tipo'] == 1 ? ($linha['categoria'] ?? '') : ($comprador[0]['nome'] ?? '')) ?: '—')?></td>
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
                <tr><td colspan="8" class="text-center text-muted">Nenhum lançamento encontrado para os filtros selecionados.</td></tr>
              <? } ?>
              </tbody>
            </table>
        </div>
      </div>
      <?php renderPaginacaoFinanceiro($paginacaoFinanceiro); ?>
    </div>
  </section>

<div class="modal fade financeiro-novo-modal" id="novo-lancamento-modal" tabindex="-1" role="dialog" aria-labelledby="novo-lancamento-titulo" aria-describedby="novo-lancamento-descricao">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="novo-lancamento-titulo">Novo lançamento</h4>
      </div>
      <div class="modal-body">
        <p class="text-muted" id="novo-lancamento-descricao">Selecione o tipo de lançamento que deseja cadastrar.</p>
        <div class="financeiro-tipos">
          <button type="button" class="financeiro-tipo" data-tipo-lancamento="despesa">
            <i class="fa fa-arrow-circle-up text-danger" aria-hidden="true"></i>
            <strong>Despesa</strong><span class="text-muted">Registrar uma conta a pagar</span>
          </button>
          <button type="button" class="financeiro-tipo" data-tipo-lancamento="receita">
            <i class="fa fa-arrow-circle-down text-success" aria-hidden="true"></i>
            <strong>Receita</strong><span class="text-muted">Registrar um valor a receber</span>
          </button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('.financeiro-cadastro-modal').on('shown.bs.modal', function () {
    jQuery(this).find('input').first().focus();
  }).on('hidden.bs.modal', function () {
    document.getElementById('abrir-novo-lancamento').focus();
  });
  var modal = jQuery('#novo-lancamento-modal');
  var tipoSelecionado = null;
  modal.on('show.bs.modal', function () { tipoSelecionado = null; });
  modal.on('shown.bs.modal', function () {
    modal.find('[data-tipo-lancamento]').first().focus();
  });
  modal.find('[data-tipo-lancamento]').on('click', function () {
    tipoSelecionado = this.getAttribute('data-tipo-lancamento');
    modal.modal('hide');
  });
  modal.on('hidden.bs.modal', function () {
    if (tipoSelecionado === 'despesa') { debito(); }
    if (tipoSelecionado === 'receita') { faturamento(); }
    tipoSelecionado = null;
  });
});
</script>

<div class="modal fade sob-confirmacao-exclusao" id="financeiro-confirmacao-modal" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="financeiro-confirmacao-titulo" aria-describedby="financeiro-confirmacao-descricao">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <button type="button" class="fechar-confirmacao" data-dismiss="modal" aria-label="Fechar">&times;</button>
      <div class="icone-confirmacao" id="financeiro-confirmacao-icone" aria-hidden="true"><i class="fa fa-trash-o" id="financeiro-confirmacao-simbolo"></i></div>
      <h3 id="financeiro-confirmacao-titulo"></h3>
      <p id="financeiro-confirmacao-descricao"></p>
      <div class="animal-confirmacao"><strong id="financeiro-confirmacao-nome"></strong></div>
      <div class="acoes-confirmacao">
        <button type="button" class="btn btn-default" id="financeiro-confirmacao-cancelar" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn" id="financeiro-confirmacao-prosseguir"></button>
      </div>
    </div>
  </div>
</div>
<script src="dist/js/confirmacao-financeiro.js?v=<?=filemtime(__DIR__ . '/../dist/js/confirmacao-financeiro.js')?>"></script>

<form id="financeiro-acao-form" method="post" hidden>
  <?=campoCsrfSob()?>
  <input type="hidden" name="id_financeiro">
  <input type="hidden" name="data_inicial">
  <input type="hidden" name="data_final">
</form>
