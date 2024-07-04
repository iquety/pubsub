# Implementando no "Intermediador de Mensagens" (Message Broker)

--page-nav--

## 1. Introdução

Esta é a forma mais interessante na maioria dos casos. A arquitetura Publish/Subscribe surgiu justamente para promover o desacoplamento na comunicação das coisas que acontecem em um sistema.

O objetivo é que as ações ocorram sem gerar dependências entre os módulos. Este formato possibilita um desacoplamento tão alto que até mesmo linguagens diferentes de PHP conseguirão enviar eventos.

Este tipo de abordagem consiste em manter em execução um "Intermediador de Mensagens" (também conhecido como "Message Broker") para receber os eventos ocorridos. Como será explicado na sequência.

### 1. Executar o Intermediador de Mensagens

Na raiz deste repositório existe um script chamado "example", que contém uma implementação de exemplo usando a biblioteca [Iquety Console](https://github.com/iquety/console).

> **Nota**: para facilitar a execução, você pode implementar em seu projeto um script personalizado semelhante a esse. Veja mais informações em ["Usando um script personalizado"](05-usando-script-personalizado.md).

Para executar o "Intermediador de Mensagens", abra um terminal e digite o seguinte comando:

```bash
./example pubsub:broker -c 'tests/Example/config-file.php' -v
```

Isso irá executar o "Intermediador de Mensagens" em "localhost" na porta "7703", com dois "Subscribers" e dois canais (configurados no arquivo `tests/Example/config-file.php`). Mais informações em [Usando o script example](04-usando-script-example.md). A saída deverá se parecer como a seguir:

```text
✔ The publish/subscriber server has been started in tcp://localhost:7703
```

### 2. Enviando eventos pelo PHP

Para enviar eventos, em qualquer lugar do sistema, basta invocar o método `publish()` do publicador "PhpEventPublisher". Veja um exemplo abaixo:

```php
$publisher = new PhpEventPublisher('localhost', 7703);

$schedule = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new EventOne('Ricardo', '99988877766', $schedule);

$publisher->publish('channel-vormir', $event);
```

### 3. Enviando eventos por outras linguagens

É possível enviar eventos a partir de aplicações construídas em linguagens diferentes de PHP. Isso é conseguido enviando uma mensagem TCP simples para o "Intermediador de Mensagens" em execução (no caso atual, tcp://localhost:7703).

Um exemplo de envio do evento "Tests\Example\Events\EventOne" pode ser visto abaixo:

```text
channel-one

event-one

{"cpf":123,"name":"ricardo","schedule":{"date":"2020-01-10 00:00:01.000000","timezone_type":3,"timezone":"UTC"},"occurredOn":{"date":"2020-01-10 00:00:01.000000","timezone_type":3,"timezone":"UTC"}}
```

O formato da mensagem deve seguir o seguinte esquema:

Conteúdo | Descrição
-- | --
nome do canal | texto simples
duas quebras de linha | "\n" + "\n"
nome do evento | Nome retornado pelo método Event->label() do evento
duas quebras de linha | "\n" + "\n"
conteúdo json serializado | **Importante**: o "Intermediador de Mensagens" deve estar configurado para usar serializações do tipo Json (o padrão da biblioteca). Mais informações em [Usando o script example](04-usando-script-example.md)
uma quebra de linha | "\n"

--page-nav--
