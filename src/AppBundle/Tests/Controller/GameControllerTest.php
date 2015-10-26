<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Library\ImprovedTestEnvironment\ExtendedAssertTestCase;

class GameControllerTest extends ExtendedAssertTestCase
{
    /**
     * @test
     * @see AppBundle\Controller\GameController::indexAction()
     */
    public function index()
    {
        $client = $this->getClient();

        $client->request('GET', $this->getRouter()->generate('battleship.game.gui.index'));

        $this->assertCorrectResponse($client->getResponse());
    }

    /**
     * @test
     * @see AppBundle\Controller\GameController::initAction()
     */
    public function init()
    {
        $client = $this->getClient();

        $client->request('POST', $this->getRouter()->generate('battleship.game.api.init'));

        $this->assertJsonCorrectResponse($client->getResponse());
    }

    /**
     * @test
     * @see AppBundle\Controller\GameController::turnAction()
     */
    public function turn()
    {
        $client = $this->getClient();

        $client->request('POST', $this->getRouter()->generate('battleship.game.api.turn'));

        $this->assertJsonCorrectResponse($client->getResponse());
    }
}
