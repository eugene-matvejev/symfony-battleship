<?php

namespace EM\Tests\PHPUnit\GameBundle\Request;

use EM\GameBundle\Request\GameInitiationRequest;
use EM\Tests\Environment\IntegrationTestSuite;
use JMS\Serializer\Annotation as Serializer;

/**
 * @since 18.0
 */
class GameInitiationRequestTest extends IntegrationTestSuite
{
    /**
     * @var GameInitiationRequest
     */
    private $request;

    protected function setUp()
    {
        $this->request = new GameInitiationRequest();
    }

    /*********************************** PARSING & ASSIGNMENT ***********************************/
    /**
     * @see GameInitiationRequest::parse
     *
     * @test
     */
    public function parseOnValid()
    {
        $fixture = $this->getSharedFixtureContent('init-game-request-2-players-7x7.json');
        $expected = json_decode($fixture);

        $this->request->parse($fixture);

        $this->assertCount(count($expected->coordinates), $this->request->getCoordinates());
        $this->assertEquals($expected->size, $this->request->getSize());
        $this->assertEquals($expected->playerName, $this->request->getPlayerName());
    }
}
