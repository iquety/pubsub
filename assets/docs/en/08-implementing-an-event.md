# Implementando um Evento

--page-nav--

## 1. O que é um Evento

Um evento é o encapsulamento de informações que representam uma ação ocorrida em um determinado momento no tempo. Eventos devem sempre ser nomeados no passado, pois são alguma coisa que já aconteceu (ex.: UserRegistered, PasswordChanged etc). As "consequências" de um evento são determinadas pelo assinante (Subscriber), como explicado em [Implementando um Subscriber](07-implementando-um-subscriber.md).

## 2. Como implementar um Evento

Um novo evento deve cumprir o contrato da interface `Freep\PubSub\Event\Event` e seus valores devem ser fornecidos somente através do construtor, não sendo possível alterá-los após a instanciação:

```php
class UserRegistered implements Freep\PubSub\Event\Event
{
    public function __construct(
        private string $name,
        private string $cpf,
        private DateTimeImmutable $ocurredOn
    ) {
    }
}
```

> **Dica**: Getters podem ser implementados, desde que não alterem o estado atual do evento e funcionem apenas como acessores de dados.

A interface `Freep\PubSub\Event\Event` exige cinco métodos específicos:

### 1.1. O método "factory"

Este método recebe um `array` associativo contendo os dados do evento ($values). Com base nesses valores, o "factory" deve fabricar o evento devolvê-lo adequadamente no retorno.

**Importante**: O valor de retorno sempre deve ser um evento do mesmo tipo, sendo que a impossibilidade de se fabricar um novo evento deve disparar uma exceção.

**Mais importante ainda**: Caso a implementação dos valores de um evento existente precisem mudar, seja por uma evolução no sistema ou por uma correção necessária, este método deverá garantir o máximo possível de retrocompatibilidade com os dados implementados em versões anteriores. Isso é necessário para garantir que módulos ou subsistemas que ainda não se atualizaram, possam continuar enviando eventos, mesmo que incompletos.

```php
/** @param array<string,mixed> $values */
public static function factory(array $values): Event
{
    // na versão anterior 'cpf' se chamava 'document'
    if (isset($values['document']) === true) {
        $values['cpf'] = $values['document'];
    }

    return new self(
        $values['name'],
        $values['cpf'],
        new DateTimeImmutable($values['ocurredOn'])
    );
}
```

### 1.2. O método "label"

Este método deve devolver uma identificação textual única, que nomeie o evento de forma clara e objetiva. Deve ser um nome declarativo e facilmente reconhecível por humanos.

Bons exemplos de identificação são 'user_registered' ou 'user.registered'.

Péssimos exemplos são 'registered', '12345' ou 'abst345sd'.

```php
public function label(): string
{
    return 'user.registered';
}
```

### 1.3. O método "ocurredOn"

Este método deve devolver uma instancia de `\DateTimeImmutable`, contendo o valor para a data e hora atuais.

```php
public function ocurredOn(): DateTimeImmutable
{
    return $this->ocurredOn;
}
```

### 1.4. O método "sameEventAs"

Este método deve comparar duas instâncias para determinar se tratam-se do mesmo evento.

```php
/** @param UserRegistered $other */
public function sameEventAs(Event $other): bool
{
    return $other instanceof UserRegistered
        && $this->name() === $other->name()
        && $this->cpf() === $other->cpf()
        && $this->ocurredOn() === $other->ocurredOn();
}
```

### 1.4. O método "toArray"

Este método deve devolver um `array` associativo contendo os valores do evento em tipos primitivos simples: `string`, `int`, `float` e `bool`.

```php
public function toArray(): array
{
    return [
        'cpf'       => $this->cpf,
        'name'      => $this->name,
        'ocurredOn' => $this->ocurredOn->format('Y-m-d H:i:s')
    ];
}
```

Abaixo, um exemplo de implementação para o evento "UserRegistered":

```php
declare(strict_types=1);

namespace Foo\User\Events;

use DateTimeImmutable;
use Freep\PubSub\Event\Event;

class UserRegistered implements Event
{
    public function __construct(
        private string $name,
        private string $cpf,
        private DateTimeImmutable $ocurredOn
    ) {
    }

    public function label(): string
    {
        return 'user.registered';
    }

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        // na versão anterior 'cpf' se chamava 'document'
        if (isset($values['document']) === true) {
            $values['cpf'] = $values['document'];
        }
        
        return new self(
            $values['name'],
            $values['cpf'],
            new DateTimeImmutable($values['ocurredOn'])
        );
    }

    public function cpf(): string
    {
        return $this->cpf;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function ocurredOn(): DateTimeImmutable
    {
        return $this->ocurredOn;
    }

    /** @param UserRegistered $other */
    public function sameEventAs(Event $other): bool
    {
        return $other instanceof EventOne
            && $this->name() === $other->name()
            && $this->cpf() === $other->cpf()
            && $this->ocurredOn() === $other->ocurredOn();
    }

    public function toArray(): array
    {
        return [
            'cpf'       => $this->cpf,
            'name'      => $this->name,
            'ocurredOn' => $this->ocurredOn->format('Y-m-d H:i:s')
        ];
    }
}
```

--page-nav--
