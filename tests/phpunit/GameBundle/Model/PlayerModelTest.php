<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\PlayerModel;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory;

/**
 * @see PlayerModel
 */
class PlayerModelTest extends IntegrationTestSuite
{
    /**
     * @var PlayerModel
     */
    private $playerModel;

    protected function setUp()
    {
        $this->playerModel = static::$container->get('battleship.game.services.player.model');
    }

    /**
     * @see PlayerModel::createOnRequest
     * @test
     */
    public function createOnRequestOnExistingPlayer()
    {
        $player = $this->playerModel->createOnRequest('CPU');

        $this->assertEquals('CPU', $player->getName());
        $this->assertNotNull($player->getId());
    }

    /**
     * @see     PlayerModel::createOnRequest
     * @test
     *
     * @depends createOnRequestOnExistingPlayer
     */
    public function createOnRequestOnNonExistingPlayer()
    {
        $player = $this->playerModel->createOnRequest('NON-EXISTING-USER');

        $this->assertEquals('NON-EXISTING-USER', $player->getName());
        $this->assertNull($player->getId());
    }

    /**
     * should return false if player is not marked by @see PlayerModel::FLAG_AI_CONTROLLED flag
     *
     * @see PlayerModel::isAIControlled
     * @test
     */
    public function isAIControlledOnFlagNone()
    {
        $this->assertFalse(PlayerModel::isAIControlled(MockFactory::getPlayerMock('')));
    }

    /**
     * should return true if player marked by @see PlayerModel::FLAG_AI_CONTROLLED flag
     *
     * @see PlayerModel::isAIControlled
     * @test
     */
    public function isAIControlledOnFlagAIControlled()
    {
        $this->assertTrue(PlayerModel::isAIControlled(MockFactory::getAIPlayerMock('')));
    }
}
