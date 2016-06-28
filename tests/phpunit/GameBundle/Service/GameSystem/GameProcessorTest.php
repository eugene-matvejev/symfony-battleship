<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\GameSystem\GameProcessor;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory;

/**
 * @see GameProcessor
 */
class GameProcessorTest extends IntegrationTestSuite
{
    /**
     * @var GameProcessor
     */
    private static $gameProcessor;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$gameProcessor = static::$container->get('battleship_game.service.game_processor');
    }

    /**
     * invoke game processing method on Unfinished Game
     *
     * @see GameProcessor::processGameTurn
     * @test
     */
    public function processGameTurnOnUnfinishedGame()
    {
        $game = MockFactory::getGameMock();
        $aiBattlefield = $game->getBattlefields()[0];
        $aiBattlefield->setPlayer(MockFactory::getAIPlayerMock(''));

        foreach ($game->getBattlefields() as $battlefield) {
            $battlefield->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);
            $battlefield->getCellByCoordinate('A2')->addFlag(CellModel::FLAG_SHIP);
        }

        $game = static::$gameProcessor->processGameTurn($aiBattlefield->getCellByCoordinate('A1'));

        foreach ($game->getBattlefields() as $battlefield) {
            $this->assertCount(48, BattlefieldModel::getLiveCells($battlefield));
            /** as one cell should be dead */
            $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
        }

        $this->assertTrue($aiBattlefield->getCellByCoordinate('A1')->hasFlag(CellModel::FLAG_DEAD_SHIP));
        $this->assertNull($game->getResult());
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
        $game = MockFactory::getGameMock();

        /** to make sure CPU will never win from one turn. */
        $game->getBattlefields()[0]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);
        $game->getBattlefields()[0]->getCellByCoordinate('A2')->addFlag(CellModel::FLAG_SHIP);

        $game->getBattlefields()[1]->setPlayer(MockFactory::getAIPlayerMock(''));
        $game->getBattlefields()[1]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);

        $game = static::$gameProcessor->processGameTurn($game->getBattlefields()[1]->getCellByCoordinate('A1'));

        $this->assertNotNull($game->getResult());
        $this->assertInstanceOf(GameResult::class, $game->getResult());
    }

    /**
     * invoke game processing method on finished game should throw exception
     *
     * @see GameProcessor::processGameTurn
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\GameProcessorException
     */
    public function processGameTurnOnFinishedGame()
    {
        static::$gameProcessor->processGameTurn(MockFactory::getGameResultMock()->getGame()->getBattlefields()[0]->getCellByCoordinate('A1'));
    }
}
