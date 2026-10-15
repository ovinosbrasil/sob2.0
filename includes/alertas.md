# Componente de alertas

Componente reutilizável, integrado inicialmente ao calendário de manejo.
Arquivos: `dist/css/alertas.css` e `dist/js/alertas.js`. Não depende de jQuery,
Bootstrap ou Font Awesome. A região de notificações é criada na primeira chamada.

Para integrar a um fluxo, carregar o CSS no cabeçalho e o JS antes do uso:

```html
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
```

```js
// Mensagens padrão para reutilização nos fluxos.
SobAlertas.camposObrigatorios();
SobAlertas.cadastroRealizado();
SobAlertas.erro();

// Opcional: indicar quais campos estão faltando.
SobAlertas.camposObrigatorios(['Título', 'Data']);

// Opcional: personalizar a mensagem ou a duração.
SobAlertas.cadastroRealizado({mensagem: 'Tarefa cadastrada com sucesso.'});
SobAlertas.erro({mensagem: 'Não foi possível salvar o registro.', duracao: 0});
SobAlertas.camposObrigatorios(['Título'], {duracao: 8000});
```

Os atalhos mantêm os tipos e títulos padronizados e retornam um objeto com
`fechar()`. Use `cadastroRealizado()` somente depois da confirmação de gravação.
Criar os atalhos não os vincula automaticamente a outros fluxos.

```js
// API genérica para outras mensagens.
SobAlertas.mostrar({tipo: 'success', mensagem: 'Sêmen adicionado com sucesso.'});
SobAlertas.mostrar({tipo: 'warning', mensagem: 'Existem campos pendentes.'});
SobAlertas.mostrar({tipo: 'danger', mensagem: 'Não foi possível salvar o registro.'});

// Título opcional e duração em milissegundos (padrão: 6000).
var alerta = SobAlertas.mostrar({
    tipo: 'warning', titulo: 'Confira os dados', mensagem: 'Mensagem', duracao: 0
});
alerta.fechar();
```

`duracao: 0` mantém o alerta aberto, sem barra de tempo. O botão × permite fechar
manualmente. A contagem pausa com o mouse sobre o alerta, foco dentro dele ou aba
oculta. Escape fecha o alerta que contém o foco. Mensagens e títulos aceitam texto,
sem interpretar HTML. Os alertas empilham no canto superior direito e se adaptam
à largura da tela; leitores de tela recebem anúncios conforme o tipo.

Prévia isolada: `documentation/alertas-preview.html`. Ela não altera os fluxos do sistema.

## Exposição

`includes/alertas_exposicao.php` consome `$_SESSION['alerta_exposicao']` uma única
vez e carrega o componente nas páginas do módulo. As ações usam
`exposicao/_alertas.php` para registrar sucesso, atenção ou erro e redirecionar
com HTTP 303, mantendo a aba de julgamento/vendas quando aplicável. Cadastro e
edição do evento preservam os campos enviados quando há erro de validação.

Formulários POST marcados com `data-alertas-exposicao` recebem os avisos de
campos obrigatórios e data inválida definidos no próprio include do módulo,
utilizando o `SobAlertas` padrão. As mesmas
validações de cadastro/edição são realizadas no servidor.

Validação automatizada: `php tests/exposicao_alertas_test.php`,
`node tests/alertas_exposicao_test.js` e `node tests/alertas_test.js`.

## Vendas de animais

`includes/alertas_vendas.php` consome o flash `alerta_vendas` e os campos de
`campos_venda`. O fluxo `pg=vender` utiliza somente o `dist/js/alertas.js` padrão;
as validações do formulário permanecem no próprio `vendas/vender.php`.

`vendas/_venda_conjunta.php` valida todos os animais, valores, comprador e data
antes de gravar. Campos inválidos retornam com aviso e os dados preenchidos;
sucesso limpa o formulário. O total financeiro é recalculado no servidor.
A exclusão de venda também utiliza o flash, retornando ao relatório ou comprador.

Testes: `php tests/vendas_alertas_test.php` e
`node tests/vendas_formulario_test.js`.

## Seleção de animais para progênie

Aplicar `024_julgamento_controle_progene.sql` no schema da fazenda antes de
publicar a funcionalidade. A coluna `julgamento_controle.progene` é booleana,
NOT NULL e começa em false. A seleção pertence ao animal naquela exposição e
é compartilhada entre as abas de pai e mãe. Todos os animais continuam visíveis;
os selecionados recebem destaque verde e o interruptor indica Sim.

A atualização usa POST em `exposicao/_progene.php`, valida token de sessão e
inscrição no julgamento e responde em JSON. A tela só altera a cor após a
confirmação do servidor, mantendo a seleção anterior quando ocorre erro.
São reutilizados o interruptor de `controle-status.css` e os alertas SobAlertas.

Testes: `node tests/progene_exposicao_test.js`,
`php tests/progene_endpoint_test.php` e, no contêiner app,
`php tests/exposicao_participantes_test.php`.
