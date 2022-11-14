# Publicando um sinal de parada

--page-nav--

Quando o "Intermediador de Mensagens" está sendo usado em um ambiente de produção, ele precisa ser mantido em execução de forma ininterrupta. Já foi explicado como conseguir isso em [Mantendo o "Intermediador de Mensagens" sempre em execução](06-mantendo-a-execucao.md).

Foi dito que que o ["Supervisor"](http://supervisord.org/introduction.html) pode ficar monitorando o "Intermediador de Mensagens" para que, caso ele pare de executar, possa ser reiniciado automaticamente.

É possível enviar um "Evento de Sinal" para o "Intermediador de Mensagens", para forçar sua parada. Isso é muito útil quando novos Subscribers (Assinantes) forem adicionados no "Intermediador de Mensagens", pois a nova configuração só será efetivada quando ele for reiniciado.

Veja um exemplo de evento de sinal:

```php
$publisher = new PhpEventPublisher('localhost', 7703);

$event = new StopSignal();

$publisher->publish('channel-vormir', $event);
```

--page-nav--
