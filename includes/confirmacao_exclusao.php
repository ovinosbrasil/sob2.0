<?php require_once __DIR__ . '/csrf.php'; ?>
<form id="sob-post-csrf" method="post" hidden><?=campoCsrfSob()?></form>
<script src="dist/js/csrf.js?v=<?=filemtime(__DIR__ . '/../dist/js/csrf.js')?>"></script>
<div class="modal fade sob-confirmacao-exclusao" id="confirmacao-exclusao" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="confirmacao-exclusao-titulo" aria-describedby="confirmacao-exclusao-descricao">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <button type="button" class="fechar-confirmacao" data-dismiss="modal" aria-label="Fechar">&times;</button>
      <div class="icone-confirmacao" aria-hidden="true"><i class="fa fa-trash-o"></i></div>
      <h3 id="confirmacao-exclusao-titulo">Excluir registro?</h3>
      <p id="confirmacao-exclusao-descricao"></p>
      <div class="animal-confirmacao"><strong id="confirmacao-exclusao-nome"></strong></div>
      <div class="acoes-confirmacao">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="confirmacao-exclusao-cancelar">Cancelar</button>
        <button type="button" class="btn btn-warning" id="confirmacao-exclusao-inativar" style="display:none;">Inativar</button>
        <button type="button" class="btn btn-danger" id="confirmacao-exclusao-prosseguir">Excluir</button>
      </div>
    </div>
  </div>
</div>
