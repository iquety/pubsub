# Como funciona

[◂ Voltar ao índice](indice.md) | [Usando um script de terminal ▸](02-usando-script-de-terminal.md)
-- | --

## 1. Arquitetura orientada a eventos

A primeira coisa a fazer quando implementamos uma arquitetura orientada a eventos é ter um meio de verificar ininterruptamente a aplicação, aguardando algum evento acontecer. Quando um evento é identificado, ele deve ser despachado para as rotinas ou módulos do sistema que estejam preparados para lidar com aquele tipo de evento.

Existem diversas formas de fazer isso. No entanto, esta biblioteca trata de um padrão arquitetônico específico de encaminhamento de eventos chamado de Publish/Subscribe (Publicar/Inscrever em português).

## 2. O padrão Publish/Subscribe

Este padrão arquitetônico consiste em um padrão de projeto chamado "Observer" ([clique aqui para mais informações](https://refactoring.guru/pt-br/design-patterns/observer)). Resumidamente, um "Observer" possui uma lista de "inscritos", como em um canal do Youtube. Cada inscrito tem suas próprias necessidades e recebe notificações sempre que algo de seu interesse acontece.

Existem duas maneiras de umplementar um "Observer" para Publish/Subscribe, com vantagens e desvantagens em cada cenário:

### 2.1. Bootstrap da aplicação

monomono

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

...

[◂ Voltar ao índice](indice.md) | [Usando um script de terminal ▸](02-usando-script-de-terminal.md)
-- | --
