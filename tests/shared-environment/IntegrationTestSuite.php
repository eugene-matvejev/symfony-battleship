<?php

namespace EM\Tests\Environment;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Model\PlayerSessionModel;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 15.2
 */
abstract class IntegrationTestSuite extends WebTestCase
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
    protected static $initiated;
    /**
     * @var string
     */
    private static $authHeader = 'HTTP_' . PlayerSessionModel::AUTHORIZATION_HEADER;

    protected function setUp()
    {
        static::$om->clear();
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

    public function assertSuccessfulResponse(Response $response)
    {
        $this->assertTrue($response->isSuccessful());
    }

    public function assertSuccessfulJSONResponse(Response $response)
    {
        $this->assertSuccessfulResponse($response);

        $this->assertJson($response->getContent());
    }

    public function assertSuccessfulXMLResponse(Response $response)
    {
        $this->assertSuccessfulResponse($response);

        $xmlElement = simplexml_load_string($response->getContent(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->assertInstanceOf(\SimpleXMLElement::class, $xmlElement);
    }

    public function assertUnsuccessfulResponse(Response $response)
    {
        $this->assertTrue($response->isClientError() || $response->isServerError());
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

    protected function getAuthorizedClient() : Client
    {
        return $this->createClientWithAuthHeader(static::$om->getRepository('GameBundle:PlayerSession')->find(1)->getHash());
    }

    protected function getNonAuthorizedClient() : Client
    {
        return $this->createClientWithAuthHeader('');
    }

    private function createClientWithAuthHeader(string $hash) : Client
    {
        $client = static::$client;
        $client->setServerParameter(static::$authHeader, $hash);

        return $client;
    }

    /*** ****************************** HELPERS ****************************** ***/
    public static function getRootDirectory() : string
    {
        return dirname(__DIR__);
    }

    public static function getSharedFixturesDirectory() : string
    {
        return static::getRootDirectory() . '/shared-fixtures';
    }

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

    protected static function getKernelClass() : string
    {
        return \AppKernel::class;
    }
}
