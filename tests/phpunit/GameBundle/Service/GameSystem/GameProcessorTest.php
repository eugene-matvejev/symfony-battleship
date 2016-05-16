<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Service\GameSystem\GameProcessor;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory\Entity\GameResultMockTrait;

/**
 * @see GameProcessor
 */
class GameProcessorTest extends IntegrationTestSuite
{
    use GameResultMockTrait;
    /**
     * @var GameProcessor
     */
    private $gameProcessor;
    /**
     * @var PlayerModel
     */
    private $playerModel;

    protected function setUp()
    {
        $this->gameProcessor = static::$container->get('battleship.game.services.game.processor');
        $this->playerModel = static::$container->get('battleship.game.services.player.model');
    }

    /**
     * should initiate CPU battlefields
     *
     * @see GameProcessor::processCPUBattlefieldsInitiation
     * @test
     */
    public function processCPUBattlefieldsInitiation()
    {
        $game = $this->getGameMock();
        $game->getBattlefields()[0]->setPlayer($this->getAIControlledPlayerMock(''));

        $this->invokeMethod($this->gameProcessor, 'processCPUBattlefieldsInitiation', [$game]);

        foreach ($game->getBattlefields() as $battlefield) {
            if (PlayerModel::isAIControlled($battlefield->getPlayer())) {
                $this->assertEquals(CellModel::FLAG_SHIP, $battlefield->getCellByCoordinate('B2')->getFlags());
                $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
            } else {
                foreach ($battlefield->getCells() as $cell) {
                    $this->assertEquals(CellModel::FLAG_NONE, $cell->getFlags());
                }
            }
        }
    }

    /**
     * should initiate Game with 7x7 with two Battlefields
     *
     * @see GameProcessor::processGameInitiation
     *
     * @test
     */
    public function processGameInitiation()
    {
        $game = $this->gameProcessor->processGameInitiation(static::getSharedFixtureContent('init-game-request-2-players-7x7.json'));

        $this->assertCount(2, $game->getBattlefields());
        foreach ($game->getBattlefields() as $battlefield) {
            $this->assertCount(49, $battlefield->getCells());

            PlayerModel::isAIControlled($battlefield->getPlayer())
                ? $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield))
                : $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
        }
    }

    /**
     * invoke game processing method on Unfinished Game
     *
     * @see GameProcessor::processGameTurn
     * @test
     */
    public function processGameTurnOnUnfinishedGame()
    {
        $game = $this->getGameMock();
        $game->getBattlefields()[0]->setPlayer($this->getAIControlledPlayerMock(''));

        /** because CellModel::changedCells are indexed by Cell Id */
        $i = 0;
        foreach ($game->getBattlefields() as $battlefield) {
            foreach ($battlefield->getCells() as $cell) {
                $cell->setId(++$i);
            }

            $battlefield->getCellByCoordinate('A5')->addFlag(CellModel::FLAG_SHIP);
            $battlefield->getCellByCoordinate('A6')->addFlag(CellModel::FLAG_SHIP);
            $battlefield->getCellByCoordinate('A7')->addFlag(CellModel::FLAG_SHIP);
        }

        foreach ($game->getBattlefields() as $battlefield) {
            $response = $this->gameProcessor->processGameTurn($battlefield->getCellByCoordinate('A1'));

            $this->assertGreaterThanOrEqual(1, count($response->getCells()));
            null !== $game->getResult()
                ? $this->assertInstanceOf(GameResult::class, $response->getResult())
                : $this->assertGreaterThanOrEqual(2, count($response->getCells()));
        }
    }

    /**
     * invoke game processing method to Win Game
     *
     * @see     GameProcessor::processGameTurn
     * @test
     *
     * @depends processGameTurnOnUnfinishedGame
     */
    public function processGameTurnToWin()
    {
        $game = $this->getGameMock();

        /** to make sure CPU will never win from one turn. */
        $game->getBattlefields()[0]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);
        $game->getBattlefields()[0]->getCellByCoordinate('A2')->addFlag(CellModel::FLAG_SHIP);

        $game->getBattlefields()[1]->setPlayer($this->getAIControlledPlayerMock(''));
        $game->getBattlefields()[1]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);

        $cell = $game->getBattlefields()[1]->getCellByCoordinate('A1');

        $response = $this->gameProcessor->processGameTurn($cell);

        $this->assertNotNull($response->getResult());
        $this->assertInstanceOf(GameResult::class, $response->getResult());
    }

    /**
     * invoke game processing method to Win Game
     *
     * @see GameProcessor::processGameTurn
     * @test
     */
    public function processGameTurnOnFinishedGame()
    {
        $game = $this->getGameMock()->setResult($this->getGameResultMock());

        foreach ($game->getBattlefields() as $battlefield) {
            $response = $this->gameProcessor->processGameTurn($battlefield->getCellByCoordinate('A1'));

            $this->assertCount(0, $response->getCells());
            $this->assertInstanceOf(GameResult::class, $response->getResult());
        }
    }
}
