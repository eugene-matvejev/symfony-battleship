<?php

namespace AppBundle\Library\ImprovedTestEnvironment;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

class CustomTestCase extends WebTestCase
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

    /**
     * @return Client
     */
    public function getClient()
    {
        if(!isset(self::$client))
            self::$client = static::createClient();

        return self::$client;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if(!isset(self::$container))
            self::$container = $this->getClient()->getContainer();

        return self::$container;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        if(!isset(self::$router))
            self::$router = $this->getContainer()->get('router');

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
     */
    public function assertJSONCorrectResponse(Response $response)
    {
        $this->assertCorrectResponse($response);

        $json = json_decode($response->getContent(), true);
    }
}