<?php
function dadosCompradorH($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
if (!empty($_POST['comprador'])) {
    $nomeBusca = DBEscape($_POST['comprador']);
    $comprador = DBRead('mercado', "WHERE nome = '$nomeBusca'");
    $id_comprador = (int)($comprador[0]['id'] ?? 0);
} else {
    $id_comprador = (int)($_GET['id_comprador'] ?? 0);
    $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
}
if (!$comprador) {
    echo '<section class="content"><div class="alert alert-warning">Comprador não encontrado.</div></section>';
    return;
}
$cadastro = $comprador[0];
$filtro = (int)($_GET['filtro'] ?? 0);
$ordem = array(0 => 'data ASC', 1 => 'data DESC', 2 => 'preco_de_venda DESC');
$vendas = DBRead('vendas', "WHERE comprador = '$id_comprador' ORDER BY " . ($ordem[$filtro] ?? $ordem[0])) ?: array();
$total = 0;
$ultimaVenda = '';
foreach ($vendas as $venda) {
    $total += (float)$venda['preco_de_venda'];
    if ($venda['data'] > $ultimaVenda) $ultimaVenda = $venda['data'];
}
$dataUltimaVenda = $ultimaVenda ? implode('/', array_reverse(explode('-', $ultimaVenda))) : '—';
?>
<script>
function abrir_venda(id_animal) {
  window.open('geral.php?pg=animal&id_animal=' + encodeURIComponent(id_animal) + '&aba=vender', '_blank');
}
function excluir_venda(botao) {
  confirmarExclusao({
    titulo: 'Excluir venda?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'Confirme se deseja excluir esta venda. Esta ação não pode ser desfeita. Os lançamentos financeiros vinculados ao animal serão removidos e ele retornará ao rebanho.',
    aoConfirmar: function () {
      window.location.href = 'vendas/_excluir_venda.php?id_animal=' + encodeURIComponent(botao.getAttribute('data-id')) + '&id_comprador=<?=$id_comprador?>';
    }
  });
}
</script>
<section class="content-header">
  <h1>Dados do comprador</h1>
  <ol class="breadcrumb">
    <li><a href="geral.php?pg=relatorio_venda"><i class="fa fa-shopping-cart"></i> Vendas</a></li>
    <li><a href="geral.php?pg=compradores">Compradores</a></li>
    <li class="active">Dados do comprador</li>
  </ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-header with-border"><h3 class="box-title">Cadastro do comprador</h3></div>
    <div class="box-body">
      <form method="post" action="vendas/_alterar_comprador.php?id_comprador=<?=$id_comprador?>">
        <div class="row">
          <?php foreach (array(
              'nome' => array('Nome completo', 'nome', 'text'),
              'email' => array('E-mail', 'email', 'email'),
              'celular' => array('Celular (WhatsApp)', 'celular1', 'tel'),
              'cpf' => array('CPF', 'cpf', 'text'),
              'cod' => array('Cód. criador', 'cod_criador', 'text'),
              'cidade' => array('Cidade', 'cidade', 'text'),
          ) as $campo => $config): ?>
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="<?=$campo?>"><?=$config[0]?><?php if ($campo === 'nome'): ?><span class="text-danger">*</span><?php endif; ?></label>
            <input type="<?=$config[2]?>" class="form-control" id="<?=$campo?>" name="<?=$campo?>" value="<?=dadosCompradorH($cadastro[$config[1]] ?? '')?>" <?=$campo === 'nome' ? 'required' : ''?>>
          </div></div>
          <?php endforeach; ?>
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" name="estado" id="estado">
              <option value="">Selecionar</option>
              <?php $estados = DBRead('estado', 'ORDER BY estado ASC') ?: array();
              $siglas = array_column($estados, 'sigla');
              if (!empty($cadastro['estado']) && !in_array($cadastro['estado'], $siglas, true)): ?>
              <option value="<?=dadosCompradorH($cadastro['estado'])?>" selected><?=dadosCompradorH($cadastro['estado'])?></option>
              <?php endif; ?>
              <?php foreach ($estados as $estado): ?>
              <option value="<?=dadosCompradorH($estado['sigla'])?>" <?=($cadastro['estado'] ?? '') === $estado['sigla'] ? 'selected' : ''?>><?=dadosCompradorH($estado['estado'])?></option>
              <?php endforeach; ?>
            </select>
          </div></div>
        </div>
        <div class="text-right">
          <a href="geral.php?pg=compradores" class="btn btn-default">Voltar</a>
          <button type="submit" class="btn btn-warning">Salvar alterações</button>
        </div>
      </form>
    </div>
  </div>
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-4"><strong>Valor total:</strong> <span class="text-success">R$ <?=number_format($total, 2, ',', '.')?></span></div>
        <div class="col-sm-4"><strong>Vendas:</strong> <?=count($vendas)?></div>
        <div class="col-sm-4"><strong>Última venda:</strong> <?=dadosCompradorH($dataUltimaVenda)?></div>
      </div>
    </div>
  </div>
  <div class="box" style="border-top:0;">
    <div class="box-header with-border"><h3 class="box-title">Vendas do comprador</h3></div>
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr>
            <th style="width:1%;">Nº</th><th>Animal</th><th>FBB</th>
            <th><a href="geral.php?pg=comprador&amp;id_comprador=<?=$id_comprador?>&amp;filtro=1" style="color:inherit;" title="Ordenar por data, da mais recente para a mais antiga">Data <i class="fa fa-sort-amount-desc" aria-hidden="true"></i></a></th>
            <th>Tipo de venda</th>
            <th><a href="geral.php?pg=comprador&amp;id_comprador=<?=$id_comprador?>&amp;filtro=2" style="color:inherit;" title="Ordenar por valor, do maior para o menor">Valor <i class="fa fa-sort-amount-desc" aria-hidden="true"></i></a></th>
            <th>Parcelas</th><th style="width:1%; white-space:nowrap;"><span class="sr-only">Ações</span></th>
          </tr></thead>
          <tbody>
            <?php if (!$vendas): ?><tr><td colspan="8" class="text-center">Nenhuma venda cadastrada para este comprador.</td></tr><?php endif; ?>
            <?php foreach ($vendas as $indice => $venda):
                $id_animal = (int)$venda['id_animal'];
                $animal = DBRead('animais', "WHERE id = '$id_animal'");
                $nomeAnimal = $animal[0]['nome'] ?? 'Animal não encontrado';
                $dataVenda = implode('/', array_reverse(explode('-', $venda['data'])));
            ?>
            <tr>
              <td><?=$indice + 1?></td>
              <td><a href="geral.php?pg=animal&amp;id_animal=<?=$id_animal?>&amp;aba=vender" target="_blank" rel="noopener" style="color:inherit;"><?=dadosCompradorH($nomeAnimal)?></a></td>
              <td><?=dadosCompradorH($animal[0]['fbb'] ?? '')?></td>
              <td><?=dadosCompradorH($dataVenda)?></td>
              <td><?=dadosCompradorH($venda['tipo_venda'] . ' - ' . $venda['forma_de_pagamento'])?></td>
              <td style="white-space:nowrap;">R$ <?=number_format((float)$venda['preco_de_venda'], 2, ',', '.')?></td>
              <td><?=max(1, (int)$venda['parcelas'])?>x</td>
              <td style="white-space:nowrap;">
                <button type="button" class="text-primary" style="background:none; border:0; padding:0; margin-right:10px; cursor:pointer;" onclick="abrir_venda(<?=$id_animal?>)" title="Abrir venda" aria-label="Abrir venda"><i class="fa fa-search" aria-hidden="true"></i></button>
                <button type="button" class="text-danger" style="background:none; border:0; padding:0; cursor:pointer;" data-id="<?=$id_animal?>" data-nome="<?=dadosCompradorH($nomeAnimal)?>" onclick="excluir_venda(this)" title="Excluir venda" aria-label="Excluir venda"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
