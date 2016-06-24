<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @company Amsterdam Standard Sp. z o.o.
 * @homepage http://cmf.vegas
 */

namespace Vegas\Cli\ModuleManager\EventListener;

use Phalcon\Events\Event;
use Vegas\Cli\Application;
use Vegas\Cli\BootEventListenerInterface;

/**
 * Class Boot
 * @package Vegas\Cli\ModuleManager\EventListener
 */
class Boot implements BootEventListenerInterface
{
    /**
     * @param Event $event
     * @param Application $application
     * @return mixed
     */
    public function boot(Event $event, Application $application)
    {
        $modules = $this->getModules($application->getConfig());
        $application->getConsole()->registerModules($modules);
    }

    protected function getModules($config)
    {
        $directory = $config->application->modulesDirectory;
        $modules = [];

        foreach ($config->application->modules as $key => $module) {
            $moduleFile = $directory . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Module.php';
            $modules[$key]['path'] = $moduleFile;
        }

        return $modules;
    }
}