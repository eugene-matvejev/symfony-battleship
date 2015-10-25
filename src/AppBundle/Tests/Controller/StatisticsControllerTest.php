<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Library\ImprovedTestEnvironment\ExtendedAssertTestCase;
use Symfony\Component\HttpKernel\Tests\Controller;

class StatisticsControllerTest extends ExtendedAssertTestCase
{
    /**
     * @test
     * @see AppBundle\Controller\StatisticsController::overallAction()
     */
    public function overall()
    {
        $client = $this->getClient();

        $client->request('GET', $this->getRouter()->generate('battleship.game.api.statistics'));
        print $client->getResponse()->getContent();
        $this->assertJsonCorrectResponse($client->getResponse());
    }
}