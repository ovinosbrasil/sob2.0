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
