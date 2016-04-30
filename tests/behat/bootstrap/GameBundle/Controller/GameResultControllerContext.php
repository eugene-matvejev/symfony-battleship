<?php

namespace EM\Tests\Behat\GameBundle\Controller;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Call as Behat;
use EM\Tests\Behat\AbstractContainerAwareContext;
use Symfony\Component\HttpFoundation\Request;

class GameResultControllerContext extends AbstractContainerAwareContext implements Context, SnippetAcceptingContext
{
    protected $_client;
    /**
     * @Given request api endpoint
     */
    public function requestApiEndpoint()
    {
        $client = clone static::$client;

        $client->request(
            Request::METHOD_GET,
            static::$router->generate('battleship.game.api.game.results', ['page' => 1]),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );
    }

    /**
     * @Then get results
     */
    public function getResults()
    {
        throw new PendingException();
    }

    /**
     * @Then there are :arg1 results
     */
    public function thereAreResults($arg1)
    {
        throw new PendingException();
    }
}


