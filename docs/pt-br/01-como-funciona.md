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

## 3. Tipos de implementação

Existem duas maneiras de implementar um "Observer" para Publish/Subscribe, com vantagens e desvantagens em cada cenário:

Implementação | Prós | Contras
-- | -- | --
No início da aplicação (Bootstrap) | Simples de implementar e entender, mesmo para quem não conhece a arquitetura Pub/Sub. Ideal para comunicação dos módulos dentro de uma mesma aplicação. | Cria um acoplamento com a implementação do bootstrap. Aplicações diferentes precisam reimplementar a configuração do bootstrap, o que pode aumentar a preocupação na hora de adicionar novos inscritos. Aplicações feitas com linguagens diferentes de PHP (ex.: Java, Ruby, Python) não podem enviar eventos.
No Agente de Mensagens (Message Broker) | Ideal para integrar aplicações diferentes. Centraliza a configuração dos inscritos no "Agente de Mensagens". Provê o desacoplamento real entre as partes que se comunicam. Uma aplicação não-PHP também pode enviar eventos para se comunicar | Pode ser mais difícil de entender para aqueles que não estão familiarizados com uma arquitetura orientada a eventos. É preciso executar e manter o servidor de eventos sempre ativo para receber e despachar os eventos ocorridos.

A seguir, mais informações sobre os dois tipos de implementação.

## 4. Implementando no início da aplicação (Bootstrap)

Esta é a forma mais simples de implementar e consiste em iniciar o "Observer" junto com a aplicação através de uma chamada estática à classe 'SimpleEventPublisher'. Nesta chamada, os "Subscribers" devem ser inscritos para serem notificados quando os eventos ocorrerem no sistema.

> Nota: O ponto de início de uma aplicação (também chamado de "Bootstrap") depende de como ela foi pensada, portanto, não existe um padrão. No exemplo abaixo, usamos o próprio arquivo 'index.php':

```php
// index.php (arquivo chamado em todos os acessos ao sistema)

// rotinas iniciais da aplicação ...

SimpleEventPublisher::instance()
    ->subscribe('registrations', RegistrationSubscriber::class)
    ->subscribe('payments', PaymentSubscriber::class);
```

Para disparar eventos, em qualquer lugar do sistema, basta invocar uma chamada para o método `publish()` do publicador:

```php
// arquivo qualquer, em algum lugar do sistema

$ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new UserRegistered('Ricardo', '99988877766', $ocurredOn);

SimpleEventPublisher::instance()
    ->publish('registrations', $event);

```

No exemplo acima, o evento "UserRegistered" (usuário cadastrado) é publicado no canal "registrations" (cadastros). O inscrito "RegistrationSubscriber" irá lidar com o evento, invocando as rotinas apropriadas para ele.

## 5. Implementando no Agente de Mensagens (Message Broker)

Esta é a forma mais interessante na maioria dos casos. A arquitetura Publish/Subscribe surgiu justamente para prover um maior desacoplamento na comunicação das coisas que acontecem em um sistema. O objetivo é que as ações ocorram sem gerar dependências entre os módulos. Este formato possibilita que linguagens diferentes de PHP também consigam enviar eventos.

Esta forma de implementação consiste em manter um "Agente de Mensagens" (também conhecido como "Message Broker") em execução, para receber os eventos ocorridos. Como será explicado na sequência.

### 5.1. Executar o Agente de Mensagens

Na raiz do projeto existe um script chamado "example", que contém uma implementação de exemplo usando a biblioteca [Freep Console](https://github.com/ricardopedias/freep-console).

> Nota: você pode implementar um script como esse em seu projeto. Basta copiar a implementação existente no script "example" e colar no seu próprio script Veja mais informações em ["Usando comandos do Freep Console"](03-usando-comandos-freep-console.md).

Para subir o "Agente de Mensagens", abra um terminal e digite o seguinte comando:

```bash
./example pubsub:broker -c 'tests/Example/config-file.php' -v
```

Isso irá executar o "Agente de Mensagens" em "localhost" na porta "8080", com dois "Subscribers" (configurados no arquivo `tests/Example/config-file.php`). Mais informações em [Usando um script de terminal](02-usando-script-de-terminal.md). A saída deverá se parecer como a seguir:

```text
✔ The publish/subscriber server has been started in tcp://localhost:8080
```

### 5.2. Disparar eventos PHP

Para disparar eventos, em qualquer lugar do sistema, basta invocar uma chamada para o método `publish()` do publicador "PhpEventPublisher". Veja um exemplo abaixo:

```php
$publisher = new PhpEventPublisher('localhost', 8080);

$ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new EventOne('Ricardo', '99988877766', $ocurredOn);

$publisher->publish('channel-vormir', $event);
```

### 5.3. Disparar eventos a partir de outras linguagens

É possível enviar eventos a partir de aplicações construídas em linguagens diferentes de PHP. Isso é conseguido enviando uma mensagem TCP simples para o "Agente de Mensagens" em execução (no caso atual, tcp://localhost:8080).

Um exemplo de envio do evento "Tests\Example\Events\EventOne" pode ser visto abaixo:

```text
channel-one

event-one

{"cpf":"123","name":"ricardo","ocurredOn":"2020-01-10 00:00:01"}
```

O formato da mensagem deve seguir o seguinte esquema:

Conteúdo | Descrição
-- | --
nome do canal | texto simples
duas quebras de linha | "\n" + "\n"
nome do evento | Nome retornado pelo método Event->label() do evento
duas quebras de linha | "\n" + "\n"
conteúdo json serializado | **Importante**: o Agente de Mensagens deve estar configurado para usar serializações do tipo Json (o padrão da biblioteca)
uma quebra de linha | "\n"

[◂ Voltar ao índice](indice.md) | [Usando um script de terminal ▸](02-usando-script-de-terminal.md)
-- | --
