# Freep Pub/Sub

[English](../../readme.md) | [Português](leiame.md)
-- | --

## Sinopse

Este repositório contém as funcionalidades necessárias para executar um sistema de Publish/Subscribe, possibilitando assinar e publicar eventos.

```bash
composer require ricardopedias/freep-pubsub
```

Para informações detalhadas, consulte o [Sumário da Documentação](indice.md).

## Como funciona

### 1. Execute o servidor de eventos

Na raiz deste projeto, existe um comando de terminal para exemplificar o processo. Pode ser usado da seguinte maneira:

```bash
# executando no PHP local
./example pubsub:broker -c 'tests/Example/config-file.php' -v

ou

# executando no PHP Dockerizado
./composer pubsub-server
```

```text
# Terminal do servidor em execução

➜ Using JsonEventSerializer serializer
➜ Verbose mode enabled
✔ The publish/subscriber server has been started in tcp://localhost:7703
```

Pronto! O servidor de eventos está executando na porta 7703, que ficará disponível para receber eventos.

> **Nota**: enquanto o servidor estiver em execução, o terminal atual ficará em modo de "observação", aguardando para receber eventos. A opção "-v" ativa o modo "verboso", de forma que, sempre que um um evento for recebido, o servidor fará uma notificação no teminal em execução.

### 3. Envie eventos para o servidor

Com o servidor em execução, basta abrir outro terminal e usar o comando abaixo para enviar alguns eventos de teste:

```bash
# executando no PHP local
./example pubsub:client-test -d localhost -p 7703 -v

ou

# executando no PHP Dockerizado
./composer pubsub-client
```

Os dois terminais irão reagir ao envio dos eventos, mostrando as informações correspondentes:

```text
# Terminal onde foram enviados os eventos

✔ Publish event of type 'EventOne' to channel 'channel-vormir' in tcp://localhost:7703
✔ Publish event of type 'EventTwo' to channel 'channel-vormir' in tcp://localhost:7703
✔ Publish event of type 'EventTwo' to channel 'channel-mordor' in tcp://localhost:7703
✔ Publish event of type 'EventTwo' to channel 'channel-greenville' in tcp://localhost:7703
➜ Published Events
➜ Used memory: 1921928
```

```text
# Terminal do servidor que recebeu os eventos

➜ [2022-06-03 17:06:09]: Message of type 'EventOne' received on channel 'channel-vormir'
Message dispatched to SubscriberOne
Message dispatched to SubscriberTwo

➜ [2022-06-03 17:06:09]: Message of type 'EventTwo' received on channel 'channel-vormir'
Message dispatched to SubscriberTwo

...
```

## Características

- Feito para o PHP 8.0 ou superior;
- Codificado com boas práticas e máxima qualidade;
- Bem documentado e amigável para IDEs;
- Feito com TDD (Test Driven Development);
- Implementado com testes de unidade usando PHPUnit;
- Feito com :heart: &amp; :coffee:.

## Creditos

[Ricardo Pereira Dias](https://www.ricardopedias.com.br)
