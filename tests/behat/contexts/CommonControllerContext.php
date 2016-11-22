<?php

namespace EM\Tests\Behat;

use Behat\Behat\Context\Context;
use EM\GameBundle\DataFixtures\ORM\LoadPlayerData;
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
        static::$client = null;

        static::setUpBeforeClass();
    }

    /**
     * @Given request :route route via :method
     * @Given request API :route route via :method
     *
     * @param string   $route
     * @param string   $method
     * @param string[] $server
     */
    public function requestRoute(string $route, string $method, array $server = [])
    {
        $server['CONTENT_TYPE'] = 'application/json';
        $server['HTTP_accept']  = 'application/json';

        static::$client->request(
            $method,
            $route,
            [],
            [],
            $server
        );
    }

    /**
     * @Given I am authorized
     * @Given I am :notAuthorized authorized
     *
     * @param bool $notAuthorized
     */
    public function prepareClient(bool $notAuthorized = false)
    {
        static::$client = $this->getAuthorizedClient($notAuthorized ? '' : LoadPlayerData::TEST_PLAYER_EMAIL);
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
