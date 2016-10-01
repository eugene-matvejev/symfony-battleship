<?php

namespace EM\Tests\Environment;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @since 22.7
 */
abstract class AbstractKernelTestSuite extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KernelInterface
     */
    protected static $kernel;
    /**
     * @var ContainerInterface
     */
    protected static $container;
    /**
     * @var Registry
     */
    protected static $doctrine;
    /**
     * @var ObjectManager
     */
    protected static $om;
    /**
     * @var bool
     */
    protected static $initiated;

    public static function setUpBeforeClass()
    {
        if (null !== static::$initiated) {
            return;
        }

        static::initKernel();
        static::initDatabase();

        static::$initiated = true;
    }

    private static function createKernel() : \AppKernel
    {
        return new \AppKernel('test', true);
    }

    private static function initKernel()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        static::$container = static::$kernel->getContainer();
        static::$doctrine  = static::$container->get('doctrine');
        static::$om        = static::$doctrine->getManager();
    }

    private static function initDatabase()
    {
        $console = new Application(static::$kernel);
        $console->setAutoExit(false);

        /**
         * SQLite is not supported yet
         *
         * @link https://github.com/doctrine/dbal/pull/2402
         */
        $commands = [
            /** create test database @see CreateDatabaseDoctrineCommand */
            'doctrine:database:create'    => ['--if-not-exists' => true],
            /** reset test database schema @see DropSchemaDoctrineCommand */
            'doctrine:schema:drop'        => ['--full-database' => true, '--force' => true],
            /** flush test database schema @see MigrationsMigrateDoctrineCommand */
            'doctrine:migrations:migrate' => [],
            /** seed test database with core data @see LoadDataFixturesDoctrineCommand */
            'doctrine:fixtures:load'      => []
        ];

        foreach ($commands as $command => $args) {
            /** apply common commands options */
            $args['--env']            = 'test';
            $args['--quiet']          = true;
            $args['--no-interaction'] = true;
            $args['command']          = $command;
            try {
                $console->setCatchExceptions(false);
                $console->run(new ArrayInput($args));
            } catch (\Exception $e) {
                echo PHP_EOL . $e->getMessage() . PHP_EOL;
                echo PHP_EOL . $e->getTraceAsString() . PHP_EOL;

                throw $e;
            }
        }
    }

    /**
     * return content of the fixture file in located in tests/shared-fixtures directory
     *
     * @param string $filename
     *
     * @return string
     */
    protected function getSharedFixtureContent(string $filename) : string
    {
        return file_get_contents(static::getSharedFixturesDirectory() . "/{$filename}");
    }

    protected function getSharedFixturesDirectory() : string
    {
        return dirname(__DIR__) . '/shared-fixtures';
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
