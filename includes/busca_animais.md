# Componente de busca de animais

Inclua `includes/busca_animais.php` e chame `renderBuscaAnimais()` no formulário:

```php
<?php require_once __DIR__ . '/includes/busca_animais.php'; ?>
<?php renderBuscaAnimais(array(
    'id' => 'mae',
    'name' => 'mae',
    'label' => 'Mãe',
    'tipo' => 'femeas',
    'required' => true
)); ?>
```

Tipos disponíveis:

- `machos`: machos do rebanho e terceiros;
- `femeas`: fêmeas do rebanho e terceiros;
- `receptoras`: somente receptoras ativas;
- `todos`: machos e fêmeas do rebanho e terceiros.

O componente envia o texto em `name`, o identificador em `name_id` e a origem em `name_origem`. Os nomes dos campos ocultos podem ser personalizados com `name_id` e `name_origem`.

Após uma escolha, o elemento raiz dispara o evento `buscaanimais:selecionado`. O registro completo está em `evento.detail`.


Para limitar igualmente os resultados de cada origem, use `limite_origem`. Para manter o botão de cadastro ao lado do campo, informe `novo_url`:

```php
renderBuscaAnimais(array(
    'id' => 'pai',
    'name' => 'pai',
    'label' => 'Pai',
    'tipo' => 'machos',
    'limite_origem' => 5,
    'novo_url' => 'geral.php?pg=cadastrar_animal&tipo=2'
));
```
