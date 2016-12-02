<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\DataFixtures\ORM\LoadPlayerData;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Model\PlayerModel;
use EM\Tests\Environment\AbstractKernelTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see PlayerModel
 */
class PlayerModelTest extends AbstractKernelTestSuite
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

    public function isAIControlledDataProvider() : array
    {
        return [
            [false, MockFactory::getPlayerMock('')],
            [true, MockFactory::getAIPlayerMock('')]
        ];
    }

    /**
     * should return true if player marked by @see PlayerModel::FLAG_AI_CONTROLLED flag otherwise false
     *
     * @see          PlayerModel::isAIControlled
     * @test
     *
     * @dataProvider isAIControlledDataProvider
     *
     * @param bool   $result
     * @param Player $player
     */
    public function isAIControlled(bool $result, Player $player)
    {
        $this->assertSame($result, PlayerModel::isAIControlled($player));
    }

    public function createOnRequestAIControlledDataProvider() : array
    {
        return [
            [LoadPlayerData::TEST_AI_PLAYER_EMAIL, 'int'],
            [LoadPlayerData::TEST_AI_PLAYER_EMAIL . 'NON-EXISTS', 'null']
        ];
    }

    /**
     * should return new player controlled by AI, as it didn't exist before
     *
     * @see          PlayerModel::createOnRequestAIControlled
     * @test
     *
     * @dataProvider createOnRequestAIControlledDataProvider
     *
     * @param string $username
     * @param string $idFieldType
     */
    public function createOnRequestAIControlled(string $username, string $idFieldType)
    {
        $player = static::$playerModel->createOnRequestAIControlled($username);

        $this->assertInternalType($idFieldType, $player->getId());
        $this->assertTrue(PlayerModel::isAIControlled($player));
        $this->assertSame($username, $player->getEmail());
    }

    public function createPlayerDataProvider(): array
    {
        return [
            ['AI controlled', '', PlayerModel::FLAG_AI_CONTROLLED],
            ['human controlled', '', PlayerModel::FLAG_NONE]
        ];
    }

    /**
     * @see          PlayerModel::createOnRequestHumanControlled
     * @test
     *
     * @dataProvider createPlayerDataProvider
     *
     * @param string $username
     * @param string $password
     * @param int    $flag
     */
    public function createPlayer(string $username, string $password, int $flag)
    {
        $player = static::$playerModel->createPlayer($username, $password, $flag);

        /** because player is not persisted yet */
        $this->assertNull($player->getId());
        $this->assertSame($username, $player->getEmail());
        $this->assertSame($flag, $player->getFlags());
    }
}
