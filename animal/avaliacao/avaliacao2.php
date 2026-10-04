<script type="text/javascript">

function ativar_avaliacao() {
  var valido = true;
  document.querySelectorAll('#avaliacoes-animal .avaliacao-nota').forEach(function (campo) {
    var titulo = document.getElementById(campo.dataset.titulo);
    titulo.style.color = campo.value ? '' : 'red';
    campo.setAttribute('aria-invalid', campo.value ? 'false' : 'true');
    if (!campo.value) valido = false;
  });
  return valido;
}
</script>


<?
$ava2 = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = 2");

$lerDataAvaliacao = function ($valor) {
  if (!is_string($valor) || !preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $valor, $partes)
      || !checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
    return null;
  }
  return DateTime::createFromFormat('!Y-m-d', $valor);
};
$nascimentoAvaliacao = $lerDataAvaliacao($animal[0]['data_de_nascimento'] ?? null);
$pesagemAvaliacao = $lerDataAvaliacao($animal[0]['data3'] ?? null);
$data_nascimento = $nascimentoAvaliacao ? $nascimentoAvaliacao->format('d/m/Y') : '';
$data_adulto = $pesagemAvaliacao ? $pesagemAvaliacao->format('d/m/Y') : '';
$dias_adulto = null;
$gmd = null;
if ($nascimentoAvaliacao && $pesagemAvaliacao && $pesagemAvaliacao > $nascimentoAvaliacao) {
  $dias_adulto = (int)$nascimentoAvaliacao->diff($pesagemAvaliacao)->days;
  if (is_numeric($animal[0]['peso3'] ?? null) && is_numeric($animal[0]['peso_inicial'] ?? null)) {
    $gmd = ($animal[0]['peso3'] - $animal[0]['peso_inicial']) / $dias_adulto * 1000;
  }
}
?>
<form id="form1" name="form1" method="post" action="animal/avaliacao/_avaliar.php?id_animal=<?=$id_animal?>&avaliacao=2" onsubmit="return ativar_avaliacao()"></form>
<? $notaGmd = $ava2[0]['tamanho'] ?? null; include __DIR__ . "/gmd.php"; ?>

<div class="col-md-12"><div class="avaliacao-grade">
<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_cabeca">Cabeça</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_cabeca" name="cabeca" form="form1" data-titulo="titulo_cabeca" aria-label="Nota: cabeca" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['cabeca'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_pescoco">Pescoço</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_pescoco" name="pescoco" form="form1" data-titulo="titulo_pescoco" aria-label="Nota: pescoco" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['pescoco'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_quarto_anterior">Quarto anterior</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_quarto_anterior" name="quarto_anterior" form="form1" data-titulo="titulo_quarto_anterior" aria-label="Nota: quarto anterior" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['quarto_anterior'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_barril">Barril</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_barril" name="barril" form="form1" data-titulo="titulo_barril" aria-label="Nota: barril" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['barril'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_quarto_posterior">Quarto posterior</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_quarto_posterior" name="quarto_posterior" form="form1" data-titulo="titulo_quarto_posterior" aria-label="Nota: quarto posterior" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['quarto_posterior'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_comprimento">Comprimento</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_comprimento" name="comprimento" form="form1" data-titulo="titulo_comprimento" aria-label="Nota: comprimento" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['comprimento'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_orgao">Órgão Sexual</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_orgao" name="orgao" form="form1" data-titulo="titulo_orgao" aria-label="Nota: orgao" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['orgao'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_distribuicao">Distribuição de gordura</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_distribuicao" name="distribuicao" form="form1" data-titulo="titulo_distribuicao" aria-label="Nota: distribuicao" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['distribuicao'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_cobertura">Cobertura</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_cobertura" name="cobertura" form="form1" data-titulo="titulo_cobertura" aria-label="Nota: cobertura" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['cobertura'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

<div class="avaliacao-item">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_cor">Cor</h3>
      </div>
      <div class="box-body">
        <div class="avaliacao-criterio"><select class="form-control avaliacao-nota" id="nota_cor" name="cor" form="form1" data-titulo="titulo_cor" aria-label="Nota: cor" required>
          <option value="">Selecionar</option>
          <? foreach ([5 => 'Muito bom', 4 => 'Bom', 3 => 'Ruim', 2 => 'Descarte'] as $nota => $descricao) { ?>
          <option value="<?=$nota?>" <?=($ava2[0]['cor'] ?? null) == $nota ? 'selected' : ''?>>Tipo <?=$nota?> (<?=$descricao?>)</option>
          <? } ?>
        </select></div></div>
    </div>
</div>

</div></div>

<div class="col-md-12">
  <button type="submit" form="form1" class="btn btn-success" style="margin-top:6%;">Avaliar animal</button>
</div>
