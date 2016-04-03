<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Response\GameTurnResponse;
use EM\GameBundle\Service\GameSystem\GameProcessor;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\GameMockTrait;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\GameResultMockTrait;

/**
 * @see GameProcessor
 */
class GameProcessorTest extends ExtendedTestSuite
{
    use GameMockTrait, GameResultMockTrait;
    /**
     * @var GameProcessor
     */
    private $gameProcessor;

    protected function setUp()
    {
        parent::setUp();
        $this->gameProcessor = $this->getContainer()->get('battleship.game.services.game.processor');
    }

    /**
     * @see GameProcessor::initCPUBattlefieldF
     * @test
     */
    public function initCPUBattlefield()
    {
        $battlefield = $this->getBattlefieldMock();
        $this->invokePrivateMethod(GameProcessor::class, $this->gameProcessor, 'initCPUBattlefield', [$battlefield]);
        $this->assertEquals(CellModel::STATE_SHIP_LIVE, $battlefield->getCellByCoordinate('B2')->getState()->getId());
        $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
        $this->assertCount(100, BattlefieldModel::getLiveCells($battlefield));
    }

    /**
     * @see GameProcessor::processGameInitiation
     *
     * @test
     */
    public function processGameInitiation()
    {
        $json = file_get_contents(__DIR__ . '/../../../Data/request.new.game.7x7.json');

        $game = $this->gameProcessor->processGameInitiation($json);
        $this->assertCount(2, $game->getBattlefields());
        foreach ($game->getBattlefields() as $battlefield) {
            $this->assertCount(49, $battlefield->getCells());
            $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
        }
    }

    /**
     * @see GameProcessor::processGameTurn
     * @test
     */
    public function processGameTurnOnUnfinishedGame()
    {
        $game = $this->getGameMock();
        $shipLiveState = $this->getCellStateMock(CellModel::STATE_SHIP_LIVE);
        foreach ($game->getBattlefields() as $battlefield) {
            $battlefield->getCellByCoordinate('A8')->setState($shipLiveState);
            $battlefield->getCellByCoordinate('A9')->setState($shipLiveState);
            $battlefield->getCellByCoordinate('A10')->setState($shipLiveState);
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

    /**
     * @see     GameProcessor::processPlayerTurn
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     *
     * @depends processGameTurnOnUnfinishedGame
     */
    public function processPlayerTurnThrowsCellException()
    {
        $player = $this->getPlayerMock('', $this->getPlayerTypeMock(PlayerModel::TYPE_CPU));
        $battlefield = $this->getBattlefieldMock();
        $battlefield->setPlayer($player);

        $this->invokeProcessPlayerTurnMethod([$battlefield, 'A0']);
    }

    /**
     * @see     GameProcessor::processPlayerTurn
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\PlayerException
     *
     * @depends processGameTurnOnUnfinishedGame
     */
    public function processPlayerTurnThrowsPlayerException()
    {
        $player = $this->getPlayerMock('', $this->getPlayerTypeMock(-1));
        $battlefield = $this->getBattlefieldMock();
        $battlefield->setPlayer($player);

        $this->invokeProcessPlayerTurnMethod([$battlefield, 'A0']);
    }

    private function invokeProcessPlayerTurnMethod(array $args)
    {
        $this->invokePrivateMethod(GameProcessor::class, $this->gameProcessor, 'processPlayerTurn', $args);
    }
}
