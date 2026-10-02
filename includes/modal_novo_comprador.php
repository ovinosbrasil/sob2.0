<div class="modal fade" id="novo-comprador-modal" tabindex="-1" role="dialog" aria-labelledby="novo-comprador-titulo">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:10px;">
      <form id="form-novo-comprador" action="vendas/_comprador.php" method="post">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="novo-comprador-titulo">Novo comprador</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="formato" value="json">
          <div class="alert alert-danger" id="novo-comprador-erro" role="alert" hidden></div>
          <div class="form-group">
            <label for="novo-comprador-nome">Nome completo<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="novo-comprador-nome" name="nome" required>
          </div>
          <div class="row">
            <?php foreach (array('email' => 'E-mail', 'celular' => 'Celular (WhatsApp)', 'cpf' => 'CPF', 'cod' => 'Cód. criador', 'cidade' => 'Cidade') as $campo => $rotulo): ?>
            <div class="col-sm-6"><div class="form-group">
              <label for="novo-comprador-<?=$campo?>"><?=$rotulo?></label>
              <input type="<?=$campo === 'email' ? 'email' : ($campo === 'celular' ? 'tel' : 'text')?>" class="form-control" id="novo-comprador-<?=$campo?>" name="<?=$campo?>">
            </div></div>
            <?php endforeach; ?>
            <div class="col-sm-6"><div class="form-group">
              <label for="novo-comprador-estado">Estado</label>
              <select class="form-control" id="novo-comprador-estado" name="estado">
                <option value="">Selecionar</option>
                <?php foreach ((array)DBRead('estado', 'ORDER BY estado ASC') as $estado): ?>
                <option value="<?=htmlspecialchars($estado['sigla'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($estado['estado'], ENT_QUOTES, 'UTF-8')?></option>
                <?php endforeach; ?>
              </select>
            </div></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success" id="novo-comprador-salvar">Cadastrar comprador</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var formulario = document.getElementById('form-novo-comprador');
  var salvar = document.getElementById('novo-comprador-salvar');
  var erro = document.getElementById('novo-comprador-erro');
  var modal = jQuery('#novo-comprador-modal');
  var enviando = false;
  modal.on('shown.bs.modal', function () {
    document.getElementById('novo-comprador-nome').focus();
  });
  modal.on('show.bs.modal', function () { erro.hidden = true; });
  modal.on('hide.bs.modal', function (evento) { if (enviando) evento.preventDefault(); });
  modal.on('hidden.bs.modal', function () { document.getElementById('comprador').focus(); });
  formulario.addEventListener('submit', function (evento) {
    evento.preventDefault();
    if (enviando || !formulario.reportValidity()) return;
    enviando = true;
    salvar.disabled = true;
    erro.hidden = true;
    fetch(formulario.action, {method: 'POST', body: new FormData(formulario), credentials: 'same-origin'})
      .then(function (resposta) { return resposta.json(); })
      .then(function (resultado) {
        if (!resultado.sucesso) throw new Error(resultado.mensagem || 'Não foi possível cadastrar o comprador.');
        var input = document.getElementById('comprador');
        var componente = input.closest('[data-busca-compradores]');
        input.value = resultado.comprador.nome;
        componente.querySelector('[data-busca-compradores-id]').value = resultado.comprador.id;
        componente.querySelector('.sob-busca-animais__resultados').hidden = true;
        input.setAttribute('aria-expanded', 'false');
        componente.dispatchEvent(new CustomEvent('buscacompradores:selecionado', {bubbles: true, detail: resultado.comprador}));
        formulario.reset();
        enviando = false;
        modal.modal('hide');
      })
      .catch(function (falha) {
        erro.textContent = falha.message || 'Não foi possível cadastrar o comprador. Tente novamente.';
        erro.hidden = false;
      })
      .finally(function () { enviando = false; salvar.disabled = false; });
  });
});
</script>
