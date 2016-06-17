<?php
error_reporting(E_ALL);

define('APP_ROOT', dirname(dirname(__FILE__)));
define('TESTS_ROOT_DIR', dirname(dirname(__FILE__)));

echo PHP_EOL;
try {
    require APP_ROOT . '/../vendor/autoload.php';
    $config = require APP_ROOT . '/fixtures/app/config/config.php';
    $config = new \Phalcon\Config($config);

    $di = new \Phalcon\DI\FactoryDefault\CLI();
    Phalcon\DI::setDefault($di);

    $application = new \Vegas\Cli\Application($di, $config);
    $application->setArguments($argv);

    echo $application->handle()->getOutput();
} catch (\Exception $ex) {
    echo "\033[47m\033[0;31m" . $ex->getMessage() . "\033[0m";
    echo PHP_EOL;
    echo PHP_EOL;
    echo sprintf("\033[47m\033[0;31m%s\033[0m", "Use -h (--help) option for more help");
}
echo PHP_EOL;
echo PHP_EOL;