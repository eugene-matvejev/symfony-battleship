<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\GameController;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see GameController
 */
class GameControllerTest extends ExtendedTestCase
{
    /**
     * @see GameController::indexAction()
     * @test
     */
    public function index()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_GET, $this->getRouter()->generate('battleship.game.gui.index'));

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::initAction()
     * @test
     */
    public function init()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_POST, $this->getRouter()->generate('battleship.game.api.init'));

        $this->assertJSONSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::turnAction()
     * @test
     */
    public function turn()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_PATCH, $this->getRouter()->generate('battleship.game.api.turn'));

        $this->assertJSONSuccessfulResponse($client->getResponse());
    }
}
