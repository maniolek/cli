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
use Vegas\Mvc\ModuleManager;

/**
 * Class Boot
 * @package Vegas\Cli\ModuleManager\EventListener
 */
class Boot
{

    /**
     * @param Event $event
     * @param Application $application
     * @return mixed
     */
    public function boot(Event $event, Application $application)
    {
        $moduleManager = new ModuleManager($application);
        $moduleManager->setModulesDirectory($application->getConfig()->application->modulesDirectory);

        $modules = $application->getConfig()->application->modules;
        $moduleManager->registerModules($modules ? $modules->toArray() : []);

        $modules = $this->getModules($application->getConfig());
        $application->getConsole()->registerModules($modules);

        $application->getConfig()->merge($moduleManager->getConfigs($application->getModules()));
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