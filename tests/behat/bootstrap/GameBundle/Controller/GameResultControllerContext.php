<?php

namespace EM\Tests\Behat\GameBundle\Controller;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Call as Behat;
use EM\Tests\Behat\AbstractContainerAwareContext;
use Symfony\Component\HttpFoundation\Request;

class GameResultControllerContext extends AbstractContainerAwareContext implements Context, SnippetAcceptingContext
{
    /**
     * @Given I am requesting :route with :param and :value API endpoint
     *
     * @param string $route
     * @param string $param
     * @param string $value
     */
    public function iAmRequestingWithAndApiEndpoint(string $route, string $param, string $value)
    {
        $this->_client->request(
            Request::METHOD_GET,
            static::$router->generate($route, [$param => $value]),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );
    }

    /**
     * @Then I should get successful response
     */
    public function iShouldGetSuccessfulResponse()
    {
        $this->assertSuccessfulJSONResponse($this->_client->getResponse());
    }

    /**
     * @Then there should be :arg1 results
     */
    public function thereShouldBeResults(string $page, string $results)
    {
        $parsedJson = json_decode($this->_client->getResponse()->getContent());

        $parsedJson;
        if('asd' != 1) {

        }
        $this->assertInstanceOf(\stdClass::class, $parsedJson->meta);
        $this->assertEquals()
    }
}
