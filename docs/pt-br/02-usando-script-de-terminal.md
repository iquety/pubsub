# Usando um script de terminal

[◂ Como funciona](01-como-funciona.md) | [Voltar ao índice](indice.md) | [Usando comandos do Freep Console ▸](03-usando-comandos-freep-console.md)
-- | -- | --

## 1. O script "example"

A biblioteca vem acompanhada com dois comandos comandos de terminal, implementados utilizando a biblioteca [Freep\Console](https://github.com/ricardopedias/freep-console). Os comandos podem ser encontrados no diretório 'src/Command' e são usados no script de exemplo, chamado "example", localizado na raiz do repositório.

Execute o script com a opção '--help' para saber como usar:

```bash
./example --help
```

Algo como o texto abaixo será exibido:

```bash
Available commands: 
help                  Display help information
pubsub:broker         Start the pubsub message broker
pubsub:client-test    Send a test events to the publish/subscribe server
```

## 2. Executando o "Agente de Mensagens" (Message Broker)

Na seção ["Como funciona"](01-como-funciona.md) foi explicado como executar o "Agente de Mensagens" de forma rápida. Aqui, entraremos nos detalhas mais minuciosos do comando.

Para subir o "Agente de Mensagens", abra um terminal e use o seguinte comando:

```bash
./example pubsub:broker -d localhost -p 8080 -c 'tests/Example/config-file.php' -v
```

Isso irá executar o servidor de eventos em "localhost" na porta "8080". A saída deverá se parecer como a seguir:

```text
# Terminal do servidor em execução

✔ The publish/subscriber server has been started in tcp://localhost:8080
```

A seguir, uma explicação breve das opções usadas no comando:

Opção | Descrição
-- | --
pubsub:broker | O comando executado. Pode ser "pubsub:broker" ou "pubsub:client-test"
-d | O domínio onde o servidor está sendo executado
-p | A porta do servidor
-c | O arquivo contendo a configuração dos "Subscribers"
-s | O tipo de serialização a ser usada. Pode ser 'php' ou 'json'. Padrão é 'json'
-v | Ativa o modo verboso

### (-c) O arquivo de configuração

Este arquivo deve retornar uma função anônima (Closure) contendo as inscrições, como no exemplo abaixo:

```php
<?php

declare(strict_types=1);

use Freep\PubSub\EventLoop;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

return function (EventLoop $loop) {
    $loop->addSubscriber('channel-vormir', SubscriberOne::class); // recebe EventOne
    $loop->addSubscriber('channel-vormir', SubscriberTwo::class); // recebe EventOne e EventTwo
    $loop->addSubscriber('channel-mordor', SubscriberTwo::class); // recebe EventOne e EventTwo
};
```

### (-v) O modo verboso

Enquanto o servidor estiver em execução, o terminal atual ficará em modo de observação, aguardando eventos. A opção '-v' ativa o 'modo verboso', de forma que quando um evento for recebido, o servidor fará uma notificação no terminal em execução.

## 3. Enviando eventos de teste

No mesmo script usado para executar o "Agente de Mensagens", existe um comando especial para enviar eventos de teste. Com o "Agente de Mensagens" em execução, basta abrir outro terminal e usar o comando abaixo para enviar alguns eventos de teste.

```bash
./example pubsub:client-test -d localhost -p 8080 -v
```

A seguir, uma explicação breve das opções usadas no comando:

Opção | Descrição
-- | --
pubsub::client-test | O comando executado. Pode ser "pubsub:broker" ou "pubsub:client-test"
-d | O domínio onde o servidor está sendo executado
-p | A porta do servidor
-v | Ativa o modo verboso

Observe as mensagens aparecerem nos dois terminais.

```text
# Terminal do pubsub:client-test
# ----------------------------------

✔ Publish event of type 'EventOne' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-mordor' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-greenville' in tcp://localhost:8080
➜ Published Events
➜ Used memory: 1921928
```

```text
# Terminal do pubsub:broker
# ----------------------------------

➜ [2022-06-03 17:06:09]: Message of type 'EventOne' received on channel 'channel-vormir'
Message dispatched to SubscriberOne
Message dispatched to SubscriberTwo

➜ [2022-06-03 17:06:09]: Message of type 'EventTwo' received on channel 'channel-vormir'
Message dispatched to SubscriberTwo

...
```

[◂ Como funciona](01-como-funciona.md) | [Voltar ao índice](indice.md) | [Usando comandos do Freep Console ▸](03-usando-comandos-freep-console.md)
-- | -- | --
