<?php

namespace EM\GameBundle\TestEnvironment;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 1.0
 */
class ExtendedTestCase extends WebTestCase
{
    /**
     * @var bool
     */
    protected static $setUp;
    /**
     * @var Application
     */
    protected static $consoleApp;
    /**
     * @var Client
     */
    private static $client;
    /**
     * @var ContainerInterface
     */
    private static $container;
    /**
     * @var Router
     */
    private static $router;

    /**
     * initialization
     */
    protected function setUp()
    {
        if(null === static::$setUp) {
            static::$setUp = true;

            self::$consoleApp = new Application(self::getClient()->getKernel());
            self::$consoleApp->setAutoExit(false);

            $this->runConsole('battleship:database:seed');
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
    protected function runConsole($command, array $options = [])
    {
        $options['--env'] = 'test';
        $options['--quiet'] = true;
        $options['--no-interaction'] = true;
        $options['--show-output'] = false;
        $options = array_merge($options, ['command' => $command]);
        try {
//            self::$consoleApp->setCatchExceptions(false);
            self::$consoleApp->run(new ArrayInput($options));
//            self::$consoleApp->setCatchExceptions(true);
        } catch(\Exception $ex) {
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
    protected function getConsoleApp() : Application
    {
        return self::$consoleApp;
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
     * @since 1.0
     */
    public function getClient() : Client
    {
        if(null === self::$client) {
            self::$client = static::createClient();
        }

        return self::$client;
    }

    /**
     * @since 1.0
     */
    public function getContainer() : ContainerInterface
    {
        if(null === self::$container) {
            self::$container = $this->getClient()->getContainer();

        }

        return self::$container;
    }

    /**
     * @since 2.0
     */
    public function getRouter() : Router
    {
        if(null === self::$router) {
            self::$router = $this->getContainer()->get('router');
        }

        return self::$router;
    }

    /**
     * @since 1.0
     */
    public function assertCorrectResponse(Response $response)
    {
        $this->assertGreaterThanOrEqual(Response::HTTP_OK, $response->getStatusCode());
        $this->assertLessThan(Response::HTTP_MULTIPLE_CHOICES, $response->getStatusCode());
    }

    /**
     * @since 1.0
     *
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