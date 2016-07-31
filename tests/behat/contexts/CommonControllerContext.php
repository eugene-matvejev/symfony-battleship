<?php

namespace EM\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use EM\Tests\Environment\IntegrationTestSuite;
use Symfony\Bundle\FrameworkBundle\Client;

class CommonControllerContext extends IntegrationTestSuite implements Context, SnippetAcceptingContext
{
    /**
     * @var Client
     */
    protected static $_client;

    /**
     * @BeforeScenario
     */
    public static function beforeEachScenario()
    {
        parent::setUpBeforeClass();

        static::$_client = clone static::$client;
    }

    /**
     * @Given request :routeAlias API route via :method with :key :value
     *
     * @param string $routeAlias
     * @param string $method
     * @param string $key
     * @param string $value
     */
    public function requestAPIRouteWithParams(string $routeAlias, string $method, string $key, string $value)
    {
        $routeParameters = [];
        if ('~' !== $key && '~' !== $value) {
            $routeParameters[$key] = $value;
        }

        $this->requestRoute(
            $routeAlias,
            $method,
            $routeParameters,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );
    }

    /**
     * @Given request :routeAlias route via :method
     *
     * @param string   $routeAlias
     * @param string   $method
     * @param string[] $routeParameters
     * @param string[] $serverParameters
     */
    public function requestRoute(string $routeAlias, string $method, array $routeParameters = [], array $serverParameters = [])
    {
        static::$_client->request(
            $method,
            static::$router->generate($routeAlias, $routeParameters),
            [],
            [],
            $serverParameters
        );
    }

    /**
     * @Then observe successful response
     */
    public function observeSuccessfulResponse()
    {
        $this->assertSuccessfulResponse(static::$_client->getResponse());
    }

    /**
     * @Then observe redirected response
     */
    public function observeRedirectedResponse()
    {
        $this->assertRedirectedResponse(static::$_client->getResponse());
    }

    /**
     * @Then observe unsuccessful response
     */
    public function observeUnsuccessfulResponse()
    {
        $this->assertUnsuccessfulResponse(static::$_client->getResponse());
    }

    /**
     * @Then observe response status code :statusCode
     *
     * @param int $statusCode
     */
    public function observeResponseStatusCode(int $statusCode)
    {
        $this->assertEquals($statusCode, static::$_client->getResponse()->getStatusCode());
    }

    /**
     * @Then observe successful JSON response
     */
    public function observeSuccessfulJsonResponse()
    {
        $this->assertSuccessfulJSONResponse(self::$_client->getResponse());
    }
}
