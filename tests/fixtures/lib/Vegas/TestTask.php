<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @company Amsterdam Standard Sp. z o.o.
 * @homepage http://cmf.vegas
 */

namespace Vegas;

use Vegas\Cli\Task\Action;
use Vegas\Cli\TaskAbstract;

class TestTask extends TaskAbstract
{
    public function setupOptions()
    {
        $action = new Action('test', 'test task');
        $this->addTaskAction($action);
    }

    public function testAction()
    {
        $this->putText('test');
    }
}