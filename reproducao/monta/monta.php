<? $id_lote = $_GET['id_lote']; ?>
<script type="text/javascript">
function focus(){
  document.getElementById("mae").focus();
}

function validar_montar() {
  var campos = {lote:'Lote', data_inicial:'Data inicial', data_final:'Data final', pai:'Macho', raca:'Raça', notificacao:'Notificação'};
  var faltantes = [], primeiro;
  Object.keys(campos).forEach(function (id) {
    var campo = document.getElementById(id);
    var vazio = !campo.value.trim();
    campo.style.borderColor = vazio ? '#dd4b39' : '';
    campo.setAttribute('aria-invalid', String(vazio));
    if (vazio) { faltantes.push(campos[id]); primeiro = primeiro || campo; }
  });
  if (faltantes.length) { SobAlertas.camposObrigatorios(faltantes); primeiro.focus(); return false; }
  return true;
}

function ativar_femea(){
  var campo = document.getElementById('mae');
  var vazio = !campo.value.trim();
  campo.style.borderColor = vazio ? '#dd4b39' : '';
  campo.setAttribute('aria-invalid', String(vazio));
  if (vazio) { SobAlertas.camposObrigatorios(['Adicionar fêmea']); campo.focus(); return false; }
  return true;
}

function cadastrar_monta(x,id_lote,tipo){
  if(x){
    window.location.href = "geral.php?pg=cadastrar_nascimento&x="+x+"&id_lote="+id_lote+"&tipo="+tipo;
  }
}

function excluir_femea(id_mae){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/monta/palco_excluir_femea.php?id_mae="+id_mae+"&id_lote=+<?=$id_lote?>";
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_excluir").innerHTML = resposta;
}
}
PP.send(null);
document.getElementById("transparencia").style.display = 'block';
document.getElementById("palco_excluir").style.display = 'block';
}

function fechar_excluir_femea(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_femea(id_mae,id_lote){
    window.location.href = "reproducao/monta/_excluir_femea.php?id_mae="+id_mae+"&id_lote=<?=$id_lote?>";
}

function cadastrar_ultrassom(status, id_lote){
    window.location.href = "reproducao/monta/_cadastrar_ultrassom.php?id_lote="+id_lote+"&status="+status;
}

function excluir_lote(){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/monta/palco_excluir_lote.php?id_lote=<?=$id_lote?>";
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_excluir").innerHTML = resposta;
}
}
PP.send(null);
document.getElementById("transparencia").style.display = 'block';
document.getElementById("palco_excluir").style.display = 'block';
}

function fechar_excluir_lote(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_lote(id_lote){
    window.location.href = "reproducao/monta/_excluir_lote.php?id_lote="+id_lote;
}

document.addEventListener('buscaanimais:selecionado', function (evento) {
  if (!evento.target.classList.contains('busca-femea-monta')) { return; }
  var formulario = document.getElementById('form-adicionar-femea-monta');
  if (!formulario) { return; }
  if (typeof formulario.requestSubmit === 'function') {
    formulario.requestSubmit();
  } else if (ativar_femea()) {
    formulario.submit();
  }
});

function abrirFemeaMonta(id, terceiro) {
  var pagina = terceiro ? 'terceiro' : 'animal';
  window.location.href = 'geral.php?pg=' + pagina + '&id_animal=' + encodeURIComponent(id);
}

function confirmarExclusaoFemeaMonta(botao) {
  confirmarExclusao({
    titulo: 'Excluir fêmea do lote?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'A fêmea será removida deste lote de monta natural.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/monta/_excluir_femea.php?id_controle=' + encodeURIComponent(botao.getAttribute('data-id-controle')) + '&id_lote=<?=(int)$id_lote?>';
    }
  });
}

function confirmarExclusaoLoteMontaDetalhe(botao) {
  confirmarExclusao({
    titulo: 'Excluir lote de monta natural?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'Confirme se deseja excluir este lote. Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/monta/_excluir_lote.php?id_lote=<?=(int)$id_lote?>';
    }
  });
}
</script>

<?
$id_lote = $_GET['id_lote'];
$monta = DBRead('monta', "WHERE id = '$id_lote'");
$id_macho = $monta[0]['id_animal'];
if(!$monta[0]['terceiro']){
  $macho = DBRead('animais', "WHERE id = '$id_macho'");
}else{
  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
}
$data = $monta[0]['data_inicio'];
$data_atual = $data;
$data = '0';
$data['0'] = $data_atual['8'];
$data['1'] = $data_atual['9'];
$data['2'] = "/";
$data['3'] = $data_atual['5'];
$data['4'] = $data_atual['6'];
$data['5'] = "/";
$data['6'] = $data_atual['0'];
$data['7'] = $data_atual['1'];
$data['8'] = $data_atual['2'];
$data['9'] = $data_atual['3'];
$data_inicial = $data;

$data = $monta[0]['data_fim'];
$data_atual = $data;
$data = '0';
$data['0'] = $data_atual['8'];
$data['1'] = $data_atual['9'];
$data['2'] = "/";
$data['3'] = $data_atual['5'];
$data['4'] = $data_atual['6'];
$data['5'] = "/";
$data['6'] = $data_atual['0'];
$data['7'] = $data_atual['1'];
$data['8'] = $data_atual['2'];
$data['9'] = $data_atual['3'];
$data_final = $data;
?>


<section class="content-header">
  <h1>Exibir Monta natural</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li class="active">Exibir Monta natural</li>
  </ol>
</section>

<section class="content">
  <div class="box" style="border-top:0;">
    <form method="post" action="reproducao/monta/_alterar.php?id_lote=<?=(int)$id_lote?>" onsubmit="return validar_montar()" novalidate>
      <div class="box-body">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="lote">Lote<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="lote" name="lote" value="<?=htmlspecialchars($monta[0]['codigo'], ENT_QUOTES, 'UTF-8')?>">
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="data_inicial">Data inicial<span class="text-danger">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$data_inicial?>">
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="data_final">Data final<span class="text-danger">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" class="form-control pull-right" id="data_final" name="data_final" value="<?=$data_final?>">
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group" style="position:relative;">
              <label for="pai">Macho<span class="text-danger">*</span>
                <a href="geral.php?pg=cadastrar_animal&amp;tipo=2" target="_blank" rel="noopener"><span style="font-size:11px; color:green;">Novo</span></a>
              </label>
              <input type="text" class="form-control" id="pai" name="macho" onkeyup="pesquisar_pai(this.value)" value="<?=htmlspecialchars($macho[0]['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>">
              <div id="lista_pai" style="border:1px solid #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;"></div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="raca">Raça<span class="text-danger">*</span></label>
              <select class="form-control select" id="raca" name="raca">
                <option value="<?=htmlspecialchars($monta[0]['raca'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($monta[0]['raca'], ENT_QUOTES, 'UTF-8')?></option>
                <?php $racas = DBRead('raca', 'ORDER BY nome ASC') ?: array(); foreach ($racas as $raca_): ?>
                <option value="<?=htmlspecialchars($raca_['nome'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($raca_['nome'], ENT_QUOTES, 'UTF-8')?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="notificacao">Notificação<span class="text-danger">*</span></label>
              <select class="form-control select" id="notificacao" name="notificacao">
                <option value="<?=htmlspecialchars($monta[0]['notificacao'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($monta[0]['notificacao'], ENT_QUOTES, 'UTF-8')?></option>
                <option value="PO">PO</option>
                <option value="PC">PC</option>
              </select>
            </div>
          </div>
          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-warning">Alterar lote</button>
            <button type="button" class="btn btn-danger" data-nome="<?=htmlspecialchars('Lote ' . $monta[0]['codigo'], ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoLoteMontaDetalhe(this)"><i class="fa fa-trash-o" aria-hidden="true"></i> Excluir lote</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-body">
      <form id="form-adicionar-femea-monta" method="post" action="reproducao/monta/_cadastrar_femea.php?id_lote=<?=(int)$id_lote?>" onsubmit="return ativar_femea()" novalidate>
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="col-sm-12 col-md-6">
            <div class="form-group">
              <?php renderBuscaAnimais(array(
                  'id' => 'mae',
                  'name' => 'mae',
                  'label' => 'Adicionar fêmea',
                  'tipo' => 'femeas',
                  'required' => true,
                  'limite_origem' => 5,
                  'classe' => 'busca-femea-monta'
              )); ?>
            </div>
          </div>
        </div>
      </form>

      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin:5px 0 15px;">
        <h3 class="box-title" style="font-size:16px; margin:0;">Fêmeas do lote</h3>
        <a href="reproducao/monta/_imprimir.php?id_lote=<?=(int)$id_lote?>" target="_blank" rel="noopener" class="btn btn-primary"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Gerar PDF</a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr>
            <th style="width:45px;">Nº</th>
            <th>Fêmea</th>
            <th>Tipo</th>
            <th style="width:20%;">Ultrassom</th>
            <th style="width:20%;">Nascimento</th>
            <th style="width:1%;"><span class="sr-only">Ações</span></th>
          </tr></thead>
          <tbody>
          <?php
          $controlesMonta = DBRead('monta_controle', "WHERE id_monta = '" . (int)$id_lote . "' ORDER BY id ASC") ?: array();
          if (!$controlesMonta): ?>
            <tr><td colspan="6" class="text-center">Nenhuma fêmea adicionada ao lote.</td></tr>
          <?php endif; ?>
          <?php foreach ($controlesMonta as $indice => $controle):
            $idAnimal = (int)$controle['id_animal'];
            $ehTerceiro = !empty($controle['terceiro']);
            $cadastroFemea = DBRead($ehTerceiro ? 'terceiros' : 'animais', "WHERE id = '$idAnimal'") ?: array();
            if (!$cadastroFemea) { continue; }
            $femea = $cadastroFemea[0];
          ?>
            <tr>
              <td><?=$indice + 1?></td>
              <td onclick="abrirFemeaMonta(<?=$idAnimal?>, <?=$ehTerceiro ? 1 : 0?>)" style="cursor:pointer;" title="Abrir animal"><?=htmlspecialchars($femea['nome'], ENT_QUOTES, 'UTF-8')?><?=$ehTerceiro ? ' (Terceiro)' : ''?></td>
              <td><?=htmlspecialchars($femea['tipo'] ?? '--', ENT_QUOTES, 'UTF-8')?></td>
              <td>
                <?php
                $ultrassomPositivo = (int)$controle['ultrassom'] === 1;
                $ultrassomNegativo = (int)$controle['ultrassom'] === 2;
                $proximoUltrassom = $ultrassomPositivo ? 2 : 1;
                $textoUltrassom = $ultrassomPositivo ? 'Positivo' : ($ultrassomNegativo ? 'Negativo' : 'Não informado');
                $iconeUltrassom = $ultrassomPositivo ? 'fa-check' : ($ultrassomNegativo ? 'fa-times' : 'fa-minus');
                $corUltrassom = $ultrassomPositivo ? '#008d4c' : ($ultrassomNegativo ? '#dd4b39' : '#777');
                ?>
                <div class="sob-controle-status">
                  <button type="button"
                          class="sob-interruptor"
                          role="switch"
                          aria-checked="<?=$ultrassomPositivo ? 'true' : 'false'?>"
                          aria-label="Ultrassom de <?=htmlspecialchars($femea['nome'], ENT_QUOTES, 'UTF-8')?>: <?=$textoUltrassom?>"
                          title="Alterar para <?=$proximoUltrassom === 1 ? 'positivo' : 'negativo'?>"
                          onclick="cadastrar_ultrassom(<?=$proximoUltrassom?>, <?=(int)$controle['id']?>)">
                    <span class="sob-interruptor__indicador"><i class="fa <?=$iconeUltrassom?>" aria-hidden="true"></i></span>
                  </button>
                  <span class="sob-controle-status__texto" style="color:<?=$corUltrassom?>;"><?=$textoUltrassom?></span>
                </div>
              </td>
              <td>
                <?php if ($ultrassomNegativo): ?>
                  <span class="text-muted">--</span>
                <?php else: ?>
                  <select class="form-control" onchange="cadastrar_monta(this.value, <?=(int)$controle['id']?>, '1')" style="<?=$controle['status_nascimento'] ? 'color:green;' : ''?>">
                    <option value=""><?=$controle['status_nascimento'] ? 'Cria cadastrada' : 'Selecionar'?></option>
                    <option value="1">Parto simples</option>
                    <option value="2">Parto duplo</option>
                    <option value="3">Parto triplo</option>
                  </select>
                <?php endif; ?>
              </td>
              <td style="white-space:nowrap;">
                <button type="button" class="text-danger" style="background:none; border:0; padding:0;" data-id-controle="<?=(int)$controle['id']?>" data-nome="<?=htmlspecialchars($femea['nome'], ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoFemeaMonta(this)" title="Excluir fêmea" aria-label="Excluir fêmea"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
