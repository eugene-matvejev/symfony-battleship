<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Response\GameTurnResponse;
use EM\GameBundle\Service\GameSystem\GameProcessor;
use EM\Tests\Environment\ContainerAwareTestSuite;
use EM\Tests\Environment\MockFactory\Entity\GameResultMockTrait;

/**
 * @see GameProcessor
 */
class GameProcessorTest extends ContainerAwareTestSuite
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
        parent::setUp();
        $this->gameProcessor = static::$container->get('battleship.game.services.game.processor');
        $this->playerModel = static::$container->get('battleship.game.services.player.model');
    }

    /**
     * @see GameProcessor::processCPUBattlefieldsInitiation
     * @test
     */
    public function processCPUBattlefieldsInitiation()
    {
        $game = $this->getGameMock();
        $game->getBattlefields()[0]->setPlayer($this->getCPUPlayerMock(''));

        $this->invokeNonPublicMethod($this->gameProcessor, 'processCPUBattlefieldsInitiation', [$game]);

        foreach ($game->getBattlefields() as $battlefield) {
            if (PlayerModel::isAIControlled($battlefield->getPlayer())) {
                $this->assertEquals(CellModel::FLAG_SHIP, $battlefield->getCellByCoordinate('B2')->getFlag());
                $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
                $this->assertCount(49, BattlefieldModel::getLiveCells($battlefield));
            } else {
                foreach ($battlefield->getCells() as $cell) {
                    $this->assertEquals(CellModel::FLAG_NONE, $cell->getFlag());
                }
            }
        }
    }

    /**
     * @see GameProcessor::processGameInitiation
     *
     * @test
     */
    public function processGameInitiation()
    {
        $json = file_get_contents(__DIR__ . '/../../../../data/new.game.2.players.7x7.json.request.json');

        $game = $this->gameProcessor->processGameInitiation($json);

        $this->assertCount(2, $game->getBattlefields());
        foreach ($game->getBattlefields() as $battlefield) {
            $this->assertCount(49, $battlefield->getCells());
            if (PlayerModel::isAIControlled($battlefield->getPlayer())) {
                $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield));
            } else {
                $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
            }
        }
    }

    /**
     * @see GameProcessor::processGameTurn
     * @test
     */
    public function processGameTurnOnUnfinishedGame()
    {
        $game = $this->getGameMock();
        $game->getBattlefields()[0]->getPlayer()->setFlag(PlayerModel::FLAG_AI_CONTROLLED);

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

            $this->assertInstanceOf(GameTurnResponse::class, $response);
            if (null !== $game->getResult()) {
                $this->assertGreaterThanOrEqual(1, count($response->getCells()));
                $this->assertNotNull($response->getResult());
                $this->assertInstanceOf(GameResult::class, $response->getResult());
            } else {
                $this->assertGreaterThanOrEqual(2, count($response->getCells()));
            }
        }
    }

    /**
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

        $game->getBattlefields()[1]->setPlayer($this->getCPUPlayerMock(''));
        $game->getBattlefields()[1]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);

        $cell = $game->getBattlefields()[1]->getCellByCoordinate('A1');

        $response = $this->gameProcessor->processGameTurn($cell);

        $this->assertNotNull($response->getResult());
        $this->assertInstanceOf(GameResult::class, $response->getResult());
    }

    /**
     * @see GameProcessor::processGameTurn
     * @test
     */
    public function processGameTurnOnFinishedGame()
    {
        $game = $this->getGameMock()->setResult($this->getGameResultMock());

        foreach ($game->getBattlefields() as $battlefield) {
            $response = $this->gameProcessor->processGameTurn($battlefield->getCellByCoordinate('A1'));

            $this->assertEquals(0, count($response->getCells()));
            $this->assertNotNull($response->getResult());
            $this->assertInstanceOf(GameResult::class, $response->getResult());
        }
    }

//    /**
//     * @see     GameProcessor::processPlayerTurn
//     * @test
//     *
//     * @depends processGameTurnOnUnfinishedGame
//     */
//    public function processPlayerTurnThrowsCellException()
//    {
//        $battlefield = $this->getBattlefieldMock()
//            ->setPlayer($this->getCPUPlayerMock(''));
//
//        $this->invokeProcessPlayerTurnMethod(
//            [
//                $battlefield->getPlayer(),
//                $battlefield,
//                $battlefield->getCellByCoordinate('A1')
//            ]
//        );
//        $this->assertTrue($battlefield->getCellByCoordinate('A1')->hasFlag(CellModel::FLAG_DEAD));
//    }

//    /**
//     * @see     GameProcessor::processPlayerTurn
//     * @test
//     *
//     * @expectedException \EM\GameBundle\Exception\PlayerException
//     *
//     * @depends processGameTurnOnUnfinishedGame
//     */
//    public function processPlayerTurnThrowsPlayerException()
//    {
//        $battlefield = $this->getBattlefieldMock()
//            ->setPlayer($this->getPlayerMock('', $this->getPlayerTypeMock(-1)));
//
//        $this->invokeProcessPlayerTurnMethod([$battlefield, $battlefield->getCellByCoordinate('A1')]);
//    }

    private function invokeProcessPlayerTurnMethod(array $args)
    {
        $this->invokeNonPublicMethod($this->gameProcessor, 'processPlayerTurn', $args);
    }
}
