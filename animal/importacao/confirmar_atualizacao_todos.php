<div class="modal fade" id="confirmar-atualizacao-todos" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="titulo-atualizacao-todos">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form id="form-atualizacao-todos" action="animal/_atualizar_importacao.php" method="post">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">&times;</button>
        <h4 class="modal-title" id="titulo-atualizacao-todos">Confirmar atualização de todos</h4>
      </div>
      <div class="modal-body">
        <p><strong><?=count($lotePrevia)?> animal(is)</strong> no lote completo, incluindo outras páginas e resultados fora da pesquisa. Somente campos diferentes podem ser selecionados e iniciam marcados. As seleções são preservadas ao navegar nesta revisão.</p>
        <p>Linhas inválidas, já aplicadas ou ambíguas ficam fora do lote. Se alguma atualização falhar, nenhuma alteração do lote será salva.</p>
        <fieldset id="campos-lote">
          <legend style="font-size:16px; margin-bottom:8px;">Campos que deseja atualizar</legend>
          <p class="help-block">A seleção vale para todos os animais, em todas as páginas. Desmarque um campo para manter o valor atual no banco.</p>
          <?php
          $camposDisponiveis = array();
          foreach ($lotePrevia as $itemLote) $camposDisponiveis += $itemLote['novos'];
          foreach (array('nome'=>'Nome', 'fbb'=>'FBB', 'tatuagem'=>'Tatuagem', 'data_de_nascimento'=>'Nascimento', 'causa_da_perda'=>'Causa da morte', 'data_de_saida'=>'Data da morte') as $campoLote => $rotuloLote):
              if (!array_key_exists($campoLote, $camposDisponiveis)) continue;
          ?>
          <label class="checkbox-inline"><input type="checkbox" value="<?=hPreviaRebanho($campoLote)?>" checked> <?=hPreviaRebanho($rotuloLote)?></label>
          <?php endforeach; ?>
        </fieldset>
        <p id="erro-lote" class="text-danger" role="alert"></p>
        <div class="table-responsive" style="max-height:50vh; overflow:auto;">
          <table class="table table-bordered"><thead><tr><th>Atualizar</th><th>Animal</th><th>Campo</th><th>Atual no banco</th><th>Novo valor da planilha</th></tr></thead><tbody id="corpo-lote"></tbody></table>
        </div>
        <div class="text-right">
          <button type="button" class="btn btn-default btn-sm" id="anterior-lote">«</button>
          <span id="pagina-lote" aria-live="polite"></span>
          <button type="button" class="btn btn-default btn-sm" id="proxima-lote">»</button>
        </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="token_previa" value="<?=hPreviaRebanho($_SESSION['token_previa_rebanho'])?>">
        <input type="hidden" name="envio" value="<?=hPreviaRebanho($envioPrevia)?>">
        <input type="hidden" name="acao" value="todos">
        <input type="hidden" name="exclusoes_campos" value="{}">
        <input type="hidden" name="exclusoes_globais" value="[]">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" disabled>Confirmar atualização de todos</button>
      </div>
    </form>
  </div></div>
</div>
<script src="animal/importacao/revisao_lote.js?v=<?=filemtime(__DIR__ . '/revisao_lote.js')?>"></script>
