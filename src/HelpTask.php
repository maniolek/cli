<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Cli;

use Vegas\Cli\Task\Action;

class HelpTask extends \Vegas\Cli\TaskAbstract
{
    /**
     * Task must implement this method to set available options
     *
     * @return mixed
     */
    public function setupOptions()
    {
        $this->addTaskAction(new Action('list', 'List all actions'));
    }

    public function listAction()
    {
        $declaredClasses = $this->getAvailableTasks();

        $taskInfo = "\n\t- App\\Task\n\t- (module name)\\Task\n\t- Vegas\\Task";

        if (count($declaredClasses)) {
            $this->putSuccess('Following tasks found:');
        } else {
            $this->putText('Not found any tasks with these namespaces: ' . $taskInfo);
        }

        foreach ($declaredClasses as $class) {
            $this->putText(' - ' . $class);
        }

        if (count($declaredClasses)) {
            $this->putText("If there's a missing tasks, make sure they have one of the following namespaces:" . $taskInfo);
        }

    }

    /**
     * @return array
     */
    protected function getAvailableTasks()
    {
        $response = [];
        $directories = [];

        $directories[] = [
            'path' => APP_ROOT . '/app/tasks',
            'namespace' => '\App\Task'
        ];

        $config = $this->getDI()->get('config');
        $modules = $config->application->modules;

        foreach ($modules as $module) {
            $modulePath = APP_ROOT . DIRECTORY_SEPARATOR . $config->application->modulesDirectory .
                DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Task';
            $directories[] = [
                'path' => $modulePath,
                'namespace' => '\\' . $module .'\Task'
            ];
        }

        foreach ($directories as $directory) {

            if (!file_exists($directory['path'])) {
                continue;
            }

            $directoryIterator = new \DirectoryIterator($directory['path']);
            foreach ($directoryIterator as $fileInfo) {

                if ($fileInfo->isDot() || $fileInfo->isDir()) {
                    continue;
                }

                $taskNamespace = $directory['namespace'] . '\\' . $fileInfo->getBasename('.php');

                $class = new \ReflectionClass($taskNamespace);
                if ($class->isSubclassOf('\Vegas\Cli\TaskAbstract')) {
                    $response[] = $class->getName();
                }

            }
        }

        return $response;
    }
}