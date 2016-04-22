<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\Tests\Environment\ContainerAwareTestSuite;
use EM\Tests\Environment\MockFactory\Entity\CellMockTrait;

/**
 * @see CellModel
 */
class CellModelTest extends ContainerAwareTestSuite
{
    use CellMockTrait;
    /**
     * @var CellModel
     */
    private $cellModel;

    protected function setUp()
    {
        parent::setUp();
        $this->cellModel = static::$container->get('battleship.game.services.cell.model');
    }

    /**
     * @see     CellModel::switchPhase()
     * @test
     */
    public function switchPhase()
    {
        $this->iterateCellMasks(
            [
                CellModel::FLAG_NONE => CellModel::FLAG_DEAD,
                CellModel::FLAG_DEAD => CellModel::FLAG_DEAD,
                CellModel::FLAG_SHIP => CellModel::FLAG_DEAD_SHIP,
                CellModel::FLAG_DEAD_SHIP => CellModel::FLAG_DEAD_SHIP,
                CellModel::FLAG_SKIP => CellModel::FLAG_SKIP
            ],
            function ($cell) {
                return $this->cellModel->switchPhase($cell);
            }
        );
    }

    /**
     * @see     CellModel::switchPhaseToSkipped()
     * @test
     *
     * @depends switchPhase
     */
    public function switchPhaseToCustomState()
    {
        $this->iterateCellMasks(
            [
                CellModel::FLAG_NONE => CellModel::FLAG_SKIP,
                CellModel::FLAG_DEAD => CellModel::FLAG_DEAD,
                CellModel::FLAG_SHIP => (CellModel::FLAG_SKIP | CellModel::FLAG_SHIP),
                CellModel::FLAG_DEAD_SHIP => CellModel::FLAG_DEAD_SHIP,
                CellModel::FLAG_SKIP => CellModel::FLAG_SKIP
            ],
            function ($cell) {
                return $this->cellModel->switchPhase($cell, CellModel::FLAG_SKIP);
            }
        );
    }

    /**
     * @see     CellModel::getChangedCells()
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
     * @param int[]    $masks
     * @param callable $closure
     */
    private function iterateCellMasks(array $masks, callable $closure)
    {
        foreach ($masks as $originalMask => $expectedMask) {
            /** @var Cell $cell */
            $cell = $closure($this->getCellMock('A1')->setFlag($originalMask));

            $this->assertEquals($expectedMask, $cell->getFlag());
        }
    }
}
