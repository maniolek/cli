<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests;

use Phalcon\DI;
use Vegas\CLi\Application;
use Vegas\Cli\Bootstrap;

class BootstrapTest extends TestCase
{
    protected $di;

    public function setUp()
    {
        parent::setUp();
        $this->di = DI::getDefault();
    }

    public function testShouldChangeDI()
    {
        $app = $this->application;
        $this->assertInstanceOf('\Phalcon\DI\FactoryDefault\CLI', $app->di);
        $app->setDI($this->di);
        $this->assertInstanceOf(get_class($this->di), $app->di);
    }

    public function testShouldThrowExceptionAboutNotFoundTask()
    {
        $cli = $this->application;
        $cli->setArguments(['cli/cli.php']);

        try {
            $cli->handle();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Exception\TaskNotFoundException', $ex);
        }

        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'foo',
            2 => 'bar'
        ));

        try {
            $cli->handle();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Exception\TaskNotFoundException', $ex);
        }

        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'foo:bar',
            2 => 'b'
        ));

        try {
            $cli->handle();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Exception\TaskNotFoundException', $ex);
        }

        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:bar',
            2 => 'foo'
        ));

        try {
            $cli->handle();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Phalcon\CLI\Dispatcher\Exception', $ex);
        }
    }

    public function testShouldThrowExceptionAboutMissingArguments()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test'
        ));

        try {
            $cli->handle();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Task\Exception\MissingRequiredArgumentException', $ex);
        }
    }

    public function testShouldThrowExceptionAboutInvalidArgument()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-f',
            4 => 'string'
        ));

        try {
            $cli->handle();
        } catch (\Exception $ex) {
            $this->assertTrue((bool)strstr($ex->getMessage(), 'Invalid argument'));
        }
    }

    public function testShouldThrowExceptionAboutInvalidOption()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-d',
            4 => 'string'
        ));

        try {
            $cli->handle();
        } catch (\Exception $ex) {
            $this->assertTrue((bool)strstr($ex->getMessage(), 'Invalid option'));
        }
    }

    public function testShouldReturnTaskHelp()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:test:custom',
            2 => 'test',
            3 => '-h'
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('Usage', $returnedValue);
        $this->assertContains('Options', $returnedValue);
    }

    public function testShouldReturnTaskHelpWhenNoActionSpecified()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom'
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('Available actions', $returnedValue);
    }

    public function testShouldGetHelp()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:help',
            2 => 'list'
        ));

        $returnedValue = $cli->handle()->getOutput();

        //$this->assertSame('Vegas\Cli\HelpTask', $returnedValue);
    }

    public function testShouldExecuteApplicationTask()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-f',
            4 => 123
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('123', $returnedValue);

        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-f',
            4 => 123
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('123', $returnedValue);
    }

    public function testShouldExecuteModuleTask()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:test:foo',
            2 => 'test'
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('test', $returnedValue);
    }

    public function testShouldReturnTextInResponse()
    {
        $colorsOutput = $this->getMockForTrait('\Vegas\Cli\ColorsOutputTrait');

        $runTask = function($action) {
            $cli = $this->application;
            $cli->setArguments(array(
                0 => 'cli/cli.php',
                1 => 'app:custom',
                2 => $action
            ));

            $returnedValue = $cli->handle()->getOutput();

            return $returnedValue;
        };
        $this->assertContains($colorsOutput->getColoredString('Error message', 'red'), $runTask('testError'));
        $this->assertContains($colorsOutput->getColoredString('Warning message', 'yellow'), $runTask('testWarning'));
        $this->assertContains($colorsOutput->getColoredString('Success message', 'green'), $runTask('testSuccess'));
        $this->assertContains($colorsOutput->getColoredString('Some text', 'light_blue'), $runTask('testText'));
        $this->assertContains(print_r(['key' => 'value'], true), $runTask('testObject'));
    }

    public function testShouldLoadLibraryTask()
    {
        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:task:cache',
            2 => 'clean'
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('Cleaning cache', $returnedValue);

        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:fake:fake',
            2 => 'test'
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('Vegas\Fake\FakeTask', $returnedValue);

        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:fake_nested:fake',
            2 => 'test'
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('Vegas\Fake\Nested\FakeTask', $returnedValue);


        $cli = $this->application;
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:test',
            2 => 'test'
        ));

        $returnedValue = $cli->handle()->getOutput();

        $this->assertContains('test', $returnedValue);

//        $cli = $this->application;
//        $cli->setArguments(array(
//            0 => 'cli/cli.php',
//            1 => 'vegas:test:test',
//            2 => 'test'
//        ));
//
//        $returnedValue = $cli->handle()->getOutput();
//
//        $this->assertContains('test', $returnedValue);

    }

    public function testShouldThrowExceptionAboutNotExistingModuleTask()
    {
//        $cli = $this->application;
//        $cli->bootstrap();
//
//        //remove Test module
//
//        $modules = $cli->getModuleManager();
//
//        $modulesWithoutTest = array_merge([], $modules);
//        unset($modulesWithoutTest['Test']);
//        $application->registerModules($modulesWithoutTest);
//
//        $cli->setArguments(array(
//            0 => 'cli/cli.php',
//            1 => 'app:test:foo',
//            2 => 'test'
//        ));
//
//        $exception = null;
//        try {
//            $cli->handle();
//        } catch (\Exception $e) {
//            $exception = $e;
//        }
//        $this->assertInstanceOf('Vegas\Cli\Exception\TaskNotFoundException', $exception);
//
//        $application->registerModules($modules);
    }
}