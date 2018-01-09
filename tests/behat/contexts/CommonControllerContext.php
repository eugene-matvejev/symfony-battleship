<?php

namespace EM\Tests\Behat;

use Behat\Behat\Context\Context;
use EM\FoundationBundle\DataFixtures\ORM\UsersFixture;
use EM\Tests\Environment\AbstractControllerTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class CommonControllerContext extends AbstractControllerTestCase implements Context
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * @BeforeScenario
     */
    public static function beforeEachScenario()
    {
        static::$client    = null;
        static::$initiated = null;

        static::setUpBeforeClass();
    }

    /**
     * @Given request API :route route via :method
     *
     * @param string $route
     * @param string $method
     * @param string $content
     */
    public function requestAPIRoute(string $route, string $method, string $content = null)
    {
        $this->requestRoute(
            $route,
            $method,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            $content
        );
    }

    /**
     * @Given request :route route via :method
     * @Given request API :route route via :method
     *
     * @param string   $route
     * @param string   $method
     * @param string[] $server
     * @param string   $content
     */
    public function requestRoute(string $route, string $method, array $server = [], string $content = null)
    {
        $server['CONTENT_TYPE'] = 'application/json';
        $server['HTTP_accept']  = 'application/json';

        static::$client->request(
            $method,
            $route,
            [],
            [],
            $server,
            $content
        );
    }

    /**
     * @Given I am authorized
     * @Given I am :notAuthorized authorized
     *
     * @param bool $authorized
     */
    public function prepareClient(bool $authorized = false)
    {
        static::$client = $this->getAuthorizedClient($authorized ? '' : UsersFixture::TEST_PLAYER_EMAIL);
    }

    /**
     * @Then observe response status code :statusCode
     *
     * @param int $statusCode
     */
    public function observeResponseStatusCode(int $statusCode)
    {
        $response = static::$client->getResponse();

        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * @Then observe valid JSON response
     */
    public function observeValidJsonResponse()
    {
        $response = static::$client->getResponse();

        $this->assertJson($response->getContent());
    }

    /**
     * @Given observe redirection to :route
     *
     * @param string $route
     */
    public function observeRedirectionTo(string $route)
    {
        $response = static::$client->getResponse();

        $this->assertEquals($route, $response->headers->get('location'));
    }
}
