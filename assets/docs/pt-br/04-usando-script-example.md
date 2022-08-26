# Usando o script "example"

--page-nav--

## 1. O script "example"

Este repositório vem acompanhado com dois comandos de terminal, implementados com a biblioteca [Freep\Console](https://github.com/ricardopedias/freep-console). Os comandos podem ser encontrados no diretório 'src/Command' e são invicados pelo script "example" localizado na raiz do repositório.

Execute o script com a opção '--help' para aprender como usar:

```bash
./example --help
```

Algo como o texto abaixo será exibido:

```bash
Options: 
-h, --help            Display help information

Available commands: 
help                  Display help information
pubsub:broker         Start the pubsub message broker
pubsub:client-test    Send a test events to the pub/sub message broker
```

## 2. Executando o "Intermediador de Mensagens" (Message Broker)

Em ["Implementando no 'Intermediador de Mensagens' (Message Broker)"](03-implementando-no-broker.md) foi explicado, de forma rápida, como executar o "Intermediador de Mensagens". Aqui, entraremos nos detalhes mais minuciosos do comando.

Para subir o "Intermediador de Mensagens", abra um terminal e use o seguinte comando:

```bash
./example pubsub:broker -d localhost -p 8080 -c 'tests/Example/config-file.php' -s json -v
```

Isso irá executar o servidor de eventos em "localhost" na porta "8080". A saída deverá se parecer como a seguir:

```text
✔ The publish/subscriber server has been started in tcp://localhost:8080
```

Para mais informações de ajuda, use a opção '--help' com o nome do comando:

```bash
./example pubsub:broker --help
```

A seguir, uma explicação breve sobre as opções usadas no comando:

Opção | Descrição
-- | --
pubsub:broker | O comando executado. Pode ser "pubsub:broker" ou "pubsub:client-test"
-d | O domínio onde o "Intermediador de Mensagens" deverá ser executado. O padrão é 'localhost'
-p | A porta para a execução do "Intermediador de Mensagens". O padrão é '7703'
-c | O arquivo contendo a configuração dos "Subscribers"
-s | O tipo de serialização a ser usada. Pode ser 'php' ou 'json'. O padrão é 'json'
-v | Ativa o modo verboso

### (-c) O arquivo de configuração

Este arquivo deve retornar uma função anônima (Closure) contendo os "assinantes" (Subscribers) e seus respectivos canais de comunicação. Veja o exemplo abaixo:

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

### (-s) O tipo de serialização

Os eventos precisam ser serializados anntes de ser transmitidos para os canais de comunicação. Existem dois tipos de serialização disponíveis: 'php' e 'json'.

> **Importante**: Para integração de aplicações desenvolvidas somente em PHP, ambos os tipos podem ser usados. No entanto, se a arquitetura do sistema precisar que módulos ou subsistemas escritos em linguagens diferentes (ex.: Java, Ruby, Python etc), a escolha deve ser pela serialização genérica do tipo 'json', que pode ser replicada facilmente em qualquer linguagem.

Exemplo de serialização 'json':

```text
{"cpf":"123","name":"ricardo","ocurredOn":"2020-01-10 00:00:01"}
```

Exemplo de serialização 'php':

```text
a:3:{s:3:"cpf";s:3:"123";s:4:"name";s:7:"ricardo";s:9:"ocurredOn";s:19:"2020-01-10 00:00:01";}
```

### (-v) O modo verboso

Enquanto o "Intermediador de Mensagens" estiver em execução, o terminal atual ficará em modo de observação, aguardando eventos. A opção '-v' ativa o 'modo verboso' que, a cada evento recebido, faz uma notificação no terminal aberto.

## 3. Enviando eventos de teste

No mesmo script usado para executar o "Intermediador de Mensagens", existe um comando especial para enviar eventos de teste.

Com o "Intermediador de Mensagens" em execução, basta abrir outro terminal e usar o comando abaixo para enviar alguns eventos de teste.

```bash
./example pubsub:client-test -d localhost -p 8080 -v
```

Os eventos de teste são enviados para três canais de comunicação diferentes: 'channel-vormir', 'channel-mordor' e 'channel-greenville'. Dois destes canais possuem "assinantes" (Subscribers) configurados propositalmente a partir do arquivo `tests/Example/config-file.php`.

Para mais informações de ajuda, use a opção '--help' com o nome do comando:

```bash
./example pubsub:client-test --help
```

A seguir, uma explicação breve sobre as opções usadas no comando:

Opção | Descrição
-- | --
pubsub::client-test | O comando executado. Pode ser "pubsub:broker" ou "pubsub:client-test"
-d | O domínio onde o "Intermediador de Mensagens" está sendo executado. O padrão é 'localhost'
-p | A porta do "Intermediador de Mensagens". O padrão é '7703'
-v | Ativa o modo verboso

Observe as mensagens aparecerem nos dois terminais.

No terminal do **pubsub:client-test**:

```text
✔ Publish event labeled as 'event-one' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event labeled as 'event-two' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event labeled as 'event-two' to channel 'channel-mordor' in tcp://localhost:8080
✔ Publish event labeled as 'event-two' to channel 'channel-greenville' in tcp://localhost:8080
```

No terminal do **pubsub:broker**:

```text
➜ [2022-06-21 17:22:54]: Message labeled as 'event-one' received on channel 'channel-vormir'
Message dispatched to SubscriberOne
Message dispatched to SubscriberTwo

➜ [2022-06-21 17:22:55]: Message labeled as 'event-two' received on channel 'channel-vormir'
Message dispatched to SubscriberTwo

➜ [2022-06-21 17:22:55]: Message labeled as 'event-two' received on channel 'channel-mordor'
Message dispatched to SubscriberTwo

➜ [2022-06-21 17:22:55]: Message labeled as 'event-two' received on channel 'channel-greenville'
There are no subscribers on channel 'channel-greenville'
```

--page-nav--
