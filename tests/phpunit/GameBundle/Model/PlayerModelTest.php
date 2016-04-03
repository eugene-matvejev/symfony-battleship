<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\PlayerModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;

/**
 * @see PlayerModel
 */
class PlayerModelTest extends ExtendedTestSuite
{
    /**
     * @var PlayerModel
     */
    private $playerModel;

    protected function setUp()
    {
        parent::setUp();
        $this->playerModel = $this->getContainer()->get('battleship.game.services.player.model');
    }

    /**
     * @see PlayerModel::TYPE_CPU
     * @test
     */
    public function playerTypeCPU()
    {
        $this->assertNotEquals(PlayerModel::TYPE_HUMAN, PlayerModel::TYPE_CPU);
        $this->assertContains(PlayerModel::TYPE_CPU, PlayerModel::TYPES_ALL);
    }

    /**
     * @see PlayerModel::TYPE_HUMAN
     * @test
     */
    public function playerTypeHuman()
    {
        $this->assertNotEquals(PlayerModel::TYPE_CPU, PlayerModel::TYPE_HUMAN);
        $this->assertContains(PlayerModel::TYPE_HUMAN, PlayerModel::TYPES_ALL);
    }

    /**
     * @see PlayerModel::ALL_TYPES
     * @test
     */
    public function playerTypesAll()
    {
        $this->assertCount(2, PlayerModel::TYPES_ALL);
    }

    /**
     * @see PlayerModel::getTypes
     * @test
     */
    public function getTypes()
    {
        foreach ($this->playerModel->getTypes() as $playerType) {
            $this->assertContains($playerType->getId(), PlayerModel::TYPES_ALL);
        }

        $this->assertCount(2, $this->playerModel->getTypes());
    }

    /**
     * @see PlayerModel::createOnRequest
     * @test
     */
    public function createOnRequestOnExistingPlayer()
    {
        $player = $this->playerModel->createOnRequest('CPU');
        $this->assertNotNull($player->getId());
    }

    /**
     * @see PlayerModel::createOnRequest
     * @test
     */
    public function createOnRequestOnNonExistingPlayer()
    {
        $player = $this->playerModel->createOnRequest('Not existing PlayerName');
        $this->assertNull($player->getId());
    }
}
