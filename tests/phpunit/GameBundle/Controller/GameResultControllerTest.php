<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\GameResultController;
use EM\Tests\Environment\IntegrationTestSuite;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see GameResultController
 */
class GameResultControllerTest extends IntegrationTestSuite
{
    /**
     * @see GameResultController::orderedByDateAction
     * @test
     */
    public function orderedByDateAction()
    {
        $client = clone static::$client;
        $client->request(
            Request::METHOD_GET,
            static::$router->generate('battleship.game.api.game.results', ['page' => 1]),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );

        $this->assertSuccessfulJSONResponse($client->getResponse());
    }
}
