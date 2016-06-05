<?php
namespace EM\Tests\PHPUnit\GameBundle\Response;

use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Response\GameInitiationResponse;
use EM\Tests\Environment\MockFactory;

/**
 * @see GameInitiationResponse
 */
class GameInitiationResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see GameInitiationResponse::addBattlefield()
     * @test
     */
    public function addBattlefield()
    {
        $game = MockFactory::getGameMock();
        $cpuPlayerBattlefield = $game->getBattlefields()[0];
        $cpuPlayerBattlefield
            ->setPlayer(MockFactory::getAIPlayerMock(''))
            ->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_SHIP);

        $request = new GameInitiationResponse($game->getBattlefields());
        foreach ($request->getBattlefields() as $battlefield) {
            foreach ($battlefield->getCells() as $cell) {
                if (PlayerModel::isAIControlled($battlefield->getPlayer())) {
                    $this->assertEquals(CellModel::FLAG_NONE, $cell->getFlags());
                }
            }
        }
    }
}
