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
     * should initiate Game with 7x7 with two Battlefields
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
        $game->getBattlefields()[0]->setPlayer(MockFactory::getAIPlayerMock(''));

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
        $game = MockFactory::getGameMock();

        /** to make sure CPU will never win from one turn. */
        $game->getBattlefields()[0]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);
        $game->getBattlefields()[0]->getCellByCoordinate('A2')->addFlag(CellModel::FLAG_SHIP);

        $game->getBattlefields()[1]->setPlayer(MockFactory::getAIPlayerMock(''));
        $game->getBattlefields()[1]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);

        $cell = $game->getBattlefields()[1]->getCellByCoordinate('A1');

        $response = $this->gameProcessor->processGameTurn($cell);

        $this->assertNotNull($response->getResult());
        $this->assertInstanceOf(GameResult::class, $response->getResult());
    }

    /**
     * invoke game processing method on unfinished game to Win Game
     *
     * @see GameProcessor::processGameTurn
     * @test
     */
    public function processGameTurnOnFinishedGame()
    {
        $game = MockFactory::getGameMock()->setResult(MockFactory::getGameResultMock());

        foreach ($game->getBattlefields() as $battlefield) {
            $response = $this->gameProcessor->processGameTurn($battlefield->getCellByCoordinate('A1'));

            $this->assertCount(0, $response->getCells());
            $this->assertInstanceOf(GameResult::class, $response->getResult());
        }
    }
}
