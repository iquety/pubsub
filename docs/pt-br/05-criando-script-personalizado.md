# Criando um script personalizado

[◂ Usando o script "example"](04-usando-script-example.md) | [Sumário da Documentação](indice.md) | [Mantendo o "Intermediador de Mensagens" sempre em execução ▸](06-mantendo-a-execucao.md)
-- | -- | --

Como já foi dito anteriormente, a biblioteca vem acompanhada de [dois comandos de terminal](04-usando-script-example.md) que são usados no script chamado "example", localizado na raiz do repositório.

Veja o conteúdo do script:

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

O objetivo de utilizar a biblioteca [Freep\Console](https://github.com/ricardopedias/freep-console) é que ela facilita muito a portabilidade de comandos. Pode-se implementar comandos em diversos módulos separados e carregar todos eles de um único ponto usando o método "Terminal->loadCommandsFrom()".

Portanto, basta copiar o conteúdo do script `example` e criar um novo script no projeto que fará uso de Publish/Subscribe para contextualizar melhor seu objetivo e facilitar a execução do "Intermediador de Mensagens".

Por exemplo, você pode criar um script chamado `meuapp` para seu projeto, contendo o seguinte conteúdo:

```php
#!/bin/php
<?php

// Carrega o autoloader do Composer
include __DIR__ . "/vendor/autoload.php";

use Freep\Console\Terminal;
use Freep\PubSub\EventLoop;

array_shift($argv);

$terminal = new Terminal(__DIR__ . "/src");

// carrega os comandos de Pub/Sub
$terminal->loadCommandsFrom(EventLoop::commandPath());

// carrega os comandos do meu projeto
$terminal->loadCommandsFrom(__DIR__ . '/MeuApp/Commands');

$terminal->run($argv);
```

Perceba que o script acima inicia com `#!/bin/php`. Essa notação diz para o terminal do sistema operacional que este script deverá ser interpretado pelo programa "/bin/php". Dessa forma, não é necessário digitar `php meuapp`, mas apenas `./meuapp`:

```bash
./meuapp --help
```

> **Nota**: em sistemas unix ou derivados, para poder invocar diretamente um script (ex: ./meuapp), é preciso que ele tenha permissão para executar. Isso é conseguido pelo comando `chmod a+x meuapp`.

[◂ Usando o script "example"](04-usando-script-example.md) | [Sumário da Documentação](indice.md) | [Mantendo o "Intermediador de Mensagens" sempre em execução ▸](06-mantendo-a-execucao.md)
-- | -- | --
