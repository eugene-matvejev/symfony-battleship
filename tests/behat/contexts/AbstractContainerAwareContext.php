<?php

namespace EM\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use EM\Tests\Environment\IntegrationTestSuite;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractContainerAwareContext extends IntegrationTestSuite implements Context, SnippetAcceptingContext
{
    /**
     * @var Client
     */
    protected static $_client;

    /**
     * @Given request API :route route via :method with :paramKey :paramValue
     *
     * @param string $route
     * @param string $method
     * @param string $paramKey
     * @param string $paramValue
     */
    public function requestApiRouteViaWith(string $route, string $method, string $paramKey, string $paramValue)
    {
        $routeParams = [];
        if (!empty($paramKey) && !empty($paramValue)) {
            $routeParams[$paramKey] = $paramValue;
        }
        static::$_client->request(
            $method,
            static::$router->generate($route, $routeParams),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
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
     * @Then observe unsuccessful response
     */
    public function observeUnsuccessfulResponse()
    {
        $this->assertUnsuccessfulResponse(static::$_client->getResponse());
    }

    /**
     * @BeforeScenario
     */
    public static function beforeEachScenario()
    {
        parent::setUpBeforeClass();

        static::$_client = clone static::$client;
    }

    /**
     * @BeforeScenario
     */
    public static function beforeEachScenario()
    {
        parent::setUpBeforeClass();

        static::$_client = clone static::$client;
    }
}
