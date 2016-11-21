<?php

namespace EM\Tests\PHPUnit\GameBundle\Request;

use EM\GameBundle\Request\GameInitiationRequest;
use EM\Tests\Environment\AbstractKernelTestSuite;

/**
 * @see GameInitiationRequest
 */
class GameInitiationRequestTest extends AbstractKernelTestSuite
{
    /**
     * @see GameInitiationRequest::parse
     * @test
     */
    public function parseOnValid()
    {
        $fixture  = $this->getSharedFixtureContent('game-initiation-requests/valid/valid-1-opponent-7x7.json');
        $expected = json_decode($fixture);
        $request  = new GameInitiationRequest($fixture);

        $this->assertCount(count($expected->coordinates), $request->getCoordinates());
        $this->assertEquals($expected->size, $request->getSize());
        $this->assertEquals($expected->playerName, $request->getPlayerName());
    }
}
