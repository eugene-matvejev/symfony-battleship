<?php
namespace EM\Tests\PHPUnit\GameBundle\Response;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Response\GameTurnResponse;
use EM\Tests\Environment\MockFactory;

/**
 * @see GameTurnResponse
 */
class GameTurnResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see GameTurnResponse::setCells
     * @test
     */
    public function setCellsOnConstruct()
    {
        $cells = [
            'A1' => MockFactory::getCellMock('A1'),
            'A2' => MockFactory::getCellMock('A2')
        ];

        $this->iterateResponseCells(new GameTurnResponse(MockFactory::getGameMock(), $cells));
    }

    /**
     * @see GameTurnResponse::setCells
     * @test
     */
    public function setCellsOnExternalCall()
    {
        $cells = [
            'A1' => MockFactory::getCellMock('A1'),
            'A2' => MockFactory::getCellMock('A2')
        ];

        $response = new GameTurnResponse(MockFactory::getGameMock(), []);
        $response->setCells($cells);

        $this->iterateResponseCells($response);
    }

    private function iterateResponseCells(GameTurnResponse $response)
    {
        foreach ($response->getCells() as $index => $cell) {
            $this->assertInternalType('int', $index);
            $this->assertInstanceOf(Cell::class, $cell);
        }
    }

    /**
     * @see GameTurnResponse::setCells
     * @test
     */
    public function setGameResultOnConstruct()
    {
        $gameResult = MockFactory::getGameResultMock();
        $response = new GameTurnResponse($gameResult->getGame(), []);

        $this->assertSame($gameResult, $response->getResult());
    }
}
