# Usando comandos do Freep Console

[◂ Usando um script de terminal](02-usando-script-de-terminal.md) | [Voltar ao índice](indice.md) | [Implementando um Subscriber ▸](04-implementando-um-subscriber.md)
-- | -- | --

## 1. Criando um script personalizado

Como já foi dito anteriormente, a biblioteca vem acompanhada com dois comandos de terminal, implementados utilizando a biblioteca [Freep\Console](https://github.com/ricardopedias/freep-console). Os comandos podem ser encontrados no diretório 'src/Command' e são usados no script de exemplo, chamado "example", localizado na raiz do repositório.

Veja o conteúdo deste script:

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

O objetivo de utilizar a biblioteca [Freep\Console](https://github.com/ricardopedias/freep-console) é que ela facilita muito a portabilidade de comandos implementados, podendo carregá-los facilmente de diferentes módulos.

Pensando nisso, basta copiar o conteúdo do script `example` e criar um novo script no projeto que fará uso de Publish/Subscribe para contextualizar melhor e facilitar a execução do "Intermediador de Mensagens".

Além disso, é possível implementar facilmente comandos adicionais e carragá-los usando o método `Terminal->loadCommandsFrom('caminho/dos/outros/comandos');`

[◂ Usando um script de terminal](02-usando-script-de-terminal.md) | [Voltar ao índice](indice.md) | [Implementando um Subscriber ▸](04-implementando-um-subscriber.md)
-- | -- | --
