# Como funciona

[◂ Voltar ao índice](indice.md) | [Usando um script de terminal ▸](02-usando-script-de-terminal.md)
-- | --

## 1. Arquitetura orientada a eventos

A primeira coisa a fazer quando implementamos uma arquitetura orientada a eventos é ter um meio de verificar ininterruptamente a aplicação, aguardando algum evento acontecer. Quando um evento é identificado, ele deve ser despachado para as rotinas ou módulos do sistema que estejam preparados para lidar com aquele tipo de evento.

Existem diversas formas de fazer isso. No entanto, esta biblioteca trata de um padrão arquitetônico específico de encaminhamento de eventos chamado de Publish/Subscribe (Publicar/Inscrever em português).

## 2. O padrão Publish/Subscribe

Este padrão arquitetônico faz uso de um padrão de projeto chamado ["Observer"](https://refactoring.guru/pt-br/design-patterns/observer). Resumidamente, um "Observer" possui uma lista de "inscritos" (como em um canal do Youtube). Cada inscrito tem suas próprias necessidades e recebe notificações sempre que algo de seu interesse acontece.

O exemplo fictício abaixo é autoexplicativo:

```php
$observer = new Observer();

// inscreve três ouvintes de notícias em dois canais diferentes
$observer->subscribe('channel-name', Ricardo::class);
$observer->subscribe('channel-name', Roberto::class);
$observer->subscribe('channel-other-name', Ronaldo::class);

// lança uma notícia para cada canal de ouvintes
$observer->publish('channel-name', AssistirNetflix::class);
$observer->publish('channel-other-name', AssistirDisneyPlus::class);
```

Perceba que, no código acima, a notícia 'AssistirNetflix' será recebida por 'Ricardo' e 'Roberto'. Já a notícia 'AssistirDisneyPlus' será recebida somente por 'Ronaldo'.

Com base no exemplo fictício, chamaremos os ouvintes de "Subscribers" e as notícias de "Events".

## 3. Implementando o observador de eventos

Existem duas maneiras de implementar um "Observer" para Publish/Subscribe, com vantagens e desvantagens em cada cenário:

Implementação | Prós | Contras
-- | -- | --
No bootstrap da aplicação | Simples de implementar e simples de entender, mesmo para quem não conhece a arquitetura. Ideal para comunicação dos módulos dentro de uma mesma aplicação. | Cria um acoplamento com a implementação do bootstrap. Aplicações diferentes precisam reimplementar a configuração do bootstrap, o que pode aumentar a preocupação na hora de adicionar novos inscritos. Aplicações feitas com linguagens diferentes (ex.: PHP + Java) não podem usar esta metodologia.
No servidor de publicação | Mais simples de implementar. Ideal para integrar aplicações diferentes. Centraliza a configuração dos inscritos no servidor de eventos. Provê o desacoplamento real entre as partes que se comunicam. | Pode ser mais difícil de entender para os que não conhecem a arquitetura. É preciso executar e manter o servidor de eventos sempre ativo para receber e despachar os eventos ocorridos.

### 3.1. No bootstrap da aplicação

Esta é a forma mais simples de implementar e consiste em iniciar o "Observer" junto com a aplicação através de uma chamada estática à classe 'SimpleEventPublisher', inscrevendo os "Subscribers" que serão notificados quando os eventos ocorrerem no sistema.

> Nota: O ponto de início de uma aplicação depende de como ela foi pensada, portanto, não existe um padrão. No exemplo abaixo, usamos o próprio arquivo 'index.php':

```php
// index.php (arquivo chmamado em todos os acessos ao sistema)

// rotinas iniciais da aplicação ...

SimpleEventPublisher::instance()
    ->subscribe('registrations', RegistrationSubscriber::class)
    ->subscribe('payments', PaymentSubscriber::class);

// outras rotinas iniciais da aplicação ...

```

Agora, para disparar eventos, basta invocar uma chamada para o método `publish()` do publicador:

```php
// userRegister.php (arquivo qualquer, em outra parte do sistema)

// rotinas de cadastro do usuário ...

SimpleEventPublisher::instance()
    ->publish('registrations', UserRegistered::class);

// outras rotinas de cadastro do usuário ...
```

No exemplo acima, o evento "UserRegistered" (usuário cadastrado) é publicado no canal "registrations" (cadastros). O inscrito "RegistrationSubscriber" irá lidar com o evento, invocando as rotinas apropriadas para ele.

### 3.2. No servidor de publicação

Esta é a forma mais simples de implementar e também a mais interessante na maioria dos casos. A arquitetura Publish/Subscribe surgiu justamente para prover um maior desacoplamento na comunicação das coisas que acontecem em um sistema. O objetivo é que as ações ocorram sem gerar dependências entre os módulos, mesmo que sejam implementados em diferentes linguagens de programação.

Esta segunda forma de implementação consiste em manter um servidor de eventos em execução para receber os eventos ocorridos. Na raiz do projeto existe um script chamado "example", que contém uma implementação de exemplo usando a biblioteca [Freep Console](https://github.com/ricardopedias/freep-console).

> Nota: você pode implementar um script como esse em seu projeto. Basta copiar a implementação existente em "example".

Para subir o servidor de eventos, abra um terminal e use o seguinte comando:

```bash
./example pubsub:server -d localhost -p 8080 -c path/to/config-file.php -t -v
```

```text
# Terminal do servidor em execução

✔ The publish/subscriber server has been started in tcp://localhost:8080
```

Isso irá executar o servidor de eventos em "localhost" na porta "8080". A seguir, uma explicação breve das opções usadas no comando:

Opção | Descrição
-- | --
pubsub:server | O comando executado. Pode ser "pubsub:server" ou "pubsub:client-test"
-d | O domínio onde o servidor está sendo executado
-p | A porta do servidor
-c | O arquivo contendo os "Subscribers"
-t | Ativa o modo de teste
-v | Ativa o modo verboso

#### (-c) O arquivo de configuração

Este arquivo deve retornar uma função anônima (Closure) contendo as inscrições, como no exemplo abaixo:

```php
<?php

declare(strict_types=1);

use Freep\PubSub\EventLoop;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

return function (EventLoop $loop) {

    $loop->addSubscriber('channel', MySubscriberOne::class);
    $loop->addSubscriber('other-channel', OtherSubscriber::class);
};
```

#### (-t) O modo de testes

Com o modo de testes ativo, dois "Subscribers" ("SubscriberOne" e "SubscriberTwo") serão automaticamente configurados nos canais "channel-vormir" e "channel-mordor". Eles estarão preparados para receber dois tipos de eventos ("EventOne" e "EventTwo"), como no exemplo abaixo:

```php
$loop->addSubscriber('channel-vormir', SubscriberOne::class); // recebe EventOne
$loop->addSubscriber('channel-vormir', SubscriberTwo::class); // recebe EventOne e EventTwo
$loop->addSubscriber('channel-mordor', SubscriberTwo::class); // recebe EventOne e EventTwo
```

#### (-v) O modo verboso

Enquanto o servidor estiver em execução, o terminal atual ficará em modo de observação, aguardando eventos. A opção '-v' ativa o 'modo verboso', de forma que quando um evento for recebido, o servidor fará uma notificação no terminal em execução.

### 3.3. Enviando eventos de teste para o servidor de eventos

No mesmo script usado para executar o servidor de eventos, existe um comando especial para enviar eventos de teste. Com o servidor em execução, basta abrir outro terminal e usar o comando abaixo para enviar alguns eventos de teste.

```bash
./example pubsub:client-test -d localhost -p 8080 -v
```

A seguir, uma explicação breve das opções usadas no comando:

Opção | Descrição
-- | --
pubsub::client-test | O comando executado. Pode ser "pubsub:server" ou "pubsub:client-test"
-d | O domínio onde o servidor está sendo executado
-p | A porta do servidor
-v | Ativa o modo verboso

Observe as mensagens aparecerem nos dois terminais.

```text
# Terminal do pubsub:client-test

✔ Publish event of type 'EventOne' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-mordor' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-greenville' in tcp://localhost:8080
➜ Published Events
➜ Used memory: 1921928
```

```text
# Terminal do pubsub:server

➜ [2022-06-03 17:06:09]: Message of type 'EventOne' received on channel 'channel-vormir'
Message dispatched to SubscriberOne
Message dispatched to SubscriberTwo

➜ [2022-06-03 17:06:09]: Message of type 'EventTwo' received on channel 'channel-vormir'
Message dispatched to SubscriberTwo

...
```

### 3.4. Enviando eventos reais para o servidor de eventos

Para enviar eventos ao servidor em execução, é preciso usar a classe "PhpEventPublisher" e invocar o método `publish()`, como no exemplo abaixo:

```php
$publisher = new PhpEventPublisher('localhost', 8080);

$ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new EventOne('Ricardo', '99988877766', $ocurredOn);

$publisher->publish('channel-vormir', $event);
```

### 3.5. Enviando eventos reais a partir de uma linguagem diferente de PHP

É possível enviar eventos a partir de aplicações construídas em linguagens diferentes de PHP. Isso é conseguido enviando uma mensagem TCP simples para o servidor em execução (no caso atual, tcp://localhost:8080). 

O formato da mensagem deve seguir o seguinte esquema:

Conteúdo | Descrição
-- | --
nome do canal | texto simples
duas quebras de linha | "\n" + "\n"
nome do evento | Nome retornado pelo método getName() do evento
duas quebras de linha | | "\n" + "\n"
tipo do evento | Nome completo da classe do evento
uma quebra de linha | "\n"
conteúdo json serializado | Importante: o servidor deve estar configurado para usar serializações com a classe "Event\Serializer\JsonEventSerializer"
uma quebra de linha | "\n"

Um exemplo de envio do evento "Tests\Example\Events\EventOne" pode ser visto abaixo:

```text
channel-one

Tests\Example\Events\EventOne

Tests\Example\Events\EventOne
{"cpf":"123","name":"ricardo","ocurredOn":"2020-01-10 00:00:01"}
```

[◂ Voltar ao índice](indice.md) | [Usando um script de terminal ▸](02-usando-script-de-terminal.md)
-- | --
