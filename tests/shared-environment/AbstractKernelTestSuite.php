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
        static::$om        = static::$container->get('doctrine')->getManager();
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

    static public function getSharedFixturesDirectory() : string
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
