<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\Tests\Environment\MockFactory\Entity\CellMockTrait;

/**
 * @see CellModel
 */
class CellModelTest extends \PHPUnit_Framework_TestCase
{
    use CellMockTrait;

    /**
     * @see CellModel::switchPhase
     * @test
     */
    public function switchPhase()
    {
        $this->iterateCellFlags(
            [
                CellModel::FLAG_NONE      => CellModel::FLAG_DEAD,
                CellModel::FLAG_DEAD      => CellModel::FLAG_DEAD,
                CellModel::FLAG_SHIP      => CellModel::FLAG_DEAD_SHIP,
                CellModel::FLAG_DEAD_SHIP => CellModel::FLAG_DEAD_SHIP,
                CellModel::FLAG_SKIP      => CellModel::FLAG_SKIP
            ],
            function ($cell) {
                return CellModel::switchPhase($cell);
            }
        );
    }

    /**
     * @see     CellModel::switchPhase
     * @test
     *
     * @depends switchPhase
     */
    public function switchPhaseToCustomState()
    {
        $this->iterateCellFlags(
            [
                CellModel::FLAG_NONE      => CellModel::FLAG_SKIP,
                CellModel::FLAG_DEAD      => CellModel::FLAG_DEAD,
                CellModel::FLAG_SHIP      => (CellModel::FLAG_SKIP | CellModel::FLAG_SHIP),
                CellModel::FLAG_DEAD_SHIP => CellModel::FLAG_DEAD_SHIP,
                CellModel::FLAG_SKIP      => CellModel::FLAG_SKIP
            ],
            function ($cell) {
                return CellModel::switchPhase($cell, CellModel::FLAG_SKIP);
            }
        );
    }

    /**
     * @see     CellModel::getChangedCells
     * @test
     *
     * @depends switchPhase
     * @depends switchPhaseToCustomState
     */
    public function getChangedCells()
    {
        $this->assertContainsOnlyInstancesOf(Cell::class, CellModel::getChangedCells());
        $this->assertGreaterThanOrEqual(1, count(CellModel::getChangedCells()));
    }

    /**
     * @param int[]    $flags
     * @param callable $closure
     */
    private function iterateCellFlags(array $flags, callable $closure)
    {
        foreach ($flags as $originFlag => $expectedFlag) {
            /** @var Cell $cell */
            $cell = $closure($this->getCellMock('A1')->setFlags($originFlag));

            $this->assertEquals($expectedFlag, $cell->getFlags());
        }
    }
}
