<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\GameResultController;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Tests\Controller;

/**
 * @see GameResultController
 */
class GameResultControllerTest extends ExtendedTestCase
{
    /**
     * @see GameResultController::orderedByDateAction()
     * @test
     */
    public function orderedByDateAction()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_GET, $this->getRouter()->generate('battleship.game.api.game.results', ['page' => 1]));

        $this->assertJSONSuccessfulResponse($client->getResponse());
    }
}
