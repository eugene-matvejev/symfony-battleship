<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Request\GameInitiationRequest;
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
    private $gameProcessor;

    protected function setUp()
    {
        $this->gameProcessor = static::$container->get('battleship_game.service.game_processor');
    }

    /**
     * should:
     *      generate X battlefields of Y size
     *      assign AI controlled player to the generated battlefield
     *      initiate ship cells for the generated battlefield
     *      attach generated battlefield to the Game
     *
     * @see GameProcessor::attachAIBattlefields
     * @test
     */
    public function attachAIBattlefields()
    {
        $game = MockFactory::getGameMock(0, 0);

        $this->invokeMethod($this->gameProcessor, 'attachAIBattlefields', [$game, 2, 7]);
        $this->assertCount(2, $game->getBattlefields());

        foreach ($game->getBattlefields() as $battlefield) {
            $this->assertCount(49, $battlefield->getCells());

            $this->assertTrue(PlayerModel::isAIControlled($battlefield->getPlayer()));
            $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));

            foreach ($battlefield->getCells() as $coordinate => $cell) {
                /** all battlefields associated with AI players currently have hardcoded ship into B2 cell */
                $expectedFlag = ('B2' === $coordinate) ? CellModel::FLAG_SHIP : CellModel::FLAG_NONE;
                $this->assertEquals($expectedFlag, $cell->getFlags());
            }
        }
    }

    /**
     * should:
     *      initiate game for player and opponent(s) with specific size
     *      each battlefield should have ships
     *      should have at least one AI controlled opponent
     *
     * @see GameProcessor::buildGame
     * @test
     */
    public function buildGame()
    {
        $request = new GameInitiationRequest(static::getSharedFixtureContent('init-game-request-2-players-7x7.json'));

        $game = $this->gameProcessor->buildGame($request);

        $this->assertCount(2, $game->getBattlefields());
        foreach ($game->getBattlefields() as $battlefield) {
            $this->assertCount(49, $battlefield->getCells());

            $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
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
        $game = MockFactory::getGameMock();
        $aiBattlefield = $game->getBattlefields()[0];
        $aiBattlefield->setPlayer(MockFactory::getAIPlayerMock(''));

        foreach ($game->getBattlefields() as $battlefield) {
            $battlefield->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);
            $battlefield->getCellByCoordinate('A2')->addFlag(CellModel::FLAG_SHIP);
        }

        $game = $this->gameProcessor->processGameTurn($aiBattlefield->getCellByCoordinate('A1'));

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

        $game = $this->gameProcessor->processGameTurn($game->getBattlefields()[1]->getCellByCoordinate('A1'));

        $this->assertNotNull($game->getResult());
        $this->assertInstanceOf(GameResult::class, $game->getResult());
    }

    /**
     * invoke game processing method on finished game should throw exception
     *
     * @see GameProcessor::processGameTurn
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\GameException
     */
    public function processGameTurnOnFinishedGame()
    {
        $this->gameProcessor->processGameTurn(MockFactory::getGameResultMock()->getGame()->getBattlefields()[0]->getCellByCoordinate('A1'));
    }
}
