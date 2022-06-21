# Implementando um Subscriber

[◂ Usando comandos do Freep Console](03-usando-comandos-freep-console.md) | [Voltar ao índice](indice.md) | [Implementando um Evento ▸](05-implementando-um-evento.md)
-- | -- | --

## 1. O que é um Subscriber

Um Subscriber (assinante) é responsável pela manipulação dos eventos ocorridos. Ele deve conter a rotina responsável pela interpretação de um evento e saber o que fazer quando um evento daquele tipo acontecer.

Um novo Subscriber deve implementar a interface `Freep\PubSub\Subscriber\EventSubscriber`, que exige três métodos específicos:

### 1.1. O método "eventFactory"

Este método recebe uma `string` de identificação ($eventLabel) e um `array` associativo contendo os dados do evento ($eventData). Com base nessas informações o "eventFactory" deve fabricar o evento correto e devolvê-lo adequadamente no retorno. Caso não seja possível fabricar um evento adequado, `null` deverá ser retornado:

```php
/** @param array<string,mixed> $eventData */
public function eventFactory(string $eventLabel, array $eventData): ?Event
{
    // humm... vamos fabricar o UserRegistered
    if ($eventLabel === 'user-registered') { 
        return UserRegistered::factory($eventData);
    }

    return null;
}
```

### 1.2. O método "handleEvent"

Este método recebe a instância de um evento e deve invocar a regra de negócio adequada para ele. Por exemplo, se for um evento de cadastro, pode invocar algum repositório ou serviço que efetue o cadastro apropriado.

```php
public function handleEvent(Event $event): void
{
    if ($event instanceof UserRegistered) {
        // ...
        // rotina que cria um novo usuário no banco de dados

        return;
    }

    if ($event instanceof UserEmailChanged) {
        // ...
        // rotina que atualiza o email de um usuário existente no banco de dados
    }
}
```

### 1.3. O método "subscribedToEventType"

Este método deve retornar o tipo de evento que o Subscriber é capaz de manipular.
Apenas eventos deste tipo serão recebidos no método `handleEvent`.

```php
public function subscribedToEventType(): string
{
    // Apenas eventos deste tipo serão recebidos por este assinante
    return UserEvent::class;
}
```

**Importante**: Os tipo de evento pode ser determinado atraves de polimorfismo. Por exemplo, se `subscribedToEventType` retornar o tipo `UserEvent`, todos os eventos que implementarem a interface `UserEvent` serão recebidos no método `handleEvent`.

Abaixo, um exemplo de implementação para o "UserEventSubscriber":

```php
declare(strict_types=1);

namespace Foo\User;

use Foo\User\Events\UserEmailChanged;
use Foo\User\Events\UserRegistered;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;

class UserEventSubscriber implements EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        switch($eventLabel) {
            case 'user-registered':
                return UserRegistered::factory($eventData);

            case 'user-email-changed':
                return UserEmailChanged::factory($eventData);
        }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        if ($event instanceof UserRegistered) {
            // ...
            // rotina que cria um novo usuário no banco de dados

            return;
        }

        if ($event instanceof UserEmailChanged) {
            // ...
            // rotina que atualiza o email de um usuário existente no banco de dados
        }
    }

    public function subscribedToEventType(): string
    {
        // Apenas eventos deste tipo serão recebidos por este assinante
        return UserEvent::class;
    }
}
```

[◂ Usando comandos do Freep Console](03-usando-comandos-freep-console.md) | [Voltar ao índice](indice.md) | [Implementando um Evento ▸](05-implementando-um-evento.md)
-- | -- | --
