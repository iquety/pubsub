# Publicando um evento

[◂ Implementando um Evento](08-implementando-um-evento.md) | [Sumário da Documentação](indice.md) | [Publicando um sinal de parada ▸](10-publicando-um-sinal-de-parada.md)
-- | -- | --

## Introdução

Como já explicado em [Implementando no "Ponto de início" da aplicação (Bootstrap)](02-implementando-no-bootstrap.md) e também em [Implementando no "Intermediador de Mensagens" (Message Broker)](03-implementando-no-broker.md), para enviar eventos, a partir de qualquer lugar do sistema, basta invocar o método `publish()` do publicador adequado.

Existem publicadores disponíveis para cada situação, como será explicado a seguir.

## Publicador para o "Observer" simples

Para implementações que iniciem o observer junto com o bootstrap da aplicação, o publicador ideal é o "SimpleEventPublisher".

Veja um exemplo abaixo:

```php
// arquivo qualquer, em algum lugar do sistema

$schedule = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new UserRegistered('Ricardo', '99988877766', $schedule);

SimpleEventPublisher::instance()
->publish('registrations', $event);
```

No exemplo acima, o evento "UserRegistered" (usuário cadastrado) é publicado no canal "registrations" (cadastros). O inscrito "RegistrationSubscriber" irá lidar com o evento, invocando as rotinas apropriadas para ele.

## Publicador para o Intermediador de Mensagens

Para implementações que utilizem o "Intermediador de Mensagens", o publicador ideal é o "PhpEventPublisher".

Veja um exemplo abaixo:

```php
$publisher = new PhpEventPublisher('localhost', 7703);

$schedule = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new EventOne('Ricardo', '99988877766', $schedule);

$publisher->publish('channel-vormir', $event);
```

[◂ Implementando um Evento](08-implementando-um-evento.md) | [Sumário da Documentação](indice.md) | [Publicando um sinal de parada ▸](10-publicando-um-sinal-de-parada.md)
-- | -- | --
