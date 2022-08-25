# Mantendo o "Intermediador de Mensagens" sempre em execução

--page-nav--

É muito importante que o "Intermediador de Mensagens" se mantenha executando ininterruptamente. Porém, em casos muito específicos (um problema interno do servidor, hardware, memória, etc), o observador poderá parar e precisará ser iniciado novamente.

Para não se preocupar em reiniciá-lo manualmente, é uma prática excelente usar um mecanismo secundário que fique verificando se o "Intermediador de Mensagens" está "vivo".

Uma sugestão é fazer uso do ["Supervisor"](http://supervisord.org/introduction.html), um sistema que permite controlar processos.

Com o Supervisor instalado, basta [criar uma "rotina"](http://supervisord.org/running.html#adding-a-program) que reinicie o "Intermediador de Mensagens" sempre que ele parar.

Por exemplo:

```ini
[program:restart_broker]
command=/path/to/example pubsub:broker -c 'tests/Example/config-file.php' -v
autostart=true
autorestart=true
redirect_stderr=true
stderr_logfile=/path/to/broker.err.log
stdout_logfile=/path/to/broker.out.log
```

No programa "restart_broker" mostrado acima, todas as mensagens (de erro ou de escape) serão armazenadas em arquivos de log para consulta (broker.err.log e broker.out.log). Isso é muito bom para identificar qual o problema ocorrido e encontrar uma solução mais facilmente.

--page-nav--
