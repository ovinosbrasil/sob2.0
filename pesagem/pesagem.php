<?php
$hoje = date('d/m/Y');
$id_animal = max(0, (int)filter_var($_GET['id_animal'] ?? 0, FILTER_VALIDATE_INT));
$animal = $id_animal ? (DBRead('animais', "WHERE id = '$id_animal'") ?: array()) : array();
$peso = $animal ? (DBRead('pesagem', "WHERE id_animal = '$id_animal' ORDER BY data ASC, id ASC") ?: array()) : array();
$lerDataPeso = function ($valor) {
  if (!is_string($valor) || !preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $valor, $partes)
      || !checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
    return null;
  }
  return DateTime::createFromFormat('!Y-m-d', $valor);
};
$dadosGraficoPesagem = array();
foreach ($peso as $registroPeso) {
  if ($lerDataPeso($registroPeso['data'] ?? null) && is_numeric($registroPeso['peso'] ?? null)) {
    $dadosGraficoPesagem[] = array('y' => $registroPeso['data'], 'item1' => (float)$registroPeso['peso']);
  }
}
?>

<script type="text/javascript">
function validarPesagem(){
  var faltantes=[],primeiro=null;
  [['animal','Animal'],['data','Data'],['valor','Peso (kg)']].forEach(function(item){
    var campo=document.getElementById(item[0]),invalido=!campo.value.trim();
    campo.style.borderColor=invalido?'#dd4b39':'';
    if(invalido){campo.setAttribute('aria-invalid','true');faltantes.push(item[1]);if(!primeiro)primeiro=campo;}else{campo.removeAttribute('aria-invalid');}
  });
  if(faltantes.length){SobAlertas.camposObrigatorios(faltantes);primeiro.focus();return false;}
  var campo=document.getElementById('valor'),valor=campo.value.trim().replace(/\./g,'').replace(',','.');
  if(!/^\d+(?:\.\d{1,3})?$/.test(valor)||Number(valor)<=0){campo.style.borderColor='#dd4b39';campo.setAttribute('aria-invalid','true');SobAlertas.mostrar({tipo:'warning',titulo:'Atenção!',mensagem:'Informe um peso maior que zero, com até três casas decimais.'});campo.focus();return false;}
  return true;
}

document.addEventListener('buscaanimais:selecionado', function (evento) {
  if (!evento.target.classList.contains('busca-animal-pesagem')) { return; }
  var animal = evento.detail || {};
  if (animal.id && animal.origem === 'rebanho') {
    window.location.href = 'geral.php?pg=pesagem&id_animal=' + encodeURIComponent(animal.id);
  }
});

function excluir_peso(botao) {
  confirmarExclusao({
    titulo: 'Excluir pesagem?',
    nome: botao.getAttribute('data-descricao'),
    descricao: 'Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      window.location.href = 'pesagem/_excluir_peso.php?id_peso=' +
        encodeURIComponent(botao.getAttribute('data-id')) + '&id_animal=<?=$id_animal?>';
    }
  });
}
</script>

<section class="content-header">
  <h1>Pesagem individual</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-eyedropper"></i> Pesagem</li>
    <li class="active">Individual</li>
  </ol>
</section>

<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-header with-border">
      <h3 class="box-title">Cadastrar pesagem</h3>
    </div>
    <form method="post" action="pesagem/_pesagem.php" onsubmit="return validarPesagem()" novalidate>
      <div class="box-body">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <?php renderBuscaAnimais(array(
                'id' => 'animal',
                'name' => 'animal',
                'label' => 'Animal',
                'tipo' => 'rebanho',
                'required' => true,
                'value' => $animal[0]['nome'] ?? '',
                'value_id' => $animal ? $id_animal : 0,
                'value_origem' => $animal ? 'rebanho' : '',
                'classe' => 'busca-animal-pesagem'
              )); ?>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="data">Data<span class="text-danger">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                <input type="text" class="form-control" id="data" name="data" value="<?=htmlspecialchars($_GET['data'] ?? $hoje, ENT_QUOTES, 'UTF-8')?>" required <?=$animal ? '' : 'disabled'?>>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="valor">Peso (kg)<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="valor" name="valor" value="<?=htmlspecialchars($_GET['valor'] ?? '', ENT_QUOTES, 'UTF-8')?>" data-casas-decimais="3" data-separador-decimal="," data-separador-milhar="." inputmode="decimal" placeholder="Ex.: 2,675" required <?=$animal ? '' : 'disabled'?>>
            </div>
          </div>
          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-success" <?=$animal ? '' : 'disabled'?>>Cadastrar peso</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-header with-border">
      <h3 class="box-title">Histórico e evolução de peso</h3>
    </div>
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6 col-md-4">
          <div class="form-group">
            <?php renderBuscaAnimais(array(
              'id' => 'animal-historico-pesagem',
              'name' => 'animal_historico',
              'label' => 'Pesquisar animal',
              'placeholder' => 'Digite o nome do animal',
              'tipo' => 'rebanho',
              'value' => $animal[0]['nome'] ?? '',
              'value_id' => $animal ? $id_animal : 0,
              'value_origem' => $animal ? 'rebanho' : '',
              'classe' => 'busca-animal-pesagem'
            )); ?>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-5">
          <h4 style="font-size:16px; margin:5px 0 15px;">Pesagens cadastradas</h4>
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Peso (kg)</th>
                  <th><abbr title="Ganho médio diário">GMD</abbr> / Dias</th>
                  <th style="width:1%;"><span class="sr-only">Ações</span></th>
                </tr>
              </thead>
              <tbody>
              <?php
          $dataBase = null;
          $pesoBase = null;
          if (!$peso) { ?>
            <tr><td colspan="4" class="text-center"><?=$animal ? 'Nenhuma pesagem cadastrada.' : 'Selecione um animal para consultar as pesagens.'?></td></tr>
          <?php }
          foreach ($peso as $peso_) {
            $dataPeso = $lerDataPeso($peso_['data'] ?? null);
            $data = $dataPeso ? $dataPeso->format('d/m/Y') : 'Não informada';
            $gmd = null;
            $dias = null;
            if ($dataPeso && is_numeric($peso_['peso'] ?? null)) {
              if ($dataBase && $dataPeso > $dataBase) {
                $dias = (int)$dataBase->diff($dataPeso)->days;
                $gmd = ($peso_['peso'] - $pesoBase) / $dias * 1000;
              } elseif (!$dataBase) {
                $dataBase = $dataPeso;
                $pesoBase = $peso_['peso'];
              }
            }
          ?>

                <tr>
                  <td><?=$data?></td>
                  <td><?=number_format((float)$peso_['peso'], 3, ',', '.')?> kg</td>
                  <td class="<?=$gmd === null ? 'text-muted' : ($gmd < 0 ? 'text-danger' : 'text-success')?>"><?=$gmd === null ? '—' : number_format($gmd, 2, ',', '.') . ' g / ' . $dias . ' dias'?></td>
                  <td>
                    <button type="button" class="btn btn-link text-danger" style="padding:0; color:#dd4b39;"
                            data-id="<?=(int)$peso_['id']?>"
                            data-descricao="<?=htmlspecialchars($data . ' — ' . number_format((float)$peso_['peso'], 3, ',', '.') . ' kg', ENT_QUOTES, 'UTF-8')?>"
                            onclick="excluir_peso(this)" title="Excluir pesagem" aria-label="Excluir pesagem">
                      <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-7">
          <h4 style="font-size:16px; margin:5px 0 15px;">Gráfico de evolução</h4>
          <?php if (!$dadosGraficoPesagem): ?>
            <p class="text-muted"><?=$animal ? 'Nenhuma pesagem disponível para o gráfico.' : 'Selecione um animal para visualizar a evolução de peso.'?></p>
          <?php endif; ?>
          <div class="chart" id="line-chart" style="height:300px;" role="img" aria-label="Gráfico de evolução do peso em quilogramas; valores disponíveis na tabela de pesagens."></div>
        </div>
      </div>
    </div>
  </div>
</section>
