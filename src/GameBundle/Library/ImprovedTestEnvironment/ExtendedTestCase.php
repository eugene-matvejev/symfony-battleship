<?php

namespace GameBundle\Library\ImprovedTestEnvironment;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

class ExtendedTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    private static $container;
    /**
     * @var ContainerInterface
     */
    private static $router;
    /**
     * @var Router
     */
    private static $client;

    protected static $setUp;
    protected $consoleApp;

    /**
     * Initialization
     */
    protected function setUp()
    {
        if (null === static::$setUp) {
            static::$setUp = true;
            $this->runConsole('doctrine:migrations:migrate', ['--no-interaction' => true]);
            $this->runConsole('babylon:doctrine:fixtures:load');
        }
    }


    /**
     * Run $command on Symfony console
     *
     * @param string $command
     * @param array  $options
     *
     * @throws \Exception
     */
    protected function runConsole($command, array $options = array())
    {
        $options['-e'] = 'test';
        $options['-q'] = null;
        $options = array_merge($options, ['command' => $command]);
        try {
            $this->getConsoleApp()->setCatchExceptions(false);
            $this->getConsoleApp()->run(new ArrayInput($options));
            $this->getConsoleApp()->setCatchExceptions(true);
        } catch (\Exception $ex) {
            print $ex->getMessage();
            print $ex->getTraceAsString();
            throw new \Exception();
        }
    }

    /**
     * Return application for run console command
     *
     * @return Application
     */
    protected function getConsoleApp()
    {
        if (null === $this->consoleApp) {
            $this->consoleApp = new Application(self::getClient()->getKernel());
            $this->consoleApp->setAutoExit(false);
        }

        return $this->consoleApp;
    }

    /**
     * Gets the display returned by the last execution of the command.
     *
     * @param ContainerAwareCommand $command
     *
     * @return string The display of command execution result
     */
    protected function executeCommand(ContainerAwareCommand $command)
    {
        $console = $this->getConsoleApp();
        $commandName = $command->getName();
        if (!$console->has($commandName)) {
            $this->getConsoleApp()->add($command);
        }
        $commandTester = new CommandTester($console->find($commandName));
        $commandTester->execute(['command' => $commandName]);

        return $commandTester->getDisplay();
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if(null === self::$client) {
            self::$client = static::createClient();
        }

        return self::$client;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if(null === self::$container) {
            self::$container = $this->getClient()->getContainer();

        }

        return self::$container;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        if(null === self::$router) {
            self::$router = $this->getContainer()->get('router');
        }

        return self::$router;
    }

    /**
     * @param Response $response
     */
    public function assertCorrectResponse(Response $response)
    {
        $this->assertLessThan($response->getStatusCode(), Response::HTTP_MULTIPLE_CHOICES);
    }

    /**
     * @param Response $response
     *
     * @return array
     */
    public function assertJSONCorrectResponse(Response $response)
    {
        $this->assertCorrectResponse($response);

        return json_decode($response->getContent(), true);
    }
}