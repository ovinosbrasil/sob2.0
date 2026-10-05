# Confirmação de exclusão

Carregada uma vez por `geral.php`:
- Estrutura: `includes/confirmacao_exclusao.php`
- Estilos: `dist/css/confirmacao-exclusao.css`
- Comportamento: `dist/js/confirmacao-exclusao.js`

Para novas telas, use a API compartilhada; não copie markup ou CSS:

```javascript
confirmarExclusao({
    titulo: 'Excluir registro?',
    nome: nomeDoRegistro,
    descricao: 'Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
        // Chame aqui o fluxo de exclusão da tela.
    }
});
```

Os textos são inseridos com textContent. Cancelar, fechar, Escape ou clicar
fora da janela descartam a ação. O foco inicia em Cancelar e retorna ao
acionador ao fechar. Excluir bloqueia cliques repetidos.

Migrados: lista do rebanho, lista de terceiros, exibição do terceiro e
“Excluir animal” da exibição do animal. As demais telas legadas ainda
precisam ser migradas para esta API.

## Exclusão de terceiros

As entradas da lista, do detalhe e do rebanho passam pela prévia
`animal/palco_excluir_terceiro.php`. Ela retorna nome, vínculos, possibilidade
de excluir e token CSRF. Havendo vínculos, o modal explica o bloqueio e
mantém Excluir desabilitado. Nenhum histórico é apagado.

Sem vínculos, a confirmação envia POST para `animal/_excluir_terceiro.php`.
O servidor valida sessão, token e ID, bloqueia temporariamente a tabela de
terceiros para escrita e as tabelas de dependências para leitura, refaz a
verificação e exclui somente se o cadastro continuar sem vínculos. Falhas de
consulta ou de permissão de LOCK TABLES impedem a exclusão. O bloqueio é
liberado ao terminar, inclusive em caso de erro. A conta MySQL precisa da
permissão LOCK TABLES. Consultas e exclusão usam a mesma conexão.

A verificação central está em `animal/_dependencias_terceiro.php`: animais,
monta, monta_controle, inseminacao, inseminacao_controle, transplante (inclui
macho complementar), semen e embriao. Contagens incluem registros históricos
e estoques zerados. A origem é conferida junto com o ID. Monta sem ID também
é conferida pelo nome, de forma conservadora. Previsões em crias dependem dos
controles/lotes reprodutivos já protegidos por essa verificação.

Terceiros com vínculos e ainda ativos apresentam a ação Inativar no modal.
A ação usa POST com token CSRF e muda apenas `terceiros.ativo`.
A listagem administrativa mostra ativos e inativos, permite filtrar por
situação e oferece Ativar nos inativos. A tela de detalhe indica a inativação.
Pesquisas e validações para novos vínculos exigem ativo = 1.
Importação mantém inativos na resolução de nomes para bloquear seu uso sem
criar duplicatas. Consultas de histórico continuam incluindo inativos.
Transferência de vínculos entre duplicatas ainda não faz parte deste fluxo. Novas tabelas que referenciem terceiros precisam entrar no
mapa de dependências antes de liberar exclusões.

Validação: `node tests/exclusao_terceiro_test.js` e, no ambiente Docker,
`docker compose exec -T app php tests/exclusao_terceiro_test.php`.
O teste PHP usa somente tabelas temporárias.

Antes de publicar, aplicar `docker/mysql/migrations/017_terceiros_ativos.sql`
no schema de cada fazenda. A migração é idempotente e preserva os registros
existentes como ativos. Foi aplicada no banco local `siste870_buria`.
