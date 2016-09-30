<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\GameResultController;
use EM\Tests\Environment\AbstractControllerTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see GameResultController
 */
class GameResultControllerTest extends AbstractControllerTestCase
{
    /**
     * @see GameResultController::orderedByDateAction
     * @test
     */
    public function orderedByDateAction()
    {
        $client = static::$client;
        $client->request(
            Request::METHOD_GET,
            "/api/game-results/page/{$pageId}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );

        $this->assertSuccessfulJSONResponse($client->getResponse());
    }
}
