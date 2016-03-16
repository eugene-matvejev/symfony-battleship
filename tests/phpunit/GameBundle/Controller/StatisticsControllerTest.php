<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\StatisticsController;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Tests\Controller;

/**
 * @see StatisticsController
 */
class StatisticsControllerTest extends ExtendedTestCase
{
    /**
     * @see StatisticsController::overallAction()
     * @test
     */
    public function overall()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_GET, $this->getRouter()->generate('battleship.game.api.game.results', ['page' => 1]));

        $this->assertJSONSuccessfulResponse($client->getResponse());
    }
}
