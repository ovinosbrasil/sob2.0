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
