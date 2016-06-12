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
     * @see GameTurnResponse::setCells()
     * @test
     */
    public function setCells()
    {
        $response = new GameTurnResponse();
        $cells = [
            'A1' => MockFactory::getCellMock('A1'),
            'A2' => MockFactory::getCellMock('A2')
        ];
        $response->setCells($cells);

        foreach ($response->getCells() as $index => $cell) {
            $this->assertInternalType('int', $index);
            $this->assertInstanceOf(Cell::class, $cell);
        }
    }
}
