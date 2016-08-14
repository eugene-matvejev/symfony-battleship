<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\DataFixtures\ORM\LoadPlayerData;
use EM\GameBundle\Model\PlayerModel;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see PlayerModel
 */
class PlayerModelTest extends IntegrationTestSuite
{
    /**
     * @var PlayerModel
     */
    private static $playerModel;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$playerModel = static::$container->get('battleship_game.service.player_model');
    }

    /*********************************** STATIC HELPERS ***********************************/
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

    /*********************************** AI CONTROLLED PLAYER ***********************************/
    /**
     * should return existing player controlled by AI, as it existed before
     *
     * @see      PlayerModel::createOnRequestAIControlled
     * @test
     *
     * @depends  isAIControlledOnFlagNone
     * @requires isAIControlledOnFlagAIControlled
     */
    public function createOnRequestAIControlledOnExistingPlayer()
    {
        $player = static::$playerModel->createOnRequestAIControlled('CPU 0');

        $this->assertEquals('CPU 0', $player->getEmail());
        $this->assertTrue(PlayerModel::isAIControlled($player));

        /** because player is already persisted */
        $this->assertNotNull($player->getId());
    }

    /**
     * should return new player controlled by AI, as it didn't exist before
     *
     * @see      PlayerModel::createOnRequestAIControlled
     * @test
     *
     * @depends  isAIControlledOnFlagNone
     * @requires isAIControlledOnFlagAIControlled
     */
    public function createOnRequestAIControlledOnNonExistingPlayer()
    {
        $player = static::$playerModel->createOnRequestAIControlled('NON-EXISTING-CPU-PLAYER');

        $this->assertEquals('NON-EXISTING-CPU-PLAYER', $player->getEmail());
        $this->assertTrue(PlayerModel::isAIControlled($player));

        /** because player is not persisted yet */
        $this->assertNull($player->getId());
    }

    /**
     * should throw exception, because existed Player is not controlled By AI
     *
     * @see      PlayerModel::createOnRequestAIControlled
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\PlayerException
     *
     * @depends  isAIControlledOnFlagNone
     * @requires isAIControlledOnFlagAIControlled
     */
    public function createOnRequestAIControlledOnNonExistingHumanPlayer()
    {
        static::$playerModel->createOnRequestAIControlled('Human');
    }
    /*********************************** HUMAN PLAYER ***********************************/
    /**
     * should return existing player controlled by Human, as it existed before
     *
     * @see      PlayerModel::createOnRequestHumanControlled
     * @test
     *
     * @depends  isAIControlledOnFlagNone
     * @requires isAIControlledOnFlagAIControlled
     */
    public function createOnRequestHumanControlledOnExistingPlayer()
    {
        $player = static::$playerModel->createOnRequestHumanControlled(LoadPlayerData::TEST_PLAYER_EMAIL, '');

        $this->assertEquals(LoadPlayerData::TEST_PLAYER_EMAIL, $player->getEmail());
        $this->assertFalse(PlayerModel::isAIControlled($player));

        /** because player is already persisted */
        $this->assertNotNull($player->getId());
    }

    /**
     * should return new player controlled by Human, as it didn't exist before
     *
     * @see      PlayerModel::createOnRequestHumanControlled
     * @test
     *
     * @depends  isAIControlledOnFlagNone
     * @requires isAIControlledOnFlagAIControlled
     */
    public function createOnRequestHumanControlledOnNonExistingPlayer()
    {
        $player = static::$playerModel->createOnRequestHumanControlled('NON-EXISTING-HUMAN-PLAYER', '');

        $this->assertEquals('NON-EXISTING-HUMAN-PLAYER', $player->getEmail());
        $this->assertFalse(PlayerModel::isAIControlled($player));

        /** because player is not persisted yet */
        $this->assertNull($player->getId());
    }

    /**
     * should throw exception, because existed Player is not controlled By AI
     *
     * @see      PlayerModel::createOnRequestHumanControlled
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\PlayerException
     *
     * @depends  isAIControlledOnFlagNone
     * @requires isAIControlledOnFlagAIControlled
     */
    public function createOnRequestHumanControlledOnNonExistingAIPlayer()
    {
        static::$playerModel->createOnRequestHumanControlled('CPU', '');
    }
}
