<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\PlayerModel;
use EM\Tests\Environment\ContainerAwareTestSuite;
use EM\Tests\Environment\MockFactory\Entity\PlayerMockTrait;

/**
 * @see PlayerModel
 */
class PlayerModelTest extends ContainerAwareTestSuite
{
    use PlayerMockTrait;
    /**
     * @var PlayerModel
     */
    private $playerModel;

    protected function setUp()
    {
        parent::setUp();
        $this->playerModel = static::$container->get('battleship.game.services.player.model');
    }

    /**
     * @see PlayerModel::createOnRequest()
     * @test
     */
    public function createOnRequestOnExistingPlayer()
    {
        $player = $this->playerModel->createOnRequest('CPU');
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
        $player = $this->playerModel->createOnRequest('NON_EXISTING_USER');
        $this->assertNull($player->getId());
    }

    /**
     * @see  PlayerModel::isAIControlled()
     * @test
     */
    public function isAIControlledOn_MASK_NONE()
    {
        $player = $this->getPlayerMock('', PlayerModel::MASK_NONE);
        $this->assertFalse(PlayerModel::isAIControlled($player));
    }

    /**
     * @see  PlayerModel::isAIControlled()
     * @test
     */
    public function isAIControlledOn_MASK_AI_CONTROLLED()
    {
        $player = $this->getPlayerMock('', PlayerModel::MASK_AI_CONTROLLED);
        $this->assertTrue(PlayerModel::isAIControlled($player));
    }
}
