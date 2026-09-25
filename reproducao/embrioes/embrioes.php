<?php
$porPagina = 20;
$contagem = DBRead('embriao', 'WHERE qtd > 0', 'COUNT(*) AS total');
$totalEmbrioes = (int)($contagem[0]['total'] ?? 0);
$totalPaginas = max(1, (int)ceil($totalEmbrioes / $porPagina));
$paginaInformada = filter_var($_GET['pag'] ?? 0, FILTER_VALIDATE_INT);
$pagina = min(max(0, $paginaInformada === false ? 0 : $paginaInformada), $totalPaginas - 1);
$offset = $pagina * $porPagina;
$embriao = $totalEmbrioes > 0
  ? (DBRead('embriao', "WHERE qtd > 0 ORDER BY data DESC, id DESC LIMIT $offset,$porPagina") ?: array())
  : array();
?>
<script type="text/javascript">
function validar(){
  saida = 0;
  if(!document.getElementById("macho").value){
    document.getElementById("macho").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("macho").style.border = "1px solid green";}

  if(!document.getElementById("data").value){
    document.getElementById("data").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data").style.border = "1px solid green";}

  if(!document.getElementById("femea").value){
    document.getElementById("femea").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("femea").style.border = "1px solid green";}

  if(!document.getElementById("qtd").value){
    document.getElementById("qtd").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("qtd").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}


function pesquisar_pai_monta(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/monta/lista_pai.php?nome="+encodeURIComponent(nome);
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_pai").innerHTML = resposta;
document.getElementById("lista_pai").style.display = 'block';
}
}
PP.send(null);
}

function fechar_lista_pai(){
  document.getElementById("lista_pai").style.display = 'none';
}

function linkar_pai_monta(nome){
  document.getElementById("lista_pai").style.display = 'none';
  document.getElementById("macho").value = nome;
}

function pesquisar_mae_monta(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/monta/lista_mae.php?nome="+encodeURIComponent(nome);
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_mae").innerHTML = resposta;
document.getElementById("lista_mae").style.display = 'block';
}
}
PP.send(null);
}

function fechar_lista_mae(){
  document.getElementById("lista_mae").style.display = 'none';
}

function linkar_mae_monta(nome){
  document.getElementById("lista_mae").style.display = 'none';
  document.getElementById("femea").value = nome;
}

function atualizar_embriao(tipo, id_embriao){
  if(tipo == 1){
    alterar_embriao(id_embriao);
  }
  if(tipo == 2){
    excluir_embriao(id_embriao);
  }
  if(tipo == 3){
    window.location.href = "geral.php?pg=vender_embrioes&id_embriao="+id_embriao;
  }
}

function excluir_embriao(id_embriao){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/embrioes/palco_excluir.php?id_embriao="+id_embriao;
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

function ativar_excluir(id_embriao){
    window.location.href = "reproducao/embrioes/_excluir.php?id_embriao="+id_embriao;
}

function alterar_embriao(id_embriao){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/embrioes/palco_alterar.php?id_embriao="+id_embriao;
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

function fechar_alterar(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}
</script>


<section class="content-header">
  <h1>
    Banco de embriões
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Embriões</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <form method="post" action="reproducao/embrioes/_cadastrar.php" onsubmit="return validar()">
            <div class="form-group">
                <label for="exampleInputPassword1">Macho<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="macho" name="macho" value="<?=htmlspecialchars($pai ?? '', ENT_QUOTES, 'UTF-8')?>" onKeyUp="pesquisar_pai_monta(this.value)">
                <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
            </div>
          </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Fêmea<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="femea" name="femea" value="<?=htmlspecialchars($mae ?? '', ENT_QUOTES, 'UTF-8')?>" onKeyUp="pesquisar_mae_monta(this.value)">
                <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
            </div>
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="data" name="data">
              </div>
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">Quantidade<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="qtd" name="qtd">
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">Botijão<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="botijao" name="botijao">
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">ID Palheta</label>
              <input type="text" class="form-control" id="palheta" name="palheta">
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">Qualidade</label>
              <input type="text" class="form-control" id="qualidade" name="qualidade">
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-success" style="width:100%;">Adicionar Embriões</button>
          </div>
        </form>
      </div>
    </div>
  <!-- /.box-body -->
</div>
<!-- /.col -->



      <div class="col-md-9">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">

            <div class="form-group" style="float:right;">
              <a href="geral.php?pg=vendas_embriao"><button type="button" class="btn btn-primary">Lista de vendas</button></a>
            </div>

            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Data</th>
                <th>Macho</th>
                <th>Fêmea</th>
                <th>Quantidade</th>
                <th>Botijão</th>
                <th>ID Palheta</th>
                <th>Qualidade</th>
                <th>Funções</th>
              </tr>
              <?php if (!$embriao) { ?>
                <tr><td colspan="8" class="text-center">Nenhum embrião disponível no banco.</td></tr>
              <?php }
                foreach ($embriao as $embriao_){
                $id_macho = $embriao_['pai'];
                if($embriao_['terceiro']){
                  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
                }else{
                  $macho = DBRead('animais', "WHERE id = '$id_macho'");
                }

                $id_femea = $embriao_['mae'];
                if($embriao_['terceiro']){
                  $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
                }else{
                  $femea = DBRead('animais', "WHERE id = '$id_femea'");
                }

                $data = 'Não informada';
                if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $embriao_['data'] ?? '', $partes)
                    && checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
                  $data = $partes[3] . '/' . $partes[2] . '/' . $partes[1];
                }
                ?>
                <tr>
                    <td><?=$data?></td>
                    <td><?=htmlspecialchars($macho[0]['nome'] ?? 'Animal não encontrado', ENT_QUOTES, 'UTF-8')?></td>
                    <td><?=htmlspecialchars($femea[0]['nome'] ?? 'Animal não encontrado', ENT_QUOTES, 'UTF-8')?></td>
                    <td><?=$embriao_['qtd']?></td>
                    <td><?=$embriao_['botijao']?></td>
                    <td><?=$embriao_['palheta']?></td>
                    <td><?=$embriao_['qualidade']?></td>
                    <td>
                      <select name="" id="" onchange="atualizar_embriao(this.value,<?=$embriao_['id']?>)" style="margin:0%; padding:0%; height:30px;">
                      <option value="">Selecionar</option>
                      <option value=""></option>
                      <option value="1">Alterar</option>
                      <option value="3">Vender</option>
                      <option value="2">Excluir</option>
                    </select>
                  </td>
                  </tr>
                <? } ?>
                </table>
                <?php if ($totalEmbrioes > 0) { ?>
                <div class="box-footer clearfix">
                  <span>Exibindo <?=$offset + 1?> a <?=min($offset + count($embriao), $totalEmbrioes)?> de <?=$totalEmbrioes?> registros</span>
                  <?php if ($totalPaginas > 1) { ?>
                  <nav class="pull-right" aria-label="Paginação do banco de embriões">
                    <ul class="pagination pagination-sm no-margin">
                      <?php if ($pagina > 0) { ?>
                      <li><a href="geral.php?pg=embrioes&amp;pag=0">Primeira</a></li>
                      <li><a href="geral.php?pg=embrioes&amp;pag=<?=$pagina - 1?>">Anterior</a></li>
                      <?php } ?>
                      <?php for ($numero = max(0, $pagina - 2); $numero <= min($totalPaginas - 1, $pagina + 2); $numero++) { ?>
                        <?php if ($numero === $pagina) { ?>
                        <li class="active"><span aria-current="page"><?=$numero + 1?></span></li>
                        <?php } else { ?>
                        <li><a href="geral.php?pg=embrioes&amp;pag=<?=$numero?>"><?=$numero + 1?></a></li>
                        <?php } ?>
                      <?php } ?>
                      <?php if ($pagina < $totalPaginas - 1) { ?>
                      <li><a href="geral.php?pg=embrioes&amp;pag=<?=$pagina + 1?>">Próxima</a></li>
                      <li><a href="geral.php?pg=embrioes&amp;pag=<?=$totalPaginas - 1?>">Última</a></li>
                      <?php } ?>
                    </ul>
                  </nav>
                  <?php } ?>
                </div>
                <?php } ?>
          </div>
          <!-- /.box-body -->
        </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
</section>
  <!-- /.content -->
