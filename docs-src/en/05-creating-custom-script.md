# Creating a custom script

--page-nav--

As mentioned before, the library comes with [two terminal commands](04-using-script-example.md) that are used in the script called "example", located in the repository root.

See the content of the script:

```php
#!/bin/php
<?php

// Load Composer autoloader
include __DIR__ . "/vendor/autoload.php";

use Iquety\Console\Terminal;
use Iquety\PubSub\EventLoop;

array_shift($argv);

$terminal = new Terminal(__DIR__ . "/src");

// loads the commands available in the library
$terminal->loadCommandsFrom(EventLoop::commandPath());

$terminal->run($argv);
```

The purpose of using the [Iquety\Console](https://github.com/iquety/console) library is that it makes command portability much easier. You can implement commands in several separate modules and load them all from a single point using the "Terminal->loadCommandsFrom()" method.

Therefore, just copy the content of the `example` script and create a new script in the project that will make use of Publish/Subscribe to better contextualize its objective and facilitate the execution of the "Message Intermediator".

For example, you can create a script called `myapp` for your project, containing the following content:

```php
#!/bin/php
<?php

// Load Composer autoloader
include __DIR__ . "/vendor/autoload.php";

use Iquety\Console\Terminal;
use Iquety\PubSub\EventLoop;

array_shift($argv);

$terminal = new Terminal(__DIR__ . "/src");

// load Pub/Sub commands
$terminal->loadCommandsFrom(EventLoop::commandPath());

// load my project commands
$terminal->loadCommandsFrom(__DIR__ . '/MyApp/Commands');

$terminal->run($argv);
```

Note that the above script starts with `#!/bin/php`. This notation tells the operating system's terminal that this script should be interpreted by the "/bin/php" program. That way, you don't need to type `php myapp`, just `./myapp`:

```bash
./myapp --help
```

> **Note**: on unix or derivative systems, to be able to directly invoke a script (eg ./myapp), it must have the permission to execute. This is achieved by the command `chmod a+x myapp`.

--page-nav--
