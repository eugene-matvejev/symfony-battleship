<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Request\GameInitiationRequest;
use EM\GameBundle\Service\GameSystem\GameBuilder;
use EM\Tests\Environment\AbstractKernelTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see GameBuilder
 */
class GameBuilderTest extends AbstractKernelTestSuite
{
    /**
     * @var GameBuilder
     */
    private static $gameBuilder;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$gameBuilder = static::$container->get('battleship_game.service.game_builder');
    }

    /**
     * should:
     *      generate X battlefields of Y size
     *      assign AI controlled player to the generated battlefield
     *      initiate ship cells for the generated battlefield
     *      attach generated battlefield to the Game
     *
     * @see GameBuilder::attachAIBattlefields
     * @test
     */
    public function attachAIBattlefields()
    {
        $game = MockFactory::getGameMock(0, 0);

        $this->invokeMethod(static::$gameBuilder, 'attachAIBattlefields', [$game, 2, 7]);
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
     * @see GameBuilder::buildGame
     * @test
     */
    public function buildGame()
    {
        $request = new GameInitiationRequest($this->getSharedFixtureContent('game-initiation-requests/valid/valid-1-opponent-7x7.json'));

        $game = static::$gameBuilder->buildGame($request, MockFactory::getPlayerMock(''));

        $this->assertCount(2, $game->getBattlefields());
        foreach ($game->getBattlefields() as $battlefield) {
            $this->assertCount(49, $battlefield->getCells());

            $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));
        }
    }
}
