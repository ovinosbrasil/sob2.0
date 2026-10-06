<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php
$alertaPerfil = $_SESSION['alerta_perfil'] ?? null;
unset($_SESSION['alerta_perfil']);
?>
<script type="text/javascript">
function ativar() {
  var campos = {nome: 'Nome completo', cpf: 'CPF/CNPJ', celular: 'Celular (Whatsapp)', fazenda: 'Nome da fazenda'};
  var faltantes = [];
  var primeiro = null;
  Object.keys(campos).forEach(function (id) {
    var campo = document.getElementById(id);
    var vazio = !campo.value.trim();
    campo.closest('.form-group').classList.toggle('has-error', vazio);
    campo.setAttribute('aria-invalid', vazio ? 'true' : 'false');
    if (vazio) { faltantes.push(campos[id]); primeiro = primeiro || campo; }
  });
  if (faltantes.length) {
    SobAlertas.camposObrigatorios(faltantes);
    primeiro.focus();
    return false;
  }
  return true;
}
document.addEventListener('DOMContentLoaded', function () {
  var alerta = <?=json_encode($alertaPerfil, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  if (alerta) { SobAlertas.mostrar(alerta); }
});
</script>

<?php
$perfilH = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$perfilDados = $user[0] ?? array();
?>
<section class="content-header">
  <h1>Dados gerais</h1>
  <ol class="breadcrumb"><li class="active"><i class="fa fa-user"></i> Perfil</li></ol>
</section>
<section class="content">
  <form role="form" action="perfil/_alterar.php?id=<?=(int)$id_user?>" method="post" onsubmit="return ativar()">
    <div class="box" style="border-top:0;">
      <div class="box-body">
        <h3 class="box-title" style="font-size:18px; margin:0 0 20px;">Dados pessoais</h3>
        <div class="row">
            <div class="col-sm-6"><div class="form-group">
              <label for="nome">Nome completo<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nome" name="nome" value="<?=$perfilH($perfilDados['responsavel'] ?? '')?>" aria-required="true">
            </div></div>
            <div class="col-sm-3"><div class="form-group">
              <label for="cpf">CPF/CNPJ<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="cpf" name="cpf" value="<?=$perfilH($perfilDados['cpf'] ?? '')?>" aria-required="true">
            </div></div>
            <div class="col-sm-3"><div class="form-group">
              <label for="cod">Cód. do responsável</label>
              <input type="text" class="form-control" id="cod" name="cod" value="<?=$perfilH($perfilDados['cod'] ?? '')?>">
            </div></div>
        </div>
        <div class="row">
            <div class="col-sm-6"><div class="form-group">
              <label for="email">E-mail</label>
              <input type="text" class="form-control" id="email" name="email" value="<?=$perfilH($perfilDados['email'] ?? '')?>">
            </div></div>
            <div class="col-sm-3"><div class="form-group">
              <label for="celular">Celular (Whatsapp)<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="celular" name="celular" value="<?=$perfilH($perfilDados['celular'] ?? '')?>" aria-required="true">
            </div></div>
            <div class="col-sm-3"><div class="form-group">
              <label for="telefone">Telefone</label>
              <input type="text" class="form-control" id="telefone" name="telefone" value="<?=$perfilH($perfilDados['telefone'] ?? '')?>">
            </div></div>
        </div>
        <h3 class="box-title" style="font-size:18px; margin:0 0 20px;">Dados da fazenda</h3>
        <div class="row">
            <div class="col-sm-6"><div class="form-group">
              <label for="fazenda">Nome da fazenda<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="fazenda" name="fazenda" value="<?=$perfilH($perfilDados['fazenda'] ?? '')?>" aria-required="true">
            </div></div>
            <div class="col-sm-2"><div class="form-group">
              <label for="prefixo">Prefixo</label>
              <input type="text" class="form-control" id="prefixo" name="prefixo" value="<?=$perfilH($perfilDados['prefixo'] ?? '')?>">
            </div></div>
            <div class="col-sm-2"><div class="form-group">
              <label for="raca">Raça padrão</label>
              <select class="form-control select" id="raca" name="raca">
                <option value="">Selecionar raça</option>
                <?php if (!empty($perfilDados['raca'])): ?><option value="<?=$perfilH($perfilDados['raca'])?>" selected><?=$perfilH($perfilDados['raca'])?></option><?php endif; ?>
                <?php foreach (DBRead('raca', 'ORDER BY nome asc') ?: array() as $raca): ?>
                <option value="<?=$perfilH($raca['nome'])?>"><?=$perfilH($raca['nome'])?></option>
                <?php endforeach; ?>
              </select>
            </div></div>
            <div class="col-sm-2"><div class="form-group">
              <label for="cod_rebanho">Cód. do rebanho</label>
              <input type="text" class="form-control" id="cod_rebanho" name="cod_rebanho" value="<?=$perfilH($perfilDados['cod_rebanho'] ?? '')?>">
            </div></div>
        </div>
        <div class="row">
            <div class="col-sm-8"><div class="form-group">
              <label for="tecnico">Técnico responsável</label>
              <input type="text" class="form-control" id="tecnico" name="tecnico" value="<?=$perfilH($perfilDados['tecnico'] ?? '')?>">
            </div></div>
            <div class="col-sm-4"><div class="form-group">
              <label for="cod_tecnico">Cód. do técnico</label>
              <input type="text" class="form-control" id="cod_tecnico" name="cod_tecnico" value="<?=$perfilH($perfilDados['cod_tecnico'] ?? '')?>">
            </div></div>
        </div>
        <h3 class="box-title" style="font-size:18px; margin:0 0 20px;">Endereço</h3>
        <div class="row">
            <div class="col-sm-6"><div class="form-group">
              <label for="end">Endereço</label>
              <input type="text" class="form-control" id="end" name="end" value="<?=$perfilH($perfilDados['end'] ?? '')?>">
            </div></div>
            <div class="col-sm-2"><div class="form-group">
              <label for="num">Número</label>
              <input type="text" class="form-control" id="num" name="num" value="<?=$perfilH($perfilDados['num'] ?? '')?>">
            </div></div>
            <div class="col-sm-4"><div class="form-group">
              <label for="cep">CEP</label>
              <input type="text" class="form-control" id="cep" name="cep" value="<?=$perfilH($perfilDados['cep'] ?? '')?>">
            </div></div>
        </div>
        <div class="row">
            <div class="col-sm-8"><div class="form-group">
              <label for="cidade">Cidade</label>
              <input type="text" class="form-control" id="cidade" name="cidade" value="<?=$perfilH($perfilDados['cidade'] ?? '')?>">
            </div></div>
            <div class="col-sm-4"><div class="form-group">
              <label for="estado">Estado</label>
              <select class="form-control select" id="estado" name="estado">
                <option value="">Selecionar estado</option>
                <?php foreach (DBRead('estado', 'ORDER BY estado asc') ?: array() as $estado): ?>
                <option value="<?=$perfilH($estado['sigla'])?>" <?=($perfilDados['estado'] ?? '') === $estado['sigla'] ? 'selected' : ''?>><?=$perfilH($estado['estado'])?></option>
                <?php endforeach; ?>
              </select>
            </div></div>
        </div>
        <div class="text-right">
          <button type="submit" class="btn btn-warning">Alterar perfil</button>
        </div>
      </div>
    </div>
  </form>
</section>
