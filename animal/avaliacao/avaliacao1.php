<script type="text/javascript">

function ativar_avaliacao(){
  saida = 0;
  if( (!document.getElementById("tamanho1").checked) && (!document.getElementById("tamanho2").checked) && (!document.getElementById("tamanho3").checked) && (!document.getElementById("tamanho4").checked)){
    document.getElementById("titulo_pesagem").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_pesagem").style.color = "green";}

  if( (!document.getElementById("cabeca1").checked) && (!document.getElementById("cabeca2").checked) && (!document.getElementById("cabeca3").checked) && (!document.getElementById("cabeca4").checked)){
    document.getElementById("titulo_cabeca").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_cabeca").style.color = "green";}

  if( (!document.getElementById("pescoco1").checked) && (!document.getElementById("pescoco2").checked) && (!document.getElementById("pescoco3").checked) && (!document.getElementById("pescoco4").checked)){
    document.getElementById("titulo_pescoco").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_pescoco").style.color = "green";}

  if( (!document.getElementById("quarto_anterior1").checked) && (!document.getElementById("quarto_anterior2").checked) && (!document.getElementById("quarto_anterior3").checked) && (!document.getElementById("quarto_anterior4").checked)){
    document.getElementById("titulo_quarto_anterior").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_quarto_anterior").style.color = "green";}

  if( (!document.getElementById("barril1").checked) && (!document.getElementById("barril2").checked) && (!document.getElementById("barril3").checked) && (!document.getElementById("barril4").checked)){
    document.getElementById("titulo_barril").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_barril").style.color = "green";}

  if( (!document.getElementById("quarto_posterior1").checked) && (!document.getElementById("quarto_posterior2").checked) && (!document.getElementById("quarto_posterior3").checked) && (!document.getElementById("quarto_posterior4").checked)){
    document.getElementById("titulo_quarto_posterior").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_quarto_posterior").style.color = "green";}

  if( (!document.getElementById("comprimento1").checked) && (!document.getElementById("comprimento2").checked) && (!document.getElementById("comprimento3").checked) && (!document.getElementById("comprimento4").checked)){
    document.getElementById("titulo_comprimento").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_comprimento").style.color = "green";}

  if( (!document.getElementById("orgao1").checked) && (!document.getElementById("orgao2").checked) && (!document.getElementById("orgao3").checked) && (!document.getElementById("orgao4").checked)){
    document.getElementById("titulo_orgao").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_orgao").style.color = "green";}

  if( (!document.getElementById("distribuicao1").checked) && (!document.getElementById("distribuicao2").checked) && (!document.getElementById("distribuicao3").checked) && (!document.getElementById("distribuicao4").checked)){
    document.getElementById("titulo_distribuicao").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_distribuicao").style.color = "green";}

  if( (!document.getElementById("cobertura1").checked) && (!document.getElementById("cobertura2").checked) && (!document.getElementById("cobertura3").checked) && (!document.getElementById("cobertura4").checked)){
    document.getElementById("titulo_cobertura").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_cobertura").style.color = "green";}

  if( (!document.getElementById("cor1").checked) && (!document.getElementById("cor2").checked) && (!document.getElementById("cor3").checked) && (!document.getElementById("cor4").checked)){
    document.getElementById("titulo_cor").style.color = "red";
    saida = 1;
  }else{document.getElementById("titulo_cor").style.color = "green";}
  if(saida){ return false; }else{ return true; }
}
</script>


<?
$ava1 = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = 1");

$data = $animal[0]['data_de_nascimento'];
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
$data_nascimento = $data;

if($animal[0]['data2'] != '0000-00-00'){
$data = $animal[0]['data2'];
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
$data_apartacao = $data;

$time_inicial = geraTimestamp($data_nascimento);
$time_final = geraTimestamp($data_apartacao);
$diferenca = $time_final - $time_inicial;
$dias_apartacao = (int)floor( $diferenca / (60 * 60 * 24));

$gmd = ($animal[0]['peso2'] - $animal[0]['peso_inicial']) / $dias_apartacao*1000;
}
?>
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="col-md-3">
      <div class="form-group">
        <label for="exampleInputPassword1">Preenchimento automático</label>
        <select class="form-control select" id="automatico" name="automatico" onchange="preencher_automatico(this.value)">
          <option value="">Selecionar tipo</option>
          <option></option>
          <option value="5">Tipo 5</option>
          <option value="4">Tipo 4</option>
          <option value="3">Tipo 3</option>
          <option value="2">Tipo 2</option>
        </select>
      </div>
    </div>

    <div class="col-md-6" style="margin-left:-13px;">
    <div class="box-body">
      <div class="form-group" style="margin-top:0%;">
        <span style="color:red;">Atenção: O bom uso dessa função exige do usuário conhecimento na avaliação dos animais. Procure por Técnicos credenciados e utilize nosso manual.</span>
          <a href="animal/manual.pdf" target="_blank">DOWNLOAD MANUAL DE AVALIAÇÃO</a>
        </div>
      </div>
    </div>
</div>


<div class="col-md-12">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_pesagem">Avaliar Pesagem</h3>
      </div>
      <div class="box-body">
<form method="post" action="animal/avaliacao/_peso_apartacao.php?id_animal=<?=$id_animal?>" onsubmit="return ativar_apartacao()">
        <div class="col-md-4">
            <div class="col-md-6">
              <div class="form-group" style="margin-top:3%;">
                <label for="exampleInputPassword1">Data inicial (Nascimento)<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="data_nascimento" name="data_nascimento" value="<?=$data_nascimento?>" readonly="readonly">
              </div>

              <div class="form-group" style="margin-top:3%;">
                <label for="exampleInputPassword1">Data Apartação<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="data_apartacao" name="data_apartacao" value="<?=$data_apartacao?>">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group" style="margin-top:3%;">
                <label for="exampleInputPassword1">Peso inicial(kg)<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="peso_inicial" name="peso_inicial" value="<?=$animal[0]['peso_inicial']*100?>">
              </div>
              <div class="form-group" style="margin-top:3%;">
                <label for="exampleInputPassword1">Peso Apartação(kg)<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="peso_apartacao" name="peso_apartacao" value="<?=$animal[0]['peso2']*100?>">
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-5%;">
              <button type="submit" class="btn btn-primary" style="margin-top:6%; width:100%;">Calcular Média de peso</button>
            <label style="text-align:center; width:100%; margin-top:1%;">Ganho média diário (GMD):<span style="color:green;"> <?=number_format($gmd, 2, ',', '.')?> g</span></label>
            </div>
      </div>
</form>

      <div class="col-md-6">
        <?
        if($animal[0]['sexo'] == 'Fêmea'){
          if($dias_apartacao <= 90) {
            ?>
            <div class="col-md-12">
            Dicas de tipo para cada "Ganho Média Diário" para Fêmeas com até 90 dias:</br>
              Tipo 5 = GMD > 266,00g</br>
              Tipo 4 = GMD entre 233,00g e 266,00g</br>
              Tipo 3 = GMD entre 200,00g e 233,00g</br>
              Tipo 2 = GMD entre 166,00g e 200,00g</br>
              GMD* = Ganho Média Diário
            </div>
          <? }

          if($dias_apartacao > 90 && $dias_apartacao <= 210) {
            ?>
            <div class="col-md-12">
            Dicas de tipo para cada "Ganho Média Diário" para Fêmeas com idade entre 90 e 210 dias:</br>
              Tipo 5 = GMD > 226,00g</br>
              Tipo 4 = GMD entre 193,00g e 226,00g</br>
              Tipo 3 = GMD entre 160,00g e 193,00g</br>
              Tipo 2 = GMD entre 126,00g e 160,00g</br>
              GMD* = Ganho Média Diário
            </div>
          <? }

          if($dias_apartacao > 210 && $dias_apartacao <= 365) {
            ?>
            <div class="col-md-12">
            Dicas de tipo para cada "Ganho Média Diário" para fêmeas com com idade entre 210 e 365 dias:</br>
              Tipo 5 = GMD > 206,00g</br>
              Tipo 4 = GMD entre 173,00g e 206,00g</br>
              Tipo 3 = GMD entre 140,00g e 173,00g</br>
              Tipo 2 = GMD entre 106,00g e 140,00g</br>
              GMD* = Ganho Média Diário
            </div>
          <? }

          if($dias_apartacao > 365) {
            ?>
            <div class="col-md-12">
            Animais com idade maior que 365 Dias deverão ser pontuados com TIPO 4.
            </div>
          <? } } ?>

          <?
          if($animal[0]['sexo'] == 'Macho'){
            if($dias_apartacao <= 90) {
              ?>
              <div class="col-md-12">
              Dicas de tipo para cada "Ganho Média Diário" para machos com até 90 dias:</br>
                Tipo 5 = GMD > 276,00g</br>
                Tipo 4 = GMD entre 243,00g e 276,00g</br>
                Tipo 3 = GMD entre 210,00g e 243,00g</br>
                Tipo 2 = GMD entre 176,00g e 210,00g</br>
                GMD* = Ganho Média Diário
              </div>
            <? }

            if($dias_apartacao > 90 && $dias_apartacao <= 210) {
              ?>
              <div class="col-md-12">
              Dicas de tipo para cada "Ganho Média Diário" para machos com idade entre 90 e 210 dias:</br>
                Tipo 5 = GMD > 236,00g</br>
                Tipo 4 = GMD entre 203,00g e 236,00g</br>
                Tipo 3 = GMD entre 170,00g e 203,00g</br>
                Tipo 2 = GMD entre 136,00g e 170,00g</br>
                GMD* = Ganho Média Diário
              </div>
            <? }

            if($dias_apartacao > 210 && $dias_apartacao <= 365) {
              ?>
              <div class="col-md-12">
              Dicas de tipo para cada "Ganho Média Diário" para machos com com idade entre 210 e 365 dias:</br>
                Tipo 5 = GMD > 216,00g</br>
                Tipo 4 = GMD entre 183,00g e 216,00g</br>
                Tipo 3 = GMD entre 150,00g e 163,00g</br>
                Tipo 2 = GMD entre 116,00g e 150,00g</br>
                GMD* = Ganho Média Diário
              </div>
            <? }

            if($dias_apartacao > 365) {
              ?>
              <div class="col-md-12">
              Animais com idade maior que 365 Dias deverão ser pontuados com TIPO 4.
              </div>
            <? } } ?>


<form name="form1" method="post" action="animal/avaliacao/_avaliar.php?id_animal=<?=$id_animal?>&avaliacao=1" onsubmit="return ativar_avaliacao()">
        <div class="col-md-12">
          <div class="form-group" style="margin-top:3%;">
          <label for="exampleInputPassword1">Selecionar tipo</label><br/>
          <? if($ava1[0]['tamanho'] == 5){ ?> <input type="radio" id="tamanho1" name="tamanho" value="5" checked="checked"> <? }else{?> <input type="radio" id="tamanho1" name="tamanho" value="5"><? } ?>
          Tipo 5 (Muito bom) &nbsp;&nbsp;&nbsp;
          <? if($ava1[0]['tamanho'] == 4){ ?> <input type="radio" id="tamanho2" name="tamanho" value="4" checked="checked"> <? }else{?> <input type="radio" id="tamanho2" name="tamanho" value="4"><? } ?>Tipo 4 (Bom)&nbsp;&nbsp;&nbsp;
          <? if($ava1[0]['tamanho'] == 3){ ?> <input type="radio" id="tamanho3" name="tamanho" value="3" checked="checked"> <? }else{?> <input type="radio" id="tamanho3" name="tamanho" value="3"><? } ?>Tipo 3 (Ruim)&nbsp;&nbsp;&nbsp;
          <? if($ava1[0]['tamanho'] == 2){ ?> <input type="radio" id="tamanho4" name="tamanho" value="2" checked="checked"> <? }else{?> <input type="radio" id="tamanho4" name="tamanho" value="2"><? } ?>Tipo 2 (Descarte)&nbsp;&nbsp;&nbsp;
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_cabeca">Cabeça</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['cabeca'] == 5){ ?><input type="radio" id="cabeca1" name="cabeca" value="5" checked="checked"> <? }else{?> <input type="radio" id="cabeca1" name="cabeca" value="5"><? } ?>Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['cabeca'] == 4){ ?><input type="radio" id="cabeca2" name="cabeca" value="4" checked="checked"> <? }else{?> <input type="radio" id="cabeca2" name="cabeca" value="4"><? } ?>Tipo 4 (Bom)<br/>
        <? if($ava1[0]['cabeca'] == 3){ ?><input type="radio" id="cabeca3" name="cabeca" value="3" checked="checked"> <? }else{?> <input type="radio" id="cabeca3" name="cabeca" value="3"><? } ?>Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['cabeca'] == 2){ ?><input type="radio" id="cabeca4" name="cabeca" value="2" checked="checked"> <? }else{?> <input type="radio" id="cabeca4" name="cabeca" value="2"><? } ?>Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_pescoco">Pescoço</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['pescoco'] == 5){ ?><input type="radio" id="pescoco1" name="pescoco" value="5" checked="checked"><? }else{?><input type="radio" id="pescoco1" name="pescoco" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['pescoco'] == 4){ ?><input type="radio" id="pescoco2" name="pescoco" value="4" checked="checked"><? }else{?><input type="radio" id="pescoco2" name="pescoco" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['pescoco'] == 3){ ?><input type="radio" id="pescoco3" name="pescoco" value="3" checked="checked"><? }else{?><input type="radio" id="pescoco3" name="pescoco" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['pescoco'] == 2){ ?><input type="radio" id="pescoco4" name="pescoco" value="2" checked="checked"><? }else{?><input type="radio" id="pescoco4" name="pescoco" value="2"><? } ?> Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_quarto_anterior">Quarto anterior</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['quarto_anterior'] == 5){ ?><input type="radio" id="quarto_anterior1" name="quarto_anterior" value="5" checked="checked"><? }else{?><input type="radio" id="quarto_anterior1" name="quarto_anterior" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['quarto_anterior'] == 4){ ?><input type="radio" id="quarto_anterior2" name="quarto_anterior" value="4" checked="checked"><? }else{?><input type="radio" id="quarto_anterior2" name="quarto_anterior" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['quarto_anterior'] == 3){ ?><input type="radio" id="quarto_anterior3" name="quarto_anterior" value="3" checked="checked"><? }else{?><input type="radio" id="quarto_anterior3" name="quarto_anterior" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['quarto_anterior'] == 2){ ?><input type="radio" id="quarto_anterior4" name="quarto_anterior" value="2" checked="checked"><? }else{?><input type="radio" id="quarto_anterior4" name="quarto_anterior" value="2"><? } ?> Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_barril">Barril</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['barril'] == 5){ ?><input type="radio" id="barril1" name="barril" value="5" checked="checked"><? }else{?><input type="radio" id="barril1" name="barril" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['barril'] == 4){ ?><input type="radio" id="barril2" name="barril" value="4" checked="checked"><? }else{?><input type="radio" id="barril2" name="barril" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['barril'] == 3){ ?><input type="radio" id="barril3" name="barril" value="3" checked="checked"><? }else{?><input type="radio" id="barril3" name="barril" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['barril'] == 2){ ?><input type="radio" id="barril4" name="barril" value="2" checked="checked"><? }else{?><input type="radio" id="barril4" name="barril" value="2"><? } ?> Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_quarto_posterior">Quarto posterior</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['quarto_posterior'] == 5){ ?><input type="radio" id="quarto_posterior1" name="quarto_posterior" value="5" checked="checked"><? }else{?><input type="radio" id="quarto_posterior1" name="quarto_posterior" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['quarto_posterior'] == 4){ ?><input type="radio" id="quarto_posterior2" name="quarto_posterior" value="4" checked="checked"><? }else{?><input type="radio" id="quarto_posterior2" name="quarto_posterior" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['quarto_posterior'] == 3){ ?><input type="radio" id="quarto_posterior3" name="quarto_posterior" value="3" checked="checked"><? }else{?><input type="radio" id="quarto_posterior3" name="quarto_posterior" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['quarto_posterior'] == 2){ ?><input type="radio" id="quarto_posterior4" name="quarto_posterior" value="2" checked="checked"><? }else{?><input type="radio" id="quarto_posterior4" name="quarto_posterior" value="2"><? } ?> Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_comprimento">comprimento</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['comprimento'] == 5){ ?><input type="radio" id="comprimento1" name="comprimento" value="5" checked="checked"><? }else{?><input type="radio" id="comprimento1" name="comprimento" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['comprimento'] == 4){ ?><input type="radio" id="comprimento2" name="comprimento" value="4" checked="checked"><? }else{?><input type="radio" id="comprimento2" name="comprimento" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['comprimento'] == 3){ ?><input type="radio" id="comprimento3" name="comprimento" value="3" checked="checked"><? }else{?><input type="radio" id="comprimento3" name="comprimento" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['comprimento'] == 2){ ?><input type="radio" id="comprimento4" name="comprimento" value="2" checked="checked"><? }else{?><input type="radio" id="comprimento4" name="comprimento" value="2"><? } ?> Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_orgao">Orgão sexual</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['orgao'] == 5){ ?><input type="radio" id="orgao1" name="orgao" value="5" checked="checked"><? }else{?><input type="radio" id="orgao1" name="orgao" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['orgao'] == 4){ ?><input type="radio" id="orgao2" name="orgao" value="4" checked="checked"><? }else{?><input type="radio" id="orgao2" name="orgao" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['orgao'] == 3){ ?><input type="radio" id="orgao3" name="orgao" value="3" checked="checked"><? }else{?><input type="radio" id="orgao3" name="orgao" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['orgao'] == 2){ ?><input type="radio" id="orgao4" name="orgao" value="2" checked="checked"><? }else{?><input type="radio" id="orgao4" name="orgao" value="2"><? } ?>Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_distribuicao">D. de gordura</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['distribuicao'] == 5){ ?><input type="radio" id="distribuicao1" name="distribuicao" value="5" checked="checked"><? }else{?><input type="radio" id="distribuicao1" name="distribuicao" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['distribuicao'] == 4){ ?><input type="radio" id="distribuicao2" name="distribuicao" value="4" checked="checked"><? }else{?><input type="radio" id="distribuicao2" name="distribuicao" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['distribuicao'] == 3){ ?><input type="radio" id="distribuicao3" name="distribuicao" value="3" checked="checked"><? }else{?><input type="radio" id="distribuicao3" name="distribuicao" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['distribuicao'] == 2){ ?><input type="radio" id="distribuicao4" name="distribuicao" value="2" checked="checked"><? }else{?><input type="radio" id="distribuicao4" name="distribuicao" value="2"><? } ?> Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_cobertura">Cobertura</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['cobertura'] == 5){ ?><input type="radio" id="cobertura1" name="cobertura" value="5" checked="checked"><? }else{?><input type="radio" id="cobertura1" name="cobertura" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['cobertura'] == 4){ ?><input type="radio" id="cobertura2" name="cobertura" value="4" checked="checked"><? }else{?><input type="radio" id="cobertura2" name="cobertura" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['cobertura'] == 3){ ?><input type="radio" id="cobertura3" name="cobertura" value="3" checked="checked"><? }else{?><input type="radio" id="cobertura3" name="cobertura" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['cobertura'] == 2){ ?><input type="radio" id="cobertura4" name="cobertura" value="2" checked="checked"><? }else{?><input type="radio" id="cobertura4" name="cobertura" value="2"><? } ?> Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-2">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title" id="titulo_cor">Cor</h3>
      </div>
      <div class="box-body">
        <label for="exampleInputPassword1">Selecionar tipo</label><br/>
        <? if($ava1[0]['cor'] == 5){ ?><input type="radio" id="cor1" name="cor" value="5" checked="checked"><? }else{?><input type="radio" id="cor1" name="cor" value="5"><? } ?> Tipo 5 (Muito bom)<br/>
        <? if($ava1[0]['cor'] == 4){ ?><input type="radio" id="cor2" name="cor" value="4" checked="checked"><? }else{?><input type="radio" id="cor2" name="cor" value="4"><? } ?> Tipo 4 (Bom)<br/>
        <? if($ava1[0]['cor'] == 3){ ?><input type="radio" id="cor3" name="cor" value="3" checked="checked"><? }else{?><input type="radio" id="cor3" name="cor" value="3"><? } ?> Tipo 3 (Ruim)<br/>
        <? if($ava1[0]['cor'] == 2){ ?><input type="radio" id="cor4" name="cor" value="2" checked="checked"><? }else{?><input type="radio" id="cor4" name="cor" value="2"><? } ?> Tipo 2 (Descarte)<br/>
      </div>
    </div>
</div>

<div class="col-md-4">
  Obs.: O calculo de avaliação foi desenvolvido baseado em dados com padrão Sul-africano e ajuda de técnicos brasileiros.
  <button type="submit" class="btn btn-success" style="margin-top:6%; width:100%;">Avaliar animal</button>
</div>
</form>
