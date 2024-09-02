# Biblioteca API

Este projeto é uma API RESTful para gerenciamento de uma biblioteca, desenvolvida com Laravel e integrada com Amazon SQS para processamento de filas.

## Índice

1. [Requisitos](#requisitos)
2. [Instalação](#instalação)
3. [Configuração](#configuração)
4. [Executando a Aplicação](#executando-a-aplicação)
5. [Funcionalidades](#funcionalidades)
6. [Rotas da API](#rotas-da-api)
7. [Testes](#testes)
8. [Integração com AWS SQS](#integração-com-aws-sqs)
9. [Sistema de Notificações](#sistema-de-notificações)
10. [Estilo de Código (Laravel Pint)](#estilo-de-código-laravel-pint)
11. [Deployment](#deployment)

... [Seções anteriores permanecem as mesmas] ...

## Estilo de Código (Laravel Pint)

Este projeto utiliza Laravel Pint para manter um estilo de código consistente. Pint é uma ferramenta de formatação de código opinativa para projetos PHP.

### Instalação

O Laravel Pint já está incluído nas dependências de desenvolvimento do projeto. Se por algum motivo precisar instalá-lo manualmente:

```bash
composer require laravel/pint --dev
```

### Uso

Para verificar o estilo de código sem fazer alterações:

```bash
./vendor/bin/pint --test
```

Para formatar automaticamente o código:

```bash
./vendor/bin/pint
```

Para formatar um diretório ou arquivo específico:

```bash
./vendor/bin/pint app/Models
./vendor/bin/pint app/Http/Controllers/BookController.php
```

### Configuração

O Pint usa a configuração padrão do Laravel. Se você precisar personalizar as regras, crie um arquivo `pint.json` na raiz do projeto. Exemplo:

```json
{
    "preset": "laravel",
    "rules": {
        "array_syntax": {
            "syntax": "short"
        },
        "ordered_imports": {
            "sort_algorithm": "alpha"
        },
        "no_unused_imports": true
    }
}
```

## Redis

Este projeto utiliza Redis para cache e filas. Certifique-se de que o Redis está configurado corretamente no seu ambiente.

### Configuração

As configurações do Redis podem ser encontradas no arquivo `.env`:

```
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### REDIS

Para usar o Redis como cache:

```php
use Illuminate\Support\Facades\Cache;

Cache::put('key', 'value', $seconds);
$value = Cache::get('key');
```

Para usar o Redis para filas:

```php
use App\Jobs\ProcessPodcast;

ProcessPodcast::dispatch($podcast);
```

Para processar jobs na fila:

```bash
php artisan queue:work redis
```