<?php

namespace EM\Tests\Environment;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @since 11.3
 */
abstract class ContainerAwareTestSuite extends ClientResponsesAssertionSuite
{
    /**
     * @var ContainerInterface
     */
    protected static $container;
    /**
     * @var Application
     */
    protected static $console;
    /**
     * @var Registry
     */
    protected static $doctrine;
    /**
     * @var ObjectManager
     */
    protected static $om;
    /**
     * @var Client
     */
    protected static $client;
    /**
     * @var Router
     */
    protected static $router;
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
            static::$client = static::createClient();

            static::$container = static::$kernel->getContainer();
            static::$console = new Application(static::$kernel);
            static::$console->setAutoExit(false);

            static::$router = static::$container->get('router');

            static::$doctrine = static::$container->get('doctrine');
            static::$om = static::$doctrine->getManager();

            $commands = [
                /** reset test database */
                'doctrine:database:create'    => ['--if-not-exists' => true],
                /** PostgreSQL have some limitations, that is why not simple drop database */
                'doctrine:schema:drop'        => ['--full-database' => true, '--force' => true],
                /** keep database schema up-to-date */
                'doctrine:migrations:migrate' => [],
                /** seed database with core data */
                'doctrine:fixtures:load'      => ['--append' => true]
            ];

            foreach ($commands as $command => $args) {
                $this->runConsoleCommand($command, $args);
            }

            static::$setUp = true;
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
            static::$console->setCatchExceptions(false);
            static::$console->run(new ArrayInput($options));
        } catch (\Exception $e) {
            echo PHP_EOL . $e->getMessage() . PHP_EOL;
            echo PHP_EOL . $e->getTraceAsString() . PHP_EOL;

            throw new \Exception();
        }
    }

//
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
//

    /**
     * invokes non-public method of the class and returns invoke result as well as throws Exception if it happen.
     *
     * @param mixed  $object
     * @param string $methodName
     * @param array  $methodArguments
     *
     * @return mixed
     * @throws \Exception
     */
    protected function invokeNonPublicMethod($object, string $methodName, array $methodArguments = [])
    {
        $method = (new \ReflectionClass(get_class($object)))->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $methodArguments);
    }
}
