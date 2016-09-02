<?php
namespace EM\Tests\PHPUnit\GameBundle\Response;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Response\GameTurnResponse;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see GameTurnResponse
 */
class GameTurnResponseTest extends \PHPUnit_Framework_TestCase
{
    public function setCellsProvider() : array
    {
        return [
            [[MockFactory::getCellMock('A1'), MockFactory::getCellMock('A2')]]
        ];
    }

    /**
     * @see          GameTurnResponse::setCells
     * @test
     *
     * @dataProvider setCellsProvider
     *
     * @param Cell[] $cells
     */
    public function setCellsOnConstruct(array $cells)
    {
        $this->iterateResponseCells(new GameTurnResponse(MockFactory::getGameMock(), $cells));
    }

    /**
     * @see          GameTurnResponse::setCells
     * @test
     *
     * @dataProvider setCellsProvider
     *
     * @param Cell[] $cells
     */
    public function setCellsOnExternalCall(array $cells)
    {
        $response = new GameTurnResponse(MockFactory::getGameMock(), []);
        $response->setCells($cells);

        $this->iterateResponseCells($response);
    }

    /**
     * because JMS serializer has a bug, array of cells should be indexed via numbers only
     *
     * @param GameTurnResponse $response
     */
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
        $response   = new GameTurnResponse($gameResult->getGame(), []);

        $this->assertSame($gameResult, $response->getResult());
    }
}
