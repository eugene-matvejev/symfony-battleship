<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see GameBundle\Controller\GameController
*/
class GameControllerTest extends ExtendedTestCase
{
    /**
     * @test
     * @see GameBundle\Controller\GameController::indexAction()
     */
    public function index()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_GET, $this->getRouter()->generate('battleship.game.gui.index'));

        $this->assertCorrectResponse($client->getResponse());
    }

    /**
     * @test
     * @see GameBundle\Controller\GameController::initAction()
     */
    public function init()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_POST, $this->getRouter()->generate('battleship.game.api.init'));

        $this->assertJsonCorrectResponse($client->getResponse());
    }

    /**
     * @test
     * @see GameBundle\Controller\GameController::turnAction()
     */
    public function turn()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_PATCH, $this->getRouter()->generate('battleship.game.api.turn'));

        $this->assertJsonCorrectResponse($client->getResponse());
    }
}
