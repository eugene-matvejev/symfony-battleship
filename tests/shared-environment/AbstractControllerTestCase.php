<?php

namespace EM\Tests\Environment;

use EM\Tests\Environment\AssertionSuite\ResponseAssertionSuites;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @since 22.7
 */
abstract class AbstractControllerTestCase extends AbstractKernelTestSuite
{
    use ResponseAssertionSuites;
    /**
     * @var Client
     */
    protected static $client;
    /**
     * @var Router
     */
    protected static $router;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$client = static::$container->get('test.client');
        static::$router = static::$container->get('router');
    }
}
