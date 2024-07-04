# O padrão Publish/Subscribe

--page-nav--

## 1. Arquitetura orientada a eventos

A primeira coisa a fazer quando implementamos uma arquitetura orientada a eventos é ter um meio de verificar ininterruptamente a aplicação, aguardando algum evento acontecer. Quando um evento é identificado, ele deve ser despachado para as rotinas ou módulos do sistema que estejam preparados para lidar com aquele tipo de evento.

## 2. A abordagem Publish/Subscribe

Existem diversas formas de implementar uma arquitetura orientada a eventos (APIs Rest, Observers, Ferramentas de Mensageria, etc).

Esta biblioteca implementa um **padrão arquitetônico** específico de encaminhamento de eventos chamado de Publish/Subscribe (Publicar/Assinar em português).

Este padrão arquitetônico faz uso de um **padrão de projeto** chamado ["Observer"](https://refactoring.guru/pt-br/design-patterns/observer). Resumidamente, um "Observer" possui uma lista de "assinantes" (como em um canal do Youtube). Cada assinante tem suas próprias necessidades e recebe notificações sempre que algo de seu interesse acontece.

O exemplo fictício abaixo é autoexplicativo:

```php
$observer = new Observer();

// inscreve três ouvintes de notícias em dois canais diferentes
$observer->subscribe('channel-name', Ricardo::class);
$observer->subscribe('channel-name', Roberto::class);
$observer->subscribe('channel-other-name', Ronaldo::class);

// publica uma notícia para cada canal de ouvintes
$observer->publish('channel-name', AssistirNetflix::class);
$observer->publish('channel-other-name', AssistirDisneyPlus::class);
```

Perceba que, no código acima, a notícia 'AssistirNetflix' será recebida por 'Ricardo' e 'Roberto'. Já a notícia 'AssistirDisneyPlus' será recebida somente por 'Ronaldo'.

Com base no exemplo fictício, chamaremos os ouvintes de "Subscribers" e as notícias de "Events".

## 3. Tipos de implementação

Existem duas maneiras de implementar um "Observer" para Publish/Subscribe, com vantagens e desvantagens em cada cenário:

Implementação | Prós (2) | Contras (4)
-- | -- | --
No "Ponto de Início" da aplicação (Bootstrap) | É ideal para comunicação dos módulos dentro de uma mesma aplicação. | Cria um acoplamento com a implementação do bootstrap.
-- | Simples de implementar e entender, mesmo para quem não conhece a arquitetura Pub/Sub. |  Aplicações diferentes precisam reimplementar a configuração do bootstrap
-- | -- | Necessário maior cuidado na hora de adicionar novos assinantes.
-- | -- | Aplicações feitas com linguagens diferentes de PHP (ex.: Java, Ruby, Python) não podem enviar eventos.
**Implementação** | **Prós (4)** | **Contras (2)**
No "Intermediador de Mensagens" (Message Broker) | Ideal para integrar aplicações diferentes. | Pode ser mais difícil de entender para aqueles que não estão familiarizados com Pub/Sub.
-- | Centraliza a configuração dos assinantes no "Intermediador de Mensagens". | É preciso executar e manter o servidor de eventos sempre ativo para receber e despachar os eventos ocorridos.
-- | Provê o desacoplamento real entre as partes que se comunicam. | --
-- | Uma aplicação feita com linguagem diferente de PHP também pode enviar eventos para se comunicar | --

A seguir, mais informações sobre os dois tipos de implementação.

--page-nav--
