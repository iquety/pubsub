# Usando comandos do Freep Console

[◂ Usando um script de terminal](02-usando-script-de-terminal.md) | [Voltar ao índice](indice.md) | [Implementando um Subscriber ▸](04-implementando-um-subscriber.md)
-- | -- | --

## 1. Criando um script personalizado

A biblioteca vem acompanhada com dois comandos comandos de terminal, implementados utilizando a biblioteca [Freep\Console](https://github.com/ricardopedias/freep-console). Os comandos podem ser encontrados no diretório 'src/Command' e são usados no script de exemplo, chamado "example", localizado na raiz do repositório.

```php
#!/bin/php
<?php

// Carrega o autoloader do Composer
include __DIR__ . "/vendor/autoload.php";

use Freep\Console\Terminal;
use Freep\PubSub\EventLoop;

array_shift($argv);

$terminal = new Terminal(__DIR__ . "/src");

// carrega os comandos disponíveis na biblioteca
$terminal->loadCommandsFrom(EventLoop::commandPath());

$terminal->run($argv);
```

Para facilitar, basta criar um script como esse em qualquer projeto para fornecer uma forma fácil de iniciar o "Agente de Mensagens" (Message Broker).

Além disso, é possível implementar facilmente comandos adicionais e carragá-los usando o método `Terminal->loadCommandsFrom('caminho/dos/comandos');`
WWW
[◂ Usando um script de terminal](02-usando-script-de-terminal.md) | [Voltar ao índice](indice.md) | [Implementando um Subscriber ▸](04-implementando-um-subscriber.md)
-- | -- | --
