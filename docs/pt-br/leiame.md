# Freep Publish/Subscribe

## Sinopse

Este repositório contém as funcionalidades necessárias para executar um sistema de Publish/Subscribe,
possibilitando incrições e publicações de eventos.

Para informações detalhadas, consulte a documentação em [Inglês](../en/index.md) ou em [Português](indice.md). Veja também este 'readme' em [Inglês](../../readme.md).

## Como usar

### 1. Execute o servidor de eventos

Na raiz deste projeto, existe um script para exemplificar o processo.
Pode ser usado da seguinte maneira:

```bash
./example pubsub -d localhost -p 8080 -t -v
```

```text
# Terminal do servidor em execução

✔ The publish/subscriber server has been started in tcp://localhost:8080
```

Isso irá executar o servidor de evento servidor na porta 8080, que ficará disponível para receber mensagens.

> Nota: enquanto o servidor estiver em execução, o terminal atual ficará em modo de observação, aguardando eventos. A opção '-v' ativa o modo 'verboso', de forma que, sempre que um um evento for recebido, o servidor fará uma notificação no teminal em execução.

### 3. Enviar eventos para o servidor

Com o servidor em execução, basta abrir outro terminal e usar o comando abaixo para enviar alguns eventos de teste.
Observe as mensagens aparecerem nos dois terminais.

```bash
./example pubsub:client-test -d localhost -p 8080 -v
```

```text
# Terminal do envio

✔ Publish event of type 'EventOne' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-mordor' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-greenville' in tcp://localhost:8080
➜ Published Events
➜ Used memory: 1921928
```

```text
# Terminal do servidor em execução

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

## Sumário

- [Como usar](01-como-usar.md)
- [Usando um script de terminal](02-usando-script-de-terminal.md)
- [Usando comandos do Freep Console](03-usando-comandos-freep-console.md)
- [Implementando um Subscriber](04-implementando-um-subscriber.md)
- [Implementando um Evento](05-implementando-um-evento.md)
- [Publicando um evento](06-publicando-um-evento.md)
- [Publicando um sinal de parada](07-publicando-um-sinal-de-parada.md)
- [Evoluindo a biblioteca](08-evoluindo-a-biblioteca.md)

## Creditos

[Ricardo Pereira Dias](https://www.ricardopedias.com.br)
