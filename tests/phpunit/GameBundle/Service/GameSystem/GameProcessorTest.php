<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\GameResult;
use EM\FoundationBundle\Entity\Player;
use EM\GameBundle\Exception\GameProcessorException;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\GameSystem\GameProcessor;
use EM\Tests\Environment\AbstractKernelTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see GameProcessor
 */
class GameProcessorTest extends AbstractKernelTestSuite
{
    /**
     * @var GameProcessor
     */
    private static $gameProcessor;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$gameProcessor = static::$container->get('em.game_bundle.service.game_processor');
    }

    /**
     * @see GameProcessor::processPlayerTurnOnBattlefield
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\GameProcessorException
     */
    public function processPlayerTurnOnBattlefieldExpectedExceptionOnOwnPlayerBattlefield()
    {
        $battlefield = MockFactory::getBattlefieldMock();

        $this->processPlayerTurnOnBattlefield($battlefield, $battlefield->getPlayer());
    }

    /**
     * @see GameProcessor::processPlayerTurnOnBattlefield
     * @test
     */
    public function processPlayerTurnOnBattlefieldOnNotWin()
    {
        $battlefield = MockFactory::getBattlefieldMock()
            ->setPlayer(MockFactory::getAIPlayerMock(''));
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_SHIP);
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_SHIP);

        $this->assertFalse($this->processPlayerTurnOnBattlefield($battlefield, MockFactory::getPlayerMock('')));
    }

    /**
     * @see GameProcessor::processPlayerTurnOnBattlefield
     * @test
     */
    public function processPlayerTurnOnBattlefieldToWin()
    {
        $battlefield = MockFactory::getBattlefieldMock()
            ->setPlayer(MockFactory::getAIPlayerMock(''));
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_SHIP);

        $this->assertTrue($this->processPlayerTurnOnBattlefield($battlefield, MockFactory::getPlayerMock('')));
    }

    /**
     * @see GameProcessor::processPlayerTurnOnBattlefield
     *
     * @param Battlefield $battlefield
     * @param Player      $attacker
     *
     * @return bool
     * @throws GameProcessorException
     */
    private function processPlayerTurnOnBattlefield(Battlefield $battlefield, Player $attacker)
    {
        return $this->invokeMethod(
            static::$gameProcessor,
            'processPlayerTurnOnBattlefield',
            [$battlefield, $attacker, $battlefield->getCellByCoordinate('A1')]
        );
    }

    /**
     * invoke game processing method on finished game should throw exception
     *
     * @see GameProcessor::processTurn
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\GameProcessorException
     */
    public function processTurnOnFinishedGame()
    {
        static::$gameProcessor->processTurn(MockFactory::getGameResultMock()->getGame()->getBattlefields()[0]->getCellByCoordinate('A1'));
    }

    /**
     * invoke game processing method on Unfinished Game
     *
     * @see     GameProcessor::processTurn
     * @test
     *
     * @depends processTurnOnFinishedGame
     */
    public function processTurnToNotWin()
    {
        $game          = MockFactory::getGameMock();
        $aiBattlefield = $game->getBattlefields()[0];
        $aiBattlefield->setPlayer(MockFactory::getAIPlayerMock(''));

        foreach ($game->getBattlefields() as $battlefield) {
            $battlefield->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);
            $battlefield->getCellByCoordinate('A2')->addFlag(CellModel::FLAG_SHIP);
        }

        $game = static::$gameProcessor->processTurn($aiBattlefield->getCellByCoordinate('A1'));

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
     * @see     GameProcessor::processTurn
     * @test
     *
     * @depends processTurnToNotWin
     */
    public function processTurnToWin()
    {
        $game = MockFactory::getGameMock();

        /** to make sure CPU will never win from one turn. */
        $game->getBattlefields()[0]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);
        $game->getBattlefields()[0]->getCellByCoordinate('A2')->addFlag(CellModel::FLAG_SHIP);

        $game->getBattlefields()[1]->setPlayer(MockFactory::getAIPlayerMock(''));
        $game->getBattlefields()[1]->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);

        $game = static::$gameProcessor->processTurn($game->getBattlefields()[1]->getCellByCoordinate('A1'));

        $this->assertNotNull($game->getResult());
        $this->assertInstanceOf(GameResult::class, $game->getResult());
    }
}
