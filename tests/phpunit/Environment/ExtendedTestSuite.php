<?php

namespace EM\Tests\PHPUnit\Environment;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @since 1.0
 */
abstract class ExtendedTestSuite extends ExtendedAssertionSuite
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
     * @coversNothing
     */
    protected function setUp()
    {
        if (null === static::$setUp) {
            self::bootKernel();

            self::$client = static::createClient();

            self::$container = static::$kernel->getContainer();
            self::$consoleApp = new Application(self::getClient()->getKernel());
            self::$consoleApp->setAutoExit(false);

            self::$router = static::$container->get('router');
            self::$doctrine = static::$kernel->getContainer()->get('doctrine');
            self::$om = self::$doctrine->getManager();

            static::$setUp = true;

            $commandsToExecute = [
                // reset database
                'doctrine:database:drop'      => ['--if-exists' => true, '--force' => true],
                'doctrine:database:create'    => ['--if-not-exists' => true],
                // keep database schema up-to-date
                'doctrine:migrations:migrate' => [],
                // seed database with core data
                'doctrine:fixtures:load'      => ['--append' => true]
            ];

            foreach ($commandsToExecute as $command => $args) {
                $this->runConsoleCommand($command, $args);
            }
        }
    }

    /**
     * @param string $command
     * @param array  $options
     *
     * @throws \Exception
     */
    protected function runConsoleCommand($command, array $options = [])
    {
        $options['--env'] = 'test';
        $options['--no-interaction'] = true;
        $options['--quiet'] = true;
        $options = array_merge($options, ['command' => $command]);
        try {
            self::$consoleApp->setCatchExceptions(false);
            self::$consoleApp->run(new ArrayInput($options));
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

    public function getClient() : Client
    {
        return self::$client;
    }

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
     * @param mixed  $classInstance
     * @param string $methodName
     * @param array  $methodArguments
     *
     * @return mixed
     * @throws \Exception
     */
    protected function invokePrivateMethod($classInstance, string $methodName, array $methodArguments = [])
    {
        $method = (new \ReflectionClass(get_class($classInstance)))->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($classInstance, $methodArguments);
    }
}
