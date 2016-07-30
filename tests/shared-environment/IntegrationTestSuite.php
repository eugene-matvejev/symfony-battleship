<?php

namespace EM\Tests\Environment;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use EM\Tests\Environment\AssertionSuite\ResponseAssertionSuites;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @since 15.2
 */
abstract class IntegrationTestSuite extends WebTestCase
{
    use ResponseAssertionSuites;
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
    protected static $initiated;

    /**
     * return content of the file in located in tests/shared-fixtures directory
     *
     * @param string $filename
     *
     * @return string
     */
    public static function getSharedFixtureContent(string $filename) : string
    {
        return file_get_contents(static::getSharedFixturesDirectory() . "/$filename");
    }

    public static function getSharedFixturesDirectory() : string
    {
        return dirname(__DIR__) . '/shared-fixtures';
    }

    protected static function getKernelClass() : string
    {
        return \AppKernel::class;
    }

    /**
     * @coversNothing
     */
    public static function setUpBeforeClass()
    {
        if (null !== static::$initiated) {
            return;
        }

        static::$client = static::createClient();

        static::$container = static::$kernel->getContainer();
        static::$console = new Application(static::$kernel);
        static::$console->setAutoExit(false);

        static::$router = static::$container->get('router');

        static::$doctrine = static::$container->get('doctrine');
        static::$om = static::$doctrine->getManager();

        /**
         * SQLite is not supported yet
         * @link https://github.com/doctrine/dbal/pull/2402
         */
        $commands = [
            /** create test database */
            'doctrine:database:create'    => ['--if-not-exists' => true],
            /** reset test database schema */
            'doctrine:schema:drop'        => ['--full-database' => true, '--force' => true],
            /** flush test database schema */
            'doctrine:migrations:migrate' => [],
            /** seed test database with core data */
            'doctrine:fixtures:load'      => []
        ];

        foreach ($commands as $command => $args) {
            /** apply common commands options */
            $args['--env'] = 'test';
            $args['--quiet'] = true;
            $args['--no-interaction'] = true;
            $args['command'] = $command;
            try {
                static::$console->setCatchExceptions(false);
                static::$console->run(new ArrayInput($args));
            } catch (\Exception $e) {
                echo PHP_EOL . $e->getMessage() . PHP_EOL;
                echo PHP_EOL . $e->getTraceAsString() . PHP_EOL;

                throw $e;
            }
        }

        static::$initiated = true;
    }

    /**
     * able to invoke any non-static of object and return the result and throws exceptions if so
     *
     * useful to used to invoke non-public method of the class
     *
     * @param mixed  $object
     * @param string $methodName
     * @param array  $methodArguments
     *
     * @return mixed
     * @throws \Exception
     */
    protected function invokeMethod($object, string $methodName, array $methodArguments = [])
    {
        $method = (new \ReflectionClass(get_class($object)))->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $methodArguments);
    }
}
