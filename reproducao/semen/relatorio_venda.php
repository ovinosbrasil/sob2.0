<script type="text/javascript">
function excluir_venda(id_venda){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/semen/palco_excluir_venda.php?id_venda="+id_venda;
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

function fechar_excluir(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_venda(id_venda){
    window.location.href = "reproducao/semen/_excluir_venda.php?id_venda="+id_venda;
}
</script>
<?

$data_inicial = $_POST['data_inicial'] ?? $_GET['data_inicial'] ?? date('01/m/Y');
$data_final = $_POST['data_final'] ?? $_GET['data_final'] ?? date('t/m/Y');
$validarData = function ($valor) {
  if (!is_string($valor) || !preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $valor, $partes)
      || !checkdate((int)$partes[2], (int)$partes[1], (int)$partes[3])) {
    return false;
  }
  return $partes[3] . '-' . $partes[2] . '-' . $partes[1];
};
$data_inicial_ = $validarData($data_inicial);
$data_final_ = $validarData($data_final);
$erroPeriodo = '';
if (!$data_inicial_ || !$data_final_) {
  $erroPeriodo = 'Informe datas válidas no formato dd/mm/aaaa.';
} elseif ($data_inicial_ > $data_final_) {
  $erroPeriodo = 'A data inicial deve ser anterior ou igual à data final.';
}
$data_inicial = is_string($data_inicial) ? $data_inicial : '';
$data_final = is_string($data_final) ? $data_final : '';
?>


<section class="content-header">
  <h1>
    Relatório de vendas de sêmens
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Banco de Sêmen</a></li>
    <li><a href="#">Relatório</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="geral.php?pg=vendas_semen" onsubmit="return validar_montar()">
          <!-- /.box-header -->
          <div class="box-body">

            <div class="form-group">
                <label for="exampleInputPassword1">Data inicial<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=htmlspecialchars($data_inicial, ENT_QUOTES, 'UTF-8')?>">
                </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data final<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_final" name="data_final" value="<?=htmlspecialchars($data_final, ENT_QUOTES, 'UTF-8')?>">
                </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary" style="width:100%; margin-top:4%;">Pesquisar</button>
            </div>

        </div>
      </form>
			</div>
      <!-- /.col -->

  </div>

    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <?php if ($erroPeriodo) { ?>
          <div class="alert alert-warning" role="alert"><?=$erroPeriodo?></div>
          <?php } ?>
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Data</th>
              <th>Sêmen</th>
              <th>Cliente</th>
              <th>Valor</th>
              <th>Parcelas</th>
              <th>Tipo</th>
              <th style="width:15%;">Funções</th>
            </tr>
            <?
            $venda = $erroPeriodo ? array() : (DBRead('venda_semen', "WHERE data >= '$data_inicial_' AND data <= '$data_final_' ORDER BY data ASC, id ASC") ?: array());
            if (!$venda && !$erroPeriodo) { ?>
              <tr><td colspan="7" class="text-center">Nenhuma venda encontrada no período.</td></tr>
            <?php }
            foreach($venda as $venda_){
              $id_embriao = $venda_['id_semen'];
              $embriao = DBRead('semen', "WHERE id = $id_embriao");

              $id_macho = $embriao[0]['id_animal'];
              if($embriao[0]['terceiro']){
                $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
              }else{
                $macho = DBRead('animais', "WHERE id = '$id_macho'");
              }

              $data = $venda_['data'];
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

              $id_comprador = $venda_['comprador'];
              $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
              ?>

              <tr>
                <td><?=$data?></td>
                <td><?=$macho[0]['nome']?></td>
                <td><?=$comprador[0]['nome']; ?></td>
                <td>R$ <?=number_format($venda_['valor'],2,",",".");?></td>
                <td><?=$venda_['parcelas']?>x</td>
                <td><?=$venda_['tipo_venda']?> - <?=$venda_['forma_de_pagamento']?></td>
                <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_venda(<?=$venda_['id']?>)">X</button></td>
              </tr>
            <? }  ?>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
