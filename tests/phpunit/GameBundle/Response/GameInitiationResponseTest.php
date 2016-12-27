<?php
namespace EM\Tests\PHPUnit\GameBundle\Response;

use EM\GameBundle\Model\CellModel;
use EM\FoundationBundle\Model\UserModel;
use EM\GameBundle\Response\GameInitiationResponse;
use EM\Tests\Environment\Factory\MockFactory;

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
        $game->getBattlefields()[0]
            ->setUser(MockFactory::getAIUserMock(''))
            ->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_SHIP);

        $request = new GameInitiationResponse($game->getBattlefields());
        foreach ($request->getBattlefields() as $battlefield) {
            foreach ($battlefield->getCells() as $cell) {
                if (UserModel::isAIControlled($battlefield->getUser())) {
                    $this->assertEquals(CellModel::FLAG_NONE, $cell->getFlags());
                }
            }
        }
    }
}
