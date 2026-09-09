<?
include "../../_config.php";
$nome = $_GET['nome'];
?>

<table class="table table-bordered" id="tabela_padrao" width="70%">
  <tr>
    <th>Animal</th>
    <th>Sexo</th>
    <th>Data de nascimento</th>
    <th>Fbb</th>
    <th>Status</th>
  </tr>
  <?
  $animal = DBRead('animais',"WHERE nome LIKE '%$nome%' ORDER BY nome asc LIMIT 20");
  foreach ($animal as $animais) {
    $data = $animais['data_de_nascimento'];
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
  ?>
  <tr>
    <td><?=$animais['nome']?></td>
    <td><?=$animais['sexo']?></td>
    <td><?=$data_nascimento?></td>
    <td><?=$animais['fbb']?></td>
    <td>
      <?
      if($animais['chip'] < 1){ ?>
        <button type="button" class="btn btn-success" onclick="palco_cadastro(<?=$animais['id']?>)">Cadastrar chip</button>
      <?  }else{ ?>
      <a href="chip/animal/_excluir.php?id_animal=<?=$animais['id']?>"><button type="button" class="btn btn-warning" >Excluir chip</button></a>
    <? } ?>
    </td>
  </tr>
  <? } ?>
</table>
