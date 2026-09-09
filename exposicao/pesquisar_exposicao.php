
<script type="text/javascript">
function pesquisar_evento(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "exposicao/lista_evento.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_evento").innerHTML = resposta;
document.getElementById("lista_evento").style.display = 'block';
}
}
PP.send(null);
}

function linkar_evento(id){
  window.location.href = "geral.php?pg=exposicao&id_exposicao="+id;
}

function fechar_lista_evento(){
  document.getElementById("lista_evento").style.display = 'none';
}
</script>

<section class="content-header">
  <h1>
    Exposição
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-trophy"></i> Exposição</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-5">
				<div class="box box-success">
          <form method="post" action="exposicao/_alterar.php?id_evento=<?=$id_evento?>" onsubmit="return validar()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Evento<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="evento" name="evento" onKeyUp="pesquisar_evento(this.value)" placeholder="Digite aqui...">
                <div id="lista_evento" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;">
                </div>
            </div>

        </div>
      </form>
			</div>
      <!-- /.col -->
    </div>


  </div>
</section>
  <!-- /.content -->
