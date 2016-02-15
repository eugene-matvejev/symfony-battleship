<?php

namespace EM\Tests\PHPUnit\Environment;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @since 1.0
 */
class ExtendedTestCase extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected static $container;
    /**
     * @var RegistryInterface
     */
    protected static $doctrine;
    /**
     * @var ObjectManager
     */
    protected static $om;
    /**
     * @var RouterInterface
     */
    protected static $router;
    /**
     * @var Application
     */
    protected static $consoleApp;
    /**
     * @var Client
     */
    protected static $client;
    /**
     * @var bool
     */
    protected static $setUp;
    /**
     * @var KernelInterface
     */
    protected static $kernel;

    /**
     * @coversNothing
     */
    protected function setUp()
    {
        if (null === static::$setUp) {
            self::$client = static::createClient();

            self::$kernel = static::createKernel();
            self::$kernel->boot();

            self::$container = static::$kernel->getContainer();
            self::$consoleApp = new Application(self::getClient()->getKernel());
            self::$consoleApp->setAutoExit(false);

            self::$router = static::$container->get('router');
            self::$doctrine = static::$kernel->getContainer()->get('doctrine');
            self::$om = self::$doctrine->getManager();

            static::$setUp = true;

            $this->runConsole('battleship:database:seed');
        }
    }

    /**
     * @param string $command
     * @param array  $options
     *
     * @throws \Exception
     */
    protected function runConsole($command, array $options = [])
    {
        $options['--env'] = 'test';
        $options['--no-interaction'] = true;
        $options['--quiet'] = true;
        $options = array_merge($options, ['command' => $command]);
        try {
//            self::$consoleApp->setCatchExceptions(false);
            self::$consoleApp->run(new ArrayInput($options));
//            self::$consoleApp->setCatchExceptions(true);
        } catch (\Exception $e) {
            echo PHP_EOL . $e->getMessage() . PHP_EOL;
            echo PHP_EOL . $e->getTraceAsString() . PHP_EOL;

            throw new \Exception();
        }
    }

    /**
     * @since 3.5
     */
    protected function getConsoleApp() : Application
    {
        return self::$consoleApp;
    }

//    /**
//     * Gets the display returned by the last execution of the command.
//     *
//     * @param ContainerAwareCommand $command
//     *
//     * @return string The display of command execution result
//     */
//    protected function executeCommand(ContainerAwareCommand $command)
//    {
//        $console = $this->getConsoleApp();
//        $commandName = $command->getName();
//        if (!$console->has($commandName)) {
//            $this->getConsoleApp()->add($command);
//        }
//        $commandTester = new CommandTester($console->find($commandName));
//        $commandTester->execute(['command' => $commandName]);
//
//        return $commandTester->getDisplay();
//    }

    /**
     * @since 1.0
     */
    public function getClient() : Client
    {
        return self::$client;
    }

    /**
     * @since 1.0
     */
    public function getContainer() : ContainerInterface
    {
        return self::$container;
    }

    /**
     * @since 2.0
     */
    public function getRouter() : Router
    {
        return self::$router;
    }

    /**
     * @since 3.4
     */
    public function getDoctrine() : RegistryInterface
    {
        return self::$doctrine;
    }

    /**
     * @since 3.4
     */
    public function getObjectManager() : ObjectManager
    {
        return self::$om;
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

    /**
     * @param string $className
     * @param mixed  $classInstance
     * @param string $methodName
     * @param array  $methodArguments
     *
     * @return mixed
     * @throws \Exception
     */
    protected function invokePrivateMethod(string $className, $classInstance, string $methodName, array $methodArguments = [])
    {
        $reflected = new \ReflectionClass($className);
        $method = $reflected->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($classInstance, $methodArguments);
    }
}
