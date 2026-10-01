<div class="modal fade" id="cadastro-previa-rebanho" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="titulo-cadastro-previa">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulo-cadastro-previa">Confirmar cadastro dos animais</h4>
      </div>
      <div class="modal-body" style="max-height:65vh; overflow:auto;">
        <p id="aviso-cadastro-todos" class="alert alert-info" hidden>Serão cadastrados todos os animais da aba Animais não cadastrados, incluindo outras páginas e resultados fora da pesquisa, além dos parentes necessários. Animais com dados inconsistentes serão ignorados e o cadastro seguirá com os demais. Confira abaixo os motivos. Uma falha desfaz todos os cadastros do lote.</p>
        <p>Confira todos os animais abaixo. Os pais necessários serão cadastrados antes dos filhos. Os IDs dos novos cadastros serão gerados ao confirmar e usados nos vínculos.</p>
        <p>A raça dos novos animais e terceiros será a cadastrada no perfil da fazenda. Os demais dados ausentes na planilha ficam não informados. Pai ou mãe sem nome ficam sem vínculo. Os indicadores de crias dos pais do rebanho serão atualizados.</p>
        <div id="erro-cadastro-previa" class="alert alert-danger" role="alert" hidden></div>
        <div id="dados-cadastro-previa" aria-live="polite"></div>
      </div>
      <div class="modal-footer">
        <form id="form-cadastro-previa" action="animal/_cadastrar_importacao.php" method="post">
          <input type="hidden" name="token_previa" value="<?=hPreviaRebanho($_SESSION['token_previa_rebanho'])?>">
          <input type="hidden" name="envio" value="<?=hPreviaRebanho($envioPrevia)?>">
          <input type="hidden" name="linha" value="">
          <input type="hidden" name="confirmacao" value="">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success" id="confirmar-cadastro-previa" disabled>Confirmar cadastro</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="animal/importacao/cadastro_previa.js?v=<?=filemtime(__DIR__ . '/cadastro_previa.js')?>"></script>
