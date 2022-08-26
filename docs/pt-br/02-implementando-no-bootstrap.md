# Implementando no "Ponto de início" da aplicação (Bootstrap)

[◂ O padrão Publish/Subscribe](01-padrao-pubsub.md) | [Sumário da Documentação](indice.md) | [Implementando no "Intermediador de Mensagens" (Message Broker) ▸](03-implementando-no-broker.md)
-- | -- | --

## 1. Introdução

Esta é a forma mais simples de implementar e consiste em iniciar o "Observer" junto com a aplicação através de uma chamada estática à classe 'SimpleEventPublisher'. Nesta chamada, os "Subscribers" devem ser inscritos para serem notificados quando os eventos ocorrerem no sistema.

## 2. Implementação

A localização e a implementação do "Ponto de Início" de uma aplicação (também chamado de "bootstrap") depende de como ela foi planejada, portanto, não existe um padrão. No exemplo a seguir, imaginando uma aplicação que funcione com urls amigáveis, o arquivo 'index.php' seria o "bootstrap", pois é o arquivo invocado para resolver todos os acessos ao sistema.

```php
// index.php (arquivo chamado em todos os acessos ao sistema)

// ... rotinas iniciais da aplicação ...

SimpleEventPublisher::instance()
->subscribe('registrations', RegistrationSubscriber::class)
->subscribe('payments', PaymentSubscriber::class);

// ... outras rotinas iniciais da aplicação ...
```

## 2. Enviando eventos

Para enviar eventos, a partir de qualquer lugar do sistema, basta invocar o método `publish()` do publicador:

```php
// arquivo qualquer, em algum lugar do sistema

$ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new UserRegistered('Ricardo', '99988877766', $ocurredOn);

SimpleEventPublisher::instance()
->publish('registrations', $event);
```

No exemplo acima, o evento "UserRegistered" (usuário cadastrado) é publicado no canal "registrations" (cadastros). O inscrito "RegistrationSubscriber" irá lidar com o evento, invocando as rotinas apropriadas para ele.

[◂ O padrão Publish/Subscribe](01-padrao-pubsub.md) | [Sumário da Documentação](indice.md) | [Implementando no "Intermediador de Mensagens" (Message Broker) ▸](03-implementando-no-broker.md)
-- | -- | --
