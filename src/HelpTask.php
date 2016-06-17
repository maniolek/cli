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
        $declaredClasses = get_declared_classes();
        $taskClasses = preg_grep('/^.*Task\\\\.*Task$/x', $declaredClasses);

        $taskInfo = "\n\t- App\\Task\n\t- (module name)\\Task\n\t- Vegas\\Task";

        if (count($taskClasses)) {
            $this->putSuccess('Following tasks found:');
        } else {
            $this->putText('Not found any tasks with these namespaces: ' . $taskInfo);
        }

        foreach ($taskClasses as $class) {
            $this->putText(' - ' . $class);
        }

        if (count($taskClasses)) {
            $this->putText("If there's a missing tasks, make sure they have one of the following namespaces:" . $taskInfo);
        }

    }
}