<script>
function validarCadastroInseminacao() {
  var ids = ['lote', 'data_inicial', 'pai', 'raca', 'semen', 'notificacao'];
  var invalido = false;
  ids.forEach(function (id) {
    var campo = document.getElementById(id);
    if (!campo) { return; }
    var vazio = !campo.value.trim();
    campo.style.border = vazio ? '1px solid red' : '';
    if (vazio) { invalido = true; }
  });
  return !invalido;
}
</script>

<section class="content-header">
  <h1>Cadastrar Inseminação artificial</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li class="active">Cadastrar Inseminação artificial</li>
  </ol>
</section>

<section class="content">
  <div class="box" style="border-top:0;">
    <form method="post" action="reproducao/inseminacao/_cadastrar.php" onsubmit="return validarCadastroInseminacao()">
      <div class="box-body">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="lote">Lote<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="lote" name="lote">
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="data_inicial">Data<span class="text-danger">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial">
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <?php renderBuscaAnimais(array(
                  'id' => 'pai',
                  'name' => 'macho',
                  'name_id' => 'macho_id',
                  'name_origem' => 'macho_origem',
                  'label' => 'Macho',
                  'tipo' => 'machos',
                  'value' => $user[0]['prefixo'] ?? '',
                  'required' => true,
                  'limite_origem' => 5,
                  'novo_url' => 'geral.php?pg=cadastrar_animal&tipo=2'
              )); ?>
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="raca">Raça<span class="text-danger">*</span></label>
              <select class="form-control select" id="raca" name="raca">
                <option value="<?=htmlspecialchars($user[0]['raca'] ?? '', ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($user[0]['raca'] ?? '', ENT_QUOTES, 'UTF-8')?></option>
                <?php
                $racas = DBRead('raca', 'ORDER BY nome ASC') ?: array();
                foreach ($racas as $raca):
                  if (($raca['nome'] ?? '') === ($user[0]['raca'] ?? '')) { continue; }
                ?>
                <option value="<?=htmlspecialchars($raca['nome'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($raca['nome'], ENT_QUOTES, 'UTF-8')?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="semen">Sêmen<span class="text-danger">*</span></label>
              <select class="form-control select" id="semen" name="semen">
                <option value="">Selecionar</option>
                <option value="A fresco">A fresco</option>
                <option value="Congelado">Congelado</option>
                <option value="Refrigerado">Refrigerado</option>
              </select>
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="notificacao">Notificação<span class="text-danger">*</span></label>
              <select class="form-control select" id="notificacao" name="notificacao">
                <option value="">Selecionar</option>
                <option value="PO">PO</option>
                <option value="PC">PC</option>
              </select>
            </div>
          </div>

          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-success">Cadastrar lote</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
