CLI for Vegas CMF 2.0
===========================

[![Travis Status](https://travis-ci.org/maniolek/cli.svg)](https://travis-ci.org/maniolek/cli)
[![Coverage Status](https://coveralls.io/repos/github/maniolek/cli/badge.svg?branch=master)](https://coveralls.io/github/maniolek/cli?branch=master)

Compatible with: Phalcon >= 2.0

### Getting Started

1. Clone repo
 ```ssh git clone git@github.com:vegas-cmf/cli.git```
2. Copy cli.php file to `PROJECT_HOME/cli/` path. Use this command:   
 - ```ssh mkdir cli; cp vendor/vegas-cmf/cli/Stub/cli.php cli/```
3. To run a task, you can use two of following commands:   
 - ```php cli/cli.php app:taskClassName actionName``` - for application task    
 - ```php cli/cli.php app:moduleName:taskClassName actionName``` - for module task   
4. If your task have setup an option you can use short notation or the long one:   
 - ```php cli/cli.php app:custom test -f 123```   
 - ```php cli/cli.php app:custom test --foo 123```
   
   
### Application Task

```php
namespace App\Task;

use Vegas\Cli\Task\Action;
use Vegas\Cli\Task\Option;
use Vegas\Cli\TaskAbstract;

class CustomTask extends TaskAbstract
{

    public function setupOptions()
    {
        $action = new \Vegas\Cli\Task\Action('test', 'Test action');

        $foo = new Option('foo', 'f', 'Foo option. Usage app:custom test -f numberOfSth');
        $foo->setValidator(function($value) {
            if (!is_numeric($value)) return false;
            return true;
        });
        $foo->setRequired(true);
        $action->addOption($foo);

        $this->addTaskAction($action);
    }

    public function testAction()
    {
        $this->putText($this->getArg(0));
        $this->putText($this->getOption('f'));
        $this->putObject($this->getArgs());
    }

```

##### Usage
``` php cli/cli.php app:custom test --foo 123 ```
   
   
### Module Task

```php
namespace Test\Task;

use Vegas\Cli\Task\Action;
use Vegas\Cli\Task\Option;
use Vegas\Cli\TaskAbstract;

class CustomTask extends TaskAbstract
{
    public function setupOptions()
    {
        $action = new Action('test', 'test task');
        $option = new Option('foo', 'f');
        $action->addOption($option);
        $this->addTaskAction($action);
    }

    public function testAction()
    {
        $this->putText('test');
    }
}
```

##### Usage
``` php cli/cli.php app:test:custom test -f 123 ```
