<div class="col-md-12 avaliacao-gmd">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title" id="titulo_pesagem">Cálculo de GMD (Ganho Médio Diário)</h3>
    </div>
    <div class="box-body">
      <div class="gmd-layout" data-sexo="<?=htmlspecialchars($animal[0]['sexo'], ENT_QUOTES, 'UTF-8')?>">
        <div class="gmd-campos">
          <div class="form-group">
            <label for="data_nascimento">Data inicial</label>
            <input type="text" class="form-control" id="data_nascimento" value="<?=$data_nascimento?>" readonly>
            <small>Nascimento</small>
          </div>
          <div class="form-group">
            <label for="peso_inicial">Peso inicial (kg)</label>
            <input type="text" class="form-control" id="peso_inicial" value="<?=number_format((float)($animal[0]['peso_inicial'] ?? 0), 3, '.', '')?>" readonly>
            <small>Primeira pesagem</small>
          </div>
          <div class="form-group">
            <label for="gmd_data">Data da avaliação <span style="color:#F00;">*</span></label>
            <input type="date" class="form-control" id="gmd_data" name="gmd_data" form="form1" value="<?=$pesagemAvaliacao ? $pesagemAvaliacao->format('Y-m-d') : ''?>" required>
          </div>
          <div class="form-group">
            <label for="gmd_peso">Peso na avaliação (kg) <span style="color:#F00;">*</span></label>
            <input type="text" class="form-control" id="gmd_peso" name="gmd_peso" form="form1" data-casas-decimais="3" data-separador-decimal="," data-separador-milhar="." inputmode="decimal" placeholder="Ex.: 123,213" value="<?=($animal[0][$avaliacao == 1 ? 'peso2' : 'peso3'] ?? 0) > 0 ? number_format((float)$animal[0][$avaliacao == 1 ? 'peso2' : 'peso3'], 3, ',', '') : ''?>" required>
          </div>
          <div class="form-group">
            <label for="nota_tamanho">Nota GMD</label>
            <select class="form-control avaliacao-nota" id="nota_tamanho" name="tamanho" form="form1" data-titulo="titulo_pesagem" required>
              <option value="">Selecionar</option>
              <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
              <option value="<?=$nota?>" <?=$notaGmd == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
              <? } ?>
            </select>
          </div>
        </div>
        <div class="gmd-informacoes">
        <div class="gmd-resumo" aria-live="polite">
          <div><span>Diferença de peso</span><strong id="gmd_diferenca">—</strong></div>
          <div><span>Período</span><strong id="gmd_periodo">—</strong></div>
          <div><span>GMD</span><strong id="gmd_resultado">—</strong></div>
        </div>
      <div class="gmd-dicas" id="gmd_dicas" hidden>
        <p id="gmd_dicas_titulo"></p>
        <div class="table-responsive"><table class="table table-bordered">
          <thead><tr><th>Tipo</th><th>Ganho médio diário</th></tr></thead>
          <tbody id="gmd_dicas_linhas"></tbody>
        </table></div>
      </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="animal/avaliacao/gmd.js"></script>
