<?php

namespace EM\Tests\Behat\GameBundle\Controller;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Call as Behat;
use EM\Tests\Behat\AbstractContainerAwareContext;
use Symfony\Component\HttpFoundation\Request;

class GameResultControllerContext extends AbstractContainerAwareContext implements Context, SnippetAcceptingContext
{
    /**
     * @Given requesting :route with :paramKey and :paramValue API endpoint
     *
     * @param string $route
     * @param string $paramKey
     * @param string $paramValue
     */
    public function requestingWithAndApiEndpoint(string $route, string $paramKey, string $paramValue)
    {
        $this->_client->request(
            Request::METHOD_GET,
            static::$router->generate($route, [$paramKey => $paramValue]),
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
        $this->assertSuccessfulJSONResponse($this->_client->getResponse());
    }

    /**
     * @Then there should be :page and have :expectedAmountOfResults results
     *
     * @param int $page
     * @param int $expectedAmountOfResults
     */
    public function thereShouldBeAndHaveResults(int $page, int $expectedAmountOfResults)
    {
        $response = json_decode($this->_client->getResponse()->getContent());

        $this->assertInstanceOf(\stdClass::class, $response->meta);
        $this->assertEquals($page, $response->meta->currentPage);
        $this->assertCount($expectedAmountOfResults, $response->results);
    }
}
