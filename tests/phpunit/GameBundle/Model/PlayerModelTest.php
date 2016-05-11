<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\PlayerModel;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory\Entity\PlayerMockTrait;

/**
 * @see PlayerModel
 */
class PlayerModelTest extends IntegrationTestSuite
{
    use PlayerMockTrait;
    /**
     * @var PlayerModel
     */
    private $playerModel;

    protected function setUp()
    {
        $this->playerModel = static::$container->get('battleship.game.services.player.model');
    }

    /**
     * @see PlayerModel::createOnRequest()
     * @test
     */
    public function createOnRequestOnExistingPlayer()
    {
        $player = $this->playerModel->createOnRequest('CPU');
        $this->assertEquals('CPU', $player->getName());
        $this->assertNotNull($player->getId());
    }

    /**
     * @see     PlayerModel::createOnRequest()
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
     * @see  PlayerModel::isAIControlled()
     * @test
     */
    public function isAIControlledOn_FLAG_NONE()
    {
        $player = $this->getPlayerMock('', PlayerModel::FLAG_NONE);
        $this->assertFalse(PlayerModel::isAIControlled($player));
    }

    /**
     * @see  PlayerModel::isAIControlled()
     * @test
     */
    public function isAIControlledOn_FLAG_AI_CONTROLLED()
    {
        $player = $this->getPlayerMock('', PlayerModel::FLAG_AI_CONTROLLED);
        $this->assertTrue(PlayerModel::isAIControlled($player));
    }
}
