<?php

namespace EM\GameBundle\Tests\Controller;

use EM\GameBundle\TestEnvironment\ExtendedTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Tests\Controller;

class StatisticsControllerTest extends ExtendedTestCase
{
    /**
     * @test
     * @see GameBundle\Controller\StatisticsController::overallAction()
     */
    public function overall()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_GET, $this->getRouter()->generate('battleship.game.api.statistics', ['page' => 1]));

        $this->assertJsonCorrectResponse($client->getResponse());
    }
}