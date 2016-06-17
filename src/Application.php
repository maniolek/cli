<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @company Amsterdam Standard Sp. z o.o.
 * @homepage http://cmf.vegas
 */

namespace Vegas\Cli;

use Phalcon\Cli\Dispatcher;
use Phalcon\Config;
use Phalcon\Events\Manager;
use Phalcon\Http\ResponseInterface;
use Vegas\Cli\ModuleManager\EventListener\Boot as ModuleManagerBootEventListener;
use Vegas\Mvc\Autoloader\EventListener\Boot as AutoloaderBootEventListener;
use Vegas\Cli\Task\EventListener\Boot as TaskBootEventListener;
use Vegas\Mvc\View\EventListener\Boot as ViewBootEventListener;
use Vegas\Mvc\Application\EventListener\Boot as ApplicationBootEventListener;
use Vegas\Mvc\Di\Injector\EventListener\Boot as DiBootEventListener;
use Vegas\Mvc\Router;

/**
 * Class Application
 * @package Vegas\Mvc
 */
class Application extends \Vegas\Mvc\Application
{
    /** @var Console $console */
    protected $console;

    /** @var   */
    protected $arguments;

    /**
     * @return $this
     */
    protected function attachBootstrapEvents()
    {
        $this->getEventsManager()->attach('application', new ModuleManagerBootEventListener());
        $this->getEventsManager()->attach('application', new AutoloaderBootEventListener());
        $this->getEventsManager()->attach('application', new TaskBootEventListener());
        $this->getEventsManager()->attach('application', new DiBootEventListener());
        $this->getEventsManager()->attach('application', new ViewBootEventListener());
        $this->getEventsManager()->attach('application', new ApplicationBootEventListener());

        return $this;
    }

    public function __construct($dependencyInjector, Config $config)
    {
        $this->console = new Console();
        $this->console->setDI($dependencyInjector);

        parent::__construct($dependencyInjector, $config);
    }

    /**
     * @param null $uri
     * @return mixed|object
     * @throws \Exception
     */
    public function handle($uri = null)
    {
        /**
         * Allow one bootstrap per application instance
         */
        if (!$this->isBootstrapped) {
            $this->isBootstrapped = $this->bootstrap();
        }

        /**
         * Refuse to continue if boot process failed
         */
        if (!$this->isBootstrapped) {
            return false;
        }

        $taskLoader = new Loader();
        $arguments = $taskLoader->parseArguments($this->console, $this->arguments);

        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->getDI()->getShared('dispatcher');
        $dispatcher->setParam('args', $arguments['params']);

        return $this->console->handle($arguments);
    }


    /**
     * Sets command line arguments
     *
     * @param $args
     */
    public function setArguments($args)
    {
        $this->arguments = $args;
    }

    /**
     * @return Console
     */
    public function getConsole()
    {
        return $this->console;
    }
}