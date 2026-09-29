<?php
require_once __DIR__ . '/../../includes/busca_animais.php';

function ultraLista($resultado) { return is_array($resultado) ? $resultado : array(); }
function ultraRegistro($tabela, $id) {
    $id = (int)$id;
    if ($id <= 0) return null;
    $dados = ultraLista(DBRead($tabela, "WHERE id = '{$id}' LIMIT 1"));
    return isset($dados[0]) ? $dados[0] : null;
}
function ultraAnimal($id, $terceiro) { return ultraRegistro($terceiro ? 'terceiros' : 'animais', $id); }
function ultraNome($id, $terceiro) {
    $animal = ultraAnimal($id, $terceiro);
    return $animal && !empty($animal['nome']) ? $animal['nome'] : '--';
}
function ultraData($data) {
    $data = trim((string)$data);
    if ($data === '' || $data === '0000-00-00') return '--';
    $objeto = DateTime::createFromFormat('Y-m-d', substr($data, 0, 10));
    return $objeto ? $objeto->format('d/m/Y') : $data;
}
function ultraH($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }

$tipo = isset($_GET['tipo']) && (string)$_GET['tipo'] === '1' ? 1 : 0;
$reproducao = isset($_GET['reproducao']) && in_array((string)$_GET['reproducao'], array('0','1','2'), true) ? (int)$_GET['reproducao'] : null;
$idLote = filter_input(INPUT_GET, 'id_lote', FILTER_VALIDATE_INT);
$idLote = $idLote && $idLote > 0 ? (int)$idLote : 0;
$idAnimal = filter_input(INPUT_GET, 'id_animal', FILTER_VALIDATE_INT);
$idAnimal = $idAnimal && $idAnimal > 0 ? (int)$idAnimal : 0;
$origemAnimal = isset($_GET['animal_origem']) && is_string($_GET['animal_origem']) ? $_GET['animal_origem'] : '';
$terceiro = $origemAnimal !== '' ? ($origemAnimal === 'terceiros' ? 1 : 0) : (isset($_GET['terceiro']) && (string)$_GET['terceiro'] === '1' ? 1 : 0);
$animalSelecionado = $idAnimal ? ultraAnimal($idAnimal, $terceiro) : null;

$buscarCadastro = isset($_GET['buscar_cadastro']) && (string)$_GET['buscar_cadastro'] === '1';
$cadFemea = isset($_GET['cad_femea']) ? trim((string)$_GET['cad_femea']) : '';
$cadFemeaId = filter_var($_GET['cad_femea_id'] ?? 0, FILTER_VALIDATE_INT);
$cadFemeaId = $cadFemeaId && $cadFemeaId > 0 ? (int)$cadFemeaId : 0;
$cadOrigem = isset($_GET['cad_femea_origem']) && in_array($_GET['cad_femea_origem'], array('rebanho','terceiros','receptora'), true) ? $_GET['cad_femea_origem'] : '';
$cadSituacao = isset($_GET['cad_situacao']) && in_array((string)$_GET['cad_situacao'], array('1','2'), true) ? (int)$_GET['cad_situacao'] : 0;
$cadDataTexto = isset($_GET['cad_data']) ? trim((string)$_GET['cad_data']) : date('d/m/Y');
$cadData = DateTimeImmutable::createFromFormat('!d/m/Y', $cadDataTexto);
$cadData = $cadData && $cadData->format('d/m/Y') === $cadDataTexto ? $cadData : null;
$cadLotes = array(); $cadErro = '';
if ($buscarCadastro) {
    if (!$cadFemeaId || !$cadOrigem || !$cadSituacao || !$cadData) {
        $cadErro = 'Selecione a fêmea e preencha corretamente a situação e a data.';
    } elseif ($cadOrigem === 'receptora') {
        $receptora = ultraRegistro('receptora', $cadFemeaId);
        if (!$receptora || empty($receptora['ativo'])) { $cadErro = 'A receptora selecionada não foi encontrada.'; }
        else foreach (ultraLista(DBRead('transplante_controle', "WHERE id_receptora = '{$cadFemeaId}' AND ultrassom = '0' ORDER BY id DESC")) as $controle) {
            $lote = ultraRegistro('transplante', $controle['id_lote'] ?? 0); if (!$lote) continue;
            $cadLotes[] = array('tipo'=>2,'controle'=>(int)$controle['id'],'lote'=>(int)$lote['id'],'codigo'=>$lote['codigo']??$lote['id'],'reproducao'=>'Transplante de embriões','macho'=>ultraNome($lote['id_pai']??0,!empty($lote['terceiro_pai'])),'data'=>$lote['data']??'');
        }
    } else {
        $cadTerceiro = $cadOrigem === 'terceiros' ? 1 : 0;
        $cadAnimal = ultraAnimal($cadFemeaId, $cadTerceiro);
        if (!$cadAnimal || ($cadAnimal['sexo'] ?? '') !== 'Fêmea') { $cadErro = 'A fêmea selecionada não foi encontrada.'; }
        else {
            foreach (ultraLista(DBRead('monta_controle', "WHERE id_animal = '{$cadFemeaId}' AND terceiro = '{$cadTerceiro}' AND ultrassom = '0' ORDER BY id DESC")) as $controle) {
                $lote = ultraRegistro('monta', $controle['id_monta'] ?? 0); if (!$lote) continue;
                $cadLotes[] = array('tipo'=>0,'controle'=>(int)$controle['id'],'lote'=>(int)$lote['id'],'codigo'=>$lote['codigo']??$lote['id'],'reproducao'=>'Monta natural','macho'=>ultraNome($lote['id_animal']??0,!empty($lote['terceiro'])),'data'=>$lote['data_inicio']??'');
            }
            foreach (ultraLista(DBRead('inseminacao_controle', "WHERE id_femea = '{$cadFemeaId}' AND terceiro = '{$cadTerceiro}' AND ultrassom = '0' ORDER BY id DESC")) as $controle) {
                $lote = ultraRegistro('inseminacao', $controle['id_lote'] ?? 0); if (!$lote) continue;
                $cadLotes[] = array('tipo'=>1,'controle'=>(int)$controle['id'],'lote'=>(int)$lote['id'],'codigo'=>$lote['codigo']??$lote['id'],'reproducao'=>'Inseminação artificial','macho'=>ultraNome($lote['id_macho']??0,!empty($lote['terceiro'])),'data'=>$lote['data']??'');
            }
        }
    }
}

$configuracoes = array(
    0 => array('tabela'=>'monta', 'controle'=>'monta_controle', 'fk'=>'id_monta', 'macho'=>'id_animal', 'terceiro'=>'terceiro'),
    1 => array('tabela'=>'inseminacao', 'controle'=>'inseminacao_controle', 'fk'=>'id_lote', 'macho'=>'id_macho', 'terceiro'=>'terceiro'),
    2 => array('tabela'=>'transplante', 'controle'=>'transplante_controle', 'fk'=>'id_lote', 'macho'=>'id_pai', 'terceiro'=>'terceiro_pai'),
);
$lotes = array(); $linhas = array(); $tituloResultado = ''; $mensagem = '';

if ($tipo === 1 && $reproducao !== null) {
    $config = $configuracoes[$reproducao];
    $lotes = ultraLista(DBRead($config['tabela'], 'ORDER BY id DESC'));
    if ($idLote) {
        $lote = ultraRegistro($config['tabela'], $idLote);
        if (!$lote) {
            $mensagem = 'O lote selecionado não foi encontrado.';
        } else {
            $tituloResultado = 'Lote: ' . ($lote['codigo'] ?? $idLote) . ' — Macho: ' . ultraNome($lote[$config['macho']] ?? 0, !empty($lote[$config['terceiro']]));
            if ($reproducao === 2) $tituloResultado .= ' — Fêmea: ' . ultraNome($lote['id_mae'] ?? 0, !empty($lote['terceiro_mae']));
            $controles = ultraLista(DBRead($config['controle'], "WHERE {$config['fk']} = '{$idLote}' ORDER BY id ASC"));
            foreach ($controles as $controle) {
                $femeaTerceira = false;
                if ($reproducao === 0) {
                    $femeaTerceira = !empty($controle['terceiro']);
                    $nome = ultraNome($controle['id_animal'] ?? 0, $femeaTerceira);
                } elseif ($reproducao === 1) {
                    $femeaTerceira = !empty($controle['terceiro']);
                    $nome = ultraNome($controle['id_femea'] ?? 0, $femeaTerceira);
                } else {
                    $nome = !empty($controle['receptora']) ? $controle['receptora'] : '--';
                }
                if ($femeaTerceira && $nome !== '--') { $nome .= ' (Terceiro)'; }
                $linhas[] = array('controle_id'=>(int)$controle['id'], 'lote_id'=>$idLote, 'reproducao'=>$reproducao, 'nome'=>$nome, 'data_ultrassom'=>ultraData($controle['data_ultrassom'] ?? ''), 'status'=>(int)($controle['ultrassom'] ?? 0));
            }
            if (!$linhas) $mensagem = 'Nenhuma fêmea foi adicionada a este lote.';
        }
    }
} elseif ($tipo === 0 && $animalSelecionado) {
    $tituloResultado = 'Histórico de ultrassom: ' . ($animalSelecionado['nome'] ?? 'Animal');
    foreach (ultraLista(DBRead('monta_controle', "WHERE id_animal = '{$idAnimal}' AND terceiro = '{$terceiro}' ORDER BY id DESC")) as $controle) {
        $lote = ultraRegistro('monta', $controle['id_monta'] ?? 0); if (!$lote) continue;
        $linhas[] = array('controle_id'=>(int)$controle['id'], 'lote_id'=>(int)$lote['id'], 'reproducao'=>0, 'tipo'=>'Monta natural', 'lote'=>$lote['codigo'] ?? '--', 'data'=>ultraData($lote['data_inicio'] ?? '').' a '.ultraData($lote['data_fim'] ?? ''), 'receptora'=>'--', 'data_ultrassom'=>ultraData($controle['data_ultrassom'] ?? ''), 'status'=>(int)($controle['ultrassom'] ?? 0));
    }
    foreach (ultraLista(DBRead('inseminacao_controle', "WHERE id_femea = '{$idAnimal}' AND terceiro = '{$terceiro}' ORDER BY id DESC")) as $controle) {
        $lote = ultraRegistro('inseminacao', $controle['id_lote'] ?? 0); if (!$lote) continue;
        $linhas[] = array('controle_id'=>(int)$controle['id'], 'lote_id'=>(int)$lote['id'], 'reproducao'=>1, 'tipo'=>'Inseminação artificial', 'lote'=>$lote['codigo'] ?? '--', 'data'=>ultraData($lote['data'] ?? ''), 'receptora'=>'--', 'data_ultrassom'=>ultraData($controle['data_ultrassom'] ?? ''), 'status'=>(int)($controle['ultrassom'] ?? 0));
    }
    foreach (ultraLista(DBRead('transplante', "WHERE id_mae = '{$idAnimal}' AND terceiro_mae = '{$terceiro}' ORDER BY id DESC")) as $lote) {
        $loteId = (int)$lote['id'];
        foreach (ultraLista(DBRead('transplante_controle', "WHERE id_lote = '{$loteId}' ORDER BY id ASC")) as $controle) {
            $linhas[] = array('controle_id'=>(int)$controle['id'], 'lote_id'=>$loteId, 'reproducao'=>2, 'tipo'=>'Transplante de embriões', 'lote'=>$lote['codigo'] ?? '--', 'data'=>ultraData($lote['data'] ?? ''), 'receptora'=>$controle['receptora'] ?? '--', 'data_ultrassom'=>ultraData($controle['data_ultrassom'] ?? ''), 'status'=>(int)($controle['ultrassom'] ?? 0));
        }
    }
    if (!$linhas) $mensagem = 'Nenhum registro de reprodução encontrado para esta fêmea.';
} elseif ($tipo === 0) {
    $consultasPadrao = array(
        array(
            'tipo'=>0, 'nome'=>'Monta natural',
            'dados'=>DBRead("monta_controle c INNER JOIN monta l ON l.id = c.id_monta LEFT JOIN animais a ON a.id = c.id_animal AND c.terceiro = 0 LEFT JOIN terceiros t ON t.id = c.id_animal AND c.terceiro = 1", "WHERE c.ultrassom IN ('1','2')", "c.id controle_id, c.ultrassom, c.data_ultrassom, c.terceiro, l.id lote_id, l.codigo, l.data_inicio data_reproducao, COALESCE(a.nome,t.nome,'--') femea")
        ),
        array(
            'tipo'=>1, 'nome'=>'Inseminação artificial',
            'dados'=>DBRead("inseminacao_controle c INNER JOIN inseminacao l ON l.id = c.id_lote LEFT JOIN animais a ON a.id = c.id_femea AND c.terceiro = 0 LEFT JOIN terceiros t ON t.id = c.id_femea AND c.terceiro = 1", "WHERE c.ultrassom IN ('1','2')", "c.id controle_id, c.ultrassom, c.data_ultrassom, c.terceiro, l.id lote_id, l.codigo, l.data data_reproducao, COALESCE(a.nome,t.nome,'--') femea")
        ),
        array(
            'tipo'=>2, 'nome'=>'Transplante de embriões',
            'dados'=>DBRead("transplante_controle c INNER JOIN transplante l ON l.id = c.id_lote", "WHERE c.ultrassom IN ('1','2')", "c.id controle_id, c.ultrassom, c.data_ultrassom, 0 terceiro, l.id lote_id, l.codigo, l.data data_reproducao, c.receptora femea")
        )
    );
    foreach ($consultasPadrao as $grupo) {
        foreach (ultraLista($grupo['dados']) as $registro) {
            $nome=$registro['femea']; if(!empty($registro['terceiro'])&&$nome!=='--')$nome.=' (Terceiro)';
            $linhas[]=array('controle_id'=>(int)$registro['controle_id'],'lote_id'=>(int)$registro['lote_id'],'reproducao'=>$grupo['tipo'],'tipo'=>$grupo['nome'],'lote'=>$registro['codigo']??'--','data'=>ultraData($registro['data_reproducao']??''),'receptora'=>$nome,'data_ultrassom'=>ultraData($registro['data_ultrassom']??''),'ordem'=>$registro['data_ultrassom']??'','status'=>(int)$registro['ultrassom']);
        }
    }
    usort($linhas,function($a,$b){ $data=strcmp((string)$b['ordem'],(string)$a['ordem']); return $data!==0?$data:$b['controle_id']<=>$a['controle_id']; });
    $tituloResultado = 'Últimos ultrassons cadastrados';
}

$porPagina = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) { $porPagina = 10; }
$totalRegistros = count($linhas);
$totalPaginas = max(1, (int)ceil($totalRegistros / $porPagina));
$paginaInformada = filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT);
$pagina = min(max(1, $paginaInformada === false ? 1 : $paginaInformada), $totalPaginas);
$inicio = ($pagina - 1) * $porPagina;
$linhasPagina = array_slice($linhas, $inicio, $porPagina);
$parametrosPagina = array('pg'=>'lista_ultrassom', 'tipo'=>$tipo, 'por_pagina'=>$porPagina);
if ($tipo === 1) {
    if ($reproducao !== null) { $parametrosPagina['reproducao'] = $reproducao; }
    if ($idLote) { $parametrosPagina['id_lote'] = $idLote; }
} elseif ($idAnimal) {
    $parametrosPagina['id_animal'] = $idAnimal;
    $parametrosPagina['animal_origem'] = $terceiro ? 'terceiros' : 'rebanho';
}
$urlPagina = 'geral.php?' . htmlspecialchars(http_build_query($parametrosPagina), ENT_QUOTES, 'UTF-8');
?>
<script>
function submeterFiltrosUltrassom() {
  var formulario = document.getElementById('filtros-ultrassom');
  if (formulario) { formulario.submit(); }
}
function alterarUltrassom(controle, lote, reproducao, status) {
  var parametros = {id_lote:controle,id_lote2:lote,reproducao:reproducao,status:status,tipo:<?=$tipo?>};
  <?php if ($tipo === 0): ?>parametros.id_animal=<?=$idAnimal?>; parametros.terceiro=<?=$terceiro?>;<?php endif; ?>
  window.location.href = 'reproducao/ultrassom/_cadastrar_ultrassom.php?' + new URLSearchParams(parametros).toString();
}
document.addEventListener('buscaanimais:selecionado', function (evento) {
  if (evento.target.classList.contains('busca-femea-ultrassom')) { submeterFiltrosUltrassom(); }
});
function selecionarLoteModalUltrassom(linha) {
  document.querySelectorAll('#modal-lotes-ultrassom .linha-lote-ultrassom').forEach(function (item) { item.classList.remove('info'); });
  var radio = linha.querySelector('input[name="vinculo"]');
  if (radio) { radio.checked = true; }
  linha.classList.add('info');
}
document.addEventListener('DOMContentLoaded', function () {
  var marcado = document.querySelector('#modal-lotes-ultrassom input[name="vinculo"]:checked');
  if (marcado) { selecionarLoteModalUltrassom(marcado.closest('tr')); }
  <?php if ($buscarCadastro): ?>$('#modal-lotes-ultrassom').modal('show');<?php endif; ?>
});
</script>
<section class="content-header">
  <h1>Ultrassom</h1>
  <ol class="breadcrumb"><li><i class="fa fa-venus-mars"></i> Reprodução</li><li class="active">Ultrassom</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;"><div class="box-body">
    <form action="geral.php" method="get">
      <input type="hidden" name="pg" value="lista_ultrassom"><input type="hidden" name="buscar_cadastro" value="1">
      <?php if ($tipo === 1): ?><input type="hidden" name="tipo" value="1"><?php if ($reproducao !== null): ?><input type="hidden" name="reproducao" value="<?=$reproducao?>"><?php endif; ?><?php if ($idLote): ?><input type="hidden" name="id_lote" value="<?=$idLote?>"><?php endif; ?><?php endif; ?>
      <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
        <div class="col-sm-6 col-md-4"><div class="form-group"><?php renderBuscaAnimais(array('id'=>'cad-femea-ultrassom','name'=>'cad_femea','name_id'=>'cad_femea_id','name_origem'=>'cad_femea_origem','label'=>'Fêmea','tipo'=>'femeas_receptoras','required'=>true,'value'=>$cadFemea,'value_id'=>$cadFemeaId,'value_origem'=>$cadOrigem,'limite_origem'=>5)); ?></div></div>
        <div class="col-sm-6 col-md-3"><div class="form-group"><label for="cad-situacao-ultrassom">Situação<span class="text-danger">*</span></label><select class="form-control" id="cad-situacao-ultrassom" name="cad_situacao" required><option value="">Selecionar</option><option value="1" <?=$cadSituacao===1?'selected':''?>>Positivo</option><option value="2" <?=$cadSituacao===2?'selected':''?>>Negativo</option></select></div></div>
        <div class="col-sm-6 col-md-3"><div class="form-group"><label for="cad-data-ultrassom">Data<span class="text-danger">*</span></label><div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control" id="cad-data-ultrassom" name="cad_data" value="<?=ultraH($cadDataTexto)?>" required></div></div></div>
        <div class="form-group col-sm-6 col-md-2"><button type="submit" class="btn btn-success">Cadastrar</button></div>
      </div>
    </form>
  </div></div>

  <div class="box" style="border-top:0;"><div class="box-body">
      <form id="filtros-ultrassom" action="geral.php" method="get">
        <input type="hidden" name="pg" value="lista_ultrassom">
        <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="form-group col-sm-6 col-md-3">
            <label for="tipo-ultrassom">Tipo</label>
            <select class="form-control" id="tipo-ultrassom" name="tipo" onchange="submeterFiltrosUltrassom()">
              <option value="0" <?=$tipo === 0 ? 'selected' : ''?>>Animal</option>
              <option value="1" <?=$tipo === 1 ? 'selected' : ''?>>Lote de reprodução</option>
            </select>
          </div>
          <?php if ($tipo === 0): ?>
          <div class="form-group col-sm-6 col-md-6">
            <?php renderBuscaAnimais(array(
                'id'=>'animal-ultrassom', 'name'=>'animal_ultrassom', 'name_id'=>'id_animal', 'name_origem'=>'animal_origem', 'label'=>'Fêmea', 'tipo'=>'femeas',
                'classe'=>'busca-femea-ultrassom', 'value'=>$animalSelecionado['nome'] ?? '',
                'value_id'=>$idAnimal, 'value_origem'=>$idAnimal ? ($terceiro ? 'terceiros' : 'rebanho') : '',
                'limite_origem'=>5, 'placeholder'=>'Digite para pesquisar'
            )); ?>
          </div>
          <?php else: ?>
          <div class="form-group col-sm-6 col-md-3">
            <label for="reproducao-ultrassom">Tipo de reprodução</label>
            <select class="form-control" id="reproducao-ultrassom" name="reproducao" onchange="submeterFiltrosUltrassom()">
              <option value="">Selecionar</option>
              <option value="0" <?=$reproducao === 0 ? 'selected' : ''?>>Monta natural</option>
              <option value="1" <?=$reproducao === 1 ? 'selected' : ''?>>Inseminação artificial</option>
              <option value="2" <?=$reproducao === 2 ? 'selected' : ''?>>Transplante de embriões</option>
            </select>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <label for="lote-ultrassom">Lote</label>
            <select class="form-control" id="lote-ultrassom" name="id_lote" <?=$reproducao === null ? 'disabled' : ''?>>
              <option value="">Selecionar</option>
              <?php foreach ($lotes as $opcao):
                  $conf=$configuracoes[$reproducao];
                  $descricao=($opcao['codigo'] ?? $opcao['id']).' - '.ultraNome($opcao[$conf['macho']] ?? 0, !empty($opcao[$conf['terceiro']]));
                  if ($reproducao === 2) { $descricao .= ' - '.ultraNome($opcao['id_mae'] ?? 0, !empty($opcao['terceiro_mae'])); }
              ?>
                <option value="<?=(int)$opcao['id']?>" <?=(int)$opcao['id'] === $idLote ? 'selected' : ''?>><?=ultraH($descricao)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>
          <div class="form-group col-sm-6 col-md-3">
            <button type="submit" class="btn btn-primary">Pesquisar</button>
            <a class="btn btn-default" href="geral.php?pg=lista_ultrassom">Limpar</a>
          </div>
        </div>
      </form>
    <div style="height:10px;"></div>
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;">
      <h3 class="box-title" style="font-size:16px; margin:0;"><?=$tipo === 1 && $tituloResultado !== '' ? ($reproducao === 2 ? 'Receptoras do lote' : 'Fêmeas do lote') : ($idAnimal ? 'Histórico de ultrassom' : 'Últimos ultrassons cadastrados')?></h3>
    </div>
    <?php if($tituloResultado!=='' && ($tipo===1 || $idAnimal)): ?><p style="margin-bottom:15px;"><strong><?=ultraH($tituloResultado)?></strong></p><?php endif; ?>
    <?php if(!$linhas): ?><div class="text-muted" style="padding:14px 4px;">
      <?php if($mensagem!==''): echo ultraH($mensagem); elseif($tipo===1): ?>Selecione o tipo de reprodução e o lote para consultar os animais.<?php else: ?>Pesquise e selecione uma fêmea para consultar o histórico de ultrassom.<?php endif; ?>
    </div><?php else: ?>
    <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th style="width:60px">Nº</th>
      <?php if($tipo===0): ?><th>Tipo</th><th>Lote</th><th>Data do lote</th><th><?=$idAnimal ? 'Receptora' : 'Fêmea / Receptora'?></th><th>Data do ultrassom</th><?php else: ?><th><?=$reproducao===2?'Receptora':'Fêmea'?></th><th>Data do ultrassom</th><?php endif; ?>
      <th style="width:190px">Ultrassom</th></tr></thead><tbody>
    <?php foreach($linhasPagina as $i=>$linha): $status=(int)$linha['status']; $positivo=$status===1; $negativo=$status===2; $proximo=$positivo?2:1; $texto=$positivo?'Positivo':($negativo?'Negativo':'Não informado'); $cor=$positivo?'#00a65a':($negativo?'#dd4b39':'#999'); ?>
      <tr><td><?=$inicio+$i+1?></td>
      <?php if($tipo===0): ?><td><?=ultraH($linha['tipo'])?></td><td><?=ultraH($linha['lote'])?></td><td><?=ultraH($linha['data'])?></td><td><?=ultraH($linha['receptora'])?></td><td><?=ultraH($linha['data_ultrassom'])?></td><?php else: ?><td><?=ultraH($linha['nome'])?></td><td><?=ultraH($linha['data_ultrassom'])?></td><?php endif; ?>
      <td><div class="sob-controle-status"><button type="button" class="sob-interruptor" role="switch" aria-checked="<?=$positivo?'true':'false'?>" aria-label="Ultrassom: <?=ultraH($texto)?>" title="Alterar para <?=$proximo===1?'positivo':'negativo'?>" onclick="alterarUltrassom(<?=(int)$linha['controle_id']?>,<?=(int)$linha['lote_id']?>,<?=(int)$linha['reproducao']?>,<?=$proximo?>)"><span class="sob-interruptor__indicador"><i class="fa <?=$positivo?'fa-check':($negativo?'fa-times':'fa-minus')?>" aria-hidden="true"></i></span></button><span class="sob-controle-status__texto" style="color:<?=$cor?>"><?=ultraH($texto)?></span></div></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
      <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
        <?php foreach ($parametrosPagina as $nomeParametro=>$valorParametro): if ($nomeParametro === 'por_pagina') continue; ?>
          <input type="hidden" name="<?=ultraH($nomeParametro)?>" value="<?=ultraH($valorParametro)?>">
        <?php endforeach; ?>
        <label for="por-pagina-ultrassom" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-ultrassom" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
          <?php foreach (array(10,20,50,100) as $quantidade): ?><option value="<?=$quantidade?>" <?=$porPagina===$quantidade?'selected':''?>><?=$quantidade?></option><?php endforeach; ?>
        </select>
      </form>
      <span class="text-muted">Exibindo <?=$totalRegistros ? $inicio+1 : 0?> a <?=min($inicio+$porPagina,$totalRegistros)?> de <?=$totalRegistros?> registros</span>
      <?php if ($totalPaginas > 1):
        $visiveis=array(1,$totalPaginas);
        for($n=max(1,$pagina-1);$n<=min($totalPaginas,$pagina+1);$n++)$visiveis[]=$n;
        $visiveis=array_values(array_unique($visiveis)); sort($visiveis); $anterior=0;
      ?>
      <nav aria-label="Paginação do ultrassom"><ul class="pagination pagination-sm no-margin">
        <li class="<?=$pagina===1?'disabled':''?>"><a href="<?=$pagina===1?'#':$urlPagina.'&amp;pag='.($pagina-1)?>">&laquo;</a></li>
        <?php foreach($visiveis as $n): ?>
          <?php if($anterior && $n>$anterior+1): ?><li class="disabled"><span>&hellip;</span></li><?php endif; ?>
          <li class="<?=$n===$pagina?'active':''?>"><a href="<?=$urlPagina?>&amp;pag=<?=$n?>"><?=$n?></a></li>
        <?php $anterior=$n; endforeach; ?>
        <li class="<?=$pagina===$totalPaginas?'disabled':''?>"><a href="<?=$pagina===$totalPaginas?'#':$urlPagina.'&amp;pag='.($pagina+1)?>">&raquo;</a></li>
      </ul></nav>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div></div>

  <div class="modal fade" id="modal-lotes-ultrassom" tabindex="-1" role="dialog" aria-labelledby="titulo-modal-lotes-ultrassom">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="titulo-modal-lotes-ultrassom">Lotes disponíveis para <?=ultraH($cadFemea)?></h4></div>
      <form action="reproducao/ultrassom/_cadastrar_registro.php" method="post">
        <div class="modal-body">
          <?php if ($cadErro !== ''): ?><div class="alert alert-danger"><?=ultraH($cadErro)?></div><?php endif; ?>
          <input type="hidden" name="femea_id" value="<?=$cadFemeaId?>"><input type="hidden" name="femea" value="<?=ultraH($cadFemea)?>"><input type="hidden" name="femea_origem" value="<?=ultraH($cadOrigem)?>"><input type="hidden" name="situacao" value="<?=$cadSituacao?>"><input type="hidden" name="data" value="<?=ultraH($cadDataTexto)?>">
          <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th style="width:45px;"></th><th>Tipo de reprodução</th><th>Lote</th><th>Macho</th><th>Data do lote</th></tr></thead><tbody>
          <?php if (!$cadLotes): ?><tr><td colspan="5" class="text-center"><?=$cadErro !== '' ? 'Corrija os dados do cadastro.' : 'Não há lotes pendentes de ultrassom para esta fêmea.'?></td></tr><?php endif; ?>
          <?php foreach ($cadLotes as $indice=>$item): ?><tr class="linha-lote-ultrassom" style="cursor:pointer;" onclick="selecionarLoteModalUltrassom(this)"><td class="text-center"><input type="radio" name="vinculo" value="<?=$item['tipo']?>:<?=$item['controle']?>:<?=$item['lote']?>" <?=$indice===0?'checked':''?>></td><td><?=ultraH($item['reproducao'])?></td><td><?=ultraH($item['codigo'])?></td><td><?=ultraH($item['macho'])?></td><td><?=ultraH(ultraData($item['data']))?></td></tr><?php endforeach; ?>
          </tbody></table></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><?php if ($cadLotes): ?><button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirmar cadastro</button><?php endif; ?></div>
      </form>
    </div></div>
  </div>
</section>
