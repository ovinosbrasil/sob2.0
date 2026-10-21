<?php require_once __DIR__ . '/../includes/csrf.php';

require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/registro.php';
$id_financeiro = (int)($_GET['id_financeiro'] ?? 0);
$registros = $id_financeiro > 0 ? DBRead('controle_financeiro', "WHERE id = '$id_financeiro'") : false;
if (!$registros) { http_response_code(404); exit; }
$registro = $registros[0];
$tipo = (int)$registro['tipo'];
$vendaFinanceira = vendaRegistroFinanceiro($registro);
$rotulo = $vendaFinanceira ? 'venda' : ($tipo ? 'despesa' : 'receita');
$h = function ($valor) { return htmlspecialchars(textoRegistroFinanceiro($valor), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$data = DateTime::createFromFormat('!Y-m-d', $registro['data']);
$formas = array_unique(array_merge(array($registro['forma_de_pagamento']), array('Pix', 'Boleto', 'Cheque', 'Dinheiro', 'Depósito', 'Cartão', 'Transferência', 'Transação bancária', 'Troca')));
$categorias = array_unique(array_merge(array($registro['categoria']), array('Custos fixos (Água, luz, telefone)', 'Infraestrutura', 'Pagamento Profissional', 'Encargos de funcionários', 'Contabilidade', 'Marketing', 'Outros')));
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="titulo-alterar-financeiro">Alterar <?=$rotulo?></h4>
</div>
<form method="post" action="financeiro/_alterar.php?id_financeiro=<?=$id_financeiro?>" data-alertas-financeiro="alterar" data-tipo="<?=$tipo?>" novalidate>
        <?=campoCsrfSob()?>
  <div class="modal-body">
    <div class="form-group">
      <label for="descricao_alterar">Descrição <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="descricao_alterar" name="descricao_alterar" value="<?=$h($registro['titulo'])?>" <?=$vendaFinanceira ? 'readonly aria-describedby="descricao-venda-bloqueada"' : ''?> required>
      <?php if ($vendaFinanceira): ?><small class="help-block" id="descricao-venda-bloqueada">A descrição é gerada pela venda e não pode ser alterada.</small><?php endif; ?>
    </div>
    <div class="row">
      <div class="col-sm-6 form-group">
        <label for="data_alterar">Vencimento <span class="text-danger">*</span></label>
        <div class="input-group date"><span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span><input type="text" class="form-control" id="data_alterar" name="data_alterar" value="<?=$data ? $data->format('d/m/Y') : ''?>" required></div>
      </div>
      <div class="col-sm-6 form-group">
        <label for="valor_alterar">Valor da parcela <span class="text-danger">*</span></label>
        <div class="input-group"><span class="input-group-addon">R$</span><input type="text" class="form-control" id="valor_alterar" name="valor_alterar" value="<?=number_format((float)$registro['valor'], 2, '.', '')?>" placeholder="0,00" inputmode="decimal" required></div>
      </div>
    </div>
    <div class="form-group">
      <label for="forma_alterar">Forma de pagamento <span class="text-danger">*</span></label>
      <select class="form-control" name="forma_alterar" id="forma_alterar" required>
        <option value="">Selecionar forma</option>
        <?php foreach ($formas as $forma): if ($forma === '') continue; ?>
        <option value="<?=$h($forma)?>" <?=$forma === $registro['forma_de_pagamento'] ? 'selected' : ''?>><?=$h($forma)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php if ($tipo): ?>
    <div class="form-group">
      <label for="categoria_alterar">Categoria <span class="text-danger">*</span></label>
      <select class="form-control" name="categoria_alterar" id="categoria_alterar" required>
        <option value="">Selecionar categoria</option>
        <?php foreach ($categorias as $categoria): if ($categoria === '') continue; ?>
        <option value="<?=$h($categoria)?>" <?=$categoria === $registro['categoria'] ? 'selected' : ''?>><?=$h($categoria)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-success">Salvar alterações</button>
  </div>
</form>
