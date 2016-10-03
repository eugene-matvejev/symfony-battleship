<?php

namespace EM\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use EM\Tests\Environment\AbstractControllerTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class CommonControllerContext extends AbstractControllerTestCase implements Context, SnippetAcceptingContext
{
    /**
     * @BeforeScenario
     */
    public static function beforeEachScenario()
    {
        static::setUpBeforeClass();
    }

    /**
     * @Given request API :route route via :method
     *
     * @param string $route
     * @param string $method
     */
    public function requestAPIRoute(string $route, string $method)
    {
        $this->requestRoute(
            $route,
            $method,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );
    }

    /**
     * @Given request :route route via :method
     *
     * @param string   $route
     * @param string   $method
     * @param string[] $server
     */
    public function requestRoute(string $route, string $method, array $server = [])
    {
        static::$client->request(
            $method,
            $route,
            [],
            [],
            $server
        );
    }

    /**
     * @Then observe response status code :statusCode
     *
     * @param int $statusCode
     */
    public function observeResponseStatusCode(int $statusCode)
    {
        $this->assertEquals($statusCode, static::$client->getResponse()->getStatusCode());
    }

    /**
     * @Then observe valid JSON response
     */
    public function observeValidJsonResponse()
    {
        $this->assertJson(static::$client->getResponse()->getContent());
    }

    /**
     * @Given observe redirection to :route
     *
     * @param string $route
     */
    public function observeRedirectionTo(string $route)
    {
        $this->assertEquals($route, static::$client->getResponse()->headers->get('location'));
    }
}
