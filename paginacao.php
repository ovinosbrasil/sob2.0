<div id="lista_animal" style="margin-top:1%;border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:97.5%; display:none;">
</div>
<?
switch ($_GET['pg']){

		default:
		include "home.php";
		break;

		case 'perfil';
		include "perfil/perfil.php";
		break;

		//ANIMAL
		case 'animal';
		include "animal/animal.php";
		break;

		case 'terceiro';
		include "animal/terceiros.php";
		break;

		case 'cadastrar_animal';
		include "animal/cadastrar/cadastrar.php";
		break;

		case 'cadastrar_nascimento';
		include "animal/cadastrar/cadastrar_nascimento.php";
		break;

		case 'lista_rebanho';
		include "animal/lista_rebanho.php";
		break;

		case 'lista_terceiros';
		include "animal/lista_terceiros.php";
		break;
		//FIM ANIMAL

		//MONTA
		case 'lista_monta';
		include "reproducao/monta/lista_monta.php";
		break;

		case 'cadastrar_monta';
		include "reproducao/monta/cadastrar_monta.php";
		break;

		case 'monta';
		include "reproducao/monta/monta.php";
		break;
		//FIM MONTA

		//INSEMINACAO
		case 'lista_inseminacao';
		include "reproducao/inseminacao/lista_inseminacao.php";
		break;

		case 'cadastrar_inseminacao';
		include "reproducao/inseminacao/cadastrar_inseminacao.php";
		break;

		case 'inseminacao';
		include "reproducao/inseminacao/inseminacao.php";
		break;

		case 'semen';
		include "reproducao/semen/semen.php";
		break;

		case 'vender_semen';
		include "reproducao/semen/vender.php";
		break;

		case 'vendas_semen';
		include "reproducao/semen/relatorio_venda.php";
		break;
		//FIM INSEMINACAO

		//EMBRIONAGEM
		case 'lista_te';
		include "reproducao/te/lista_te.php";
		break;

		case 'cadastrar_te';
		include "reproducao/te/cadastrar_te.php";
		break;

		case 'te';
		include "reproducao/te/te.php";
		break;

		case 'embrioes';
		include "reproducao/embrioes/embrioes.php";
		break;

		case 'vender_embrioes';
		include "reproducao/embrioes/vender.php";
		break;

		case 'vendas_embriao';
		include "reproducao/embrioes/relatorio_venda.php";
		break;
		//FIM EMBRIONAGEM

		//ULTRASSOM
		case 'lista_ultrassom';
		include "reproducao/ultrassom/lista_ultrassom.php";
		break;
		//FIM ULTRASSOM

		//RELATORIOS
		case 'relatorio_geral';
		include "relatorios/relatorio_geral.php";
		break;

		case 'relatorio_mortes';
		include "relatorios/relatorio_mortes.php";
		break;

		case 'relatorio_doencas';
		include "relatorios/relatorio_doencas.php";
		break;

		case 'relatorio_reprodutores';
		include "relatorios/reprodutor/relatorio_reprodutores.php";
		break;

		case 'relatorio_reprodutor';
		include "relatorios/reprodutor/relatorio_reprodutor.php";
		break;

		case 'relatorio_matrizes';
		include "relatorios/matriz/relatorio_matrizes.php";
		break;

		case 'relatorio_matriz';
		include "relatorios/matriz/relatorio_matriz.php";
		break;

		case 'relatorio_tipificacao';
		include "relatorios/relatorio_tipificacao.php";
		break;

		case 'relatorio_reproducao';
		include "relatorios/relatorio_reproducao.php";
		break;

		case 'relatorio_nascimentos';
		include "relatorios/relatorio_nascimentos.php";
		break;
		//FIM RELATORIOS

		//EXPOSIÇÃO
		case 'cadastrar_exposicao';
		include "exposicao/cadastrar_exposicao.php";
		break;

		case 'pesquisar_exposicao';
		include "exposicao/pesquisar_exposicao.php";
		break;

		case 'venda_exposicao';
		include "exposicao/venda_exposicao.php";
		break;

		case 'relatorio_vendas_exposicao';
		include "exposicao/relatorio_vendas_exposicao.php";
		break;

		case 'exposicao';
		include "exposicao/exposicao.php";
		break;

		case 'julgamento';
		include "exposicao/julgamento.php";
		break;

		case 'vender_animal_exposicao';
		include "exposicao/venda_exposicao.php";
		break;

		case 'premiacao';
		include "exposicao/premiacao.php";
		break;
		//FIM EXPOSIÇÃO

		//VENDAS
		case 'compradores';
		include "vendas/compradores.php";
		break;

		case 'comprador';
		include "vendas/comprador.php";
		break;

		case 'relatorio_venda';
		include "vendas/relatorio.php";
		break;

		case 'vender';
		include "vendas/vender.php";
		break;
		//FIM VENDAS

		//FINANCEIRO
		case 'financeiro';
		include "financeiro/financeiro.php";
		break;
		//FIM FINANCEIRO

		case 'relatorio_arco';
		include "reproducao/arco/relatorio_arco.php";
		break;


		//CHIP
		case 'cadastrar_chip';
		include "chip/animal/cadastrar.php";
		break;
		//FIM CHIP

		//VACINA
		case 'vacinas';
		include "vacinas/vacinas.php";
		break;

		case 'cadastrar_vacina';
		include "vacinas/cadastrar.php";
		break;

		case 'vacina';
		include "vacinas/vacina.php";
		break;
		//FIM VACINA

		//PESAGEM
		case 'pesagem';
		include "pesagem/pesagem.php";
		break;
		//FIM PESGEM
}
?>
