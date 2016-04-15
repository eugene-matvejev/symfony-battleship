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
     * @see CellModel::STATES_WATER
     * @test
     */
    public function waterStates()
    {
        $this->iterateCellStates(CellModel::STATES_WATER, CellModel::STATES_SHIP);
    }

    /**
     * @see CellModel::STATES_SHIP
     * @test
     */
    public function shipStates()
    {
        $this->iterateCellStates(CellModel::STATES_SHIP, CellModel::STATES_WATER);
    }

    /**
     * @see CellModel::STATES_LIVE
     * @test
     */
    public function liveStates()
    {
        $this->iterateCellStates(CellModel::STATES_LIVE, CellModel::STATES_DIED);
    }

    /**
     * @see CellModel::STATES_DIED
     * @test
     */
    public function diedStates()
    {
        $this->iterateCellStates(CellModel::STATES_DIED, CellModel::STATES_LIVE);
    }

    /**
     * @param int[] $statesToIterate
     * @param int[] $stateSetShouldNotContainThisState
     */
    private function iterateCellStates(array $statesToIterate, array $stateSetShouldNotContainThisState)
    {
        foreach ($statesToIterate as $state) {
            $this->assertContains($state, CellModel::STATES_ALL);
            $this->assertNotContains($state, $stateSetShouldNotContainThisState);
        }
    }

    /**
     * @see     CellModel::STATE_WATER_SKIP
     * @test
     *
     * @depends waterStates
     * @depends shipStates
     * @depends liveStates
     * @depends diedStates
     */
    public function skipState()
    {
        $this->assertNotContains(CellModel::STATE_WATER_SKIP, CellModel::STATES_LIVE);
        $this->assertNotContains(CellModel::STATE_WATER_SKIP, CellModel::STATES_DIED);
    }

    /**
     * @see     CellModel::STATES_SKIP_STRATEGY_PROCESSING
     * @test
     *
     * @depends skipState
     */
    public function skipStrategyProcessingStates()
    {
        $this->assertCount(2, CellModel::STATES_SKIP_STRATEGY_PROCESSING);
        $this->assertContains(CellModel::STATE_WATER_SKIP, CellModel::STATES_SKIP_STRATEGY_PROCESSING);
        $this->assertContains(CellModel::STATE_WATER_DIED, CellModel::STATES_SKIP_STRATEGY_PROCESSING);
    }

    /**
     * @see     CellModel::STATES_ALL
     * @test
     *
     * @depends skipStrategyProcessingStates
     */
    public function allStates()
    {
        $diedStates = count(CellModel::STATES_DIED);
        $liveStates = count(CellModel::STATES_LIVE);
        $totalActualStates = count(CellModel::STATES_ALL);
        $totalExpectedStates = $diedStates + $liveStates + 1; /* including STATE_WATER_SKIP state */

        $this->assertEquals($totalExpectedStates, $totalActualStates);
    }

    /**
     * @see     CellModel::getAllStates
     * @test
     *
     * @depends allStates
     */
    public function getAllStates()
    {
        $this->assertEquals(count($this->cellModel->getAllStates()), count(CellModel::STATES_ALL));

        foreach ($this->cellModel->getAllStates() as $state) {
            $this->assertContains($state->getId(), CellModel::STATES_ALL);
        }
    }

    /**
     * @see     CellModel::switchState()
     * @test
     *
     * @depends getAllStates
     */
    public function switchState()
    {
        $this->iterateAllCellStatesOnCell(function ($oldStateId, Cell $cell) {
            $this->cellModel->switchState($cell);

            in_array($oldStateId, CellModel::STATES_LIVE)
                ? $this->assertContains($cell->getState()->getId(), CellModel::STATES_DIED)
                : $this->assertEquals($oldStateId, $cell->getState()->getId());
        });
    }

    /**
     * @see     CellModel::switchStateToSkipped()
     * @test
     *
     * @depends switchState
     */
    public function switchStateToSkipped()
    {
        $this->iterateAllCellStatesOnCell(function ($oldStateId, Cell $cell) {
            $this->cellModel->switchStateToSkipped($cell);

            $oldStateId === CellModel::STATE_WATER_LIVE
                ? $this->assertEquals(CellModel::STATE_WATER_SKIP, $cell->getState()->getId())
                : $this->assertNotContains($cell->getState()->getId(), CellModel::STATES_LIVE);
        });
    }

    /**
     * @see     CellModel::getChangedCells()
     * @test
     *
     * @depends switchState
     * @depends switchStateToSkipped
     */
    public function getChangedCells()
    {
        $this->assertContainsOnlyInstancesOf(Cell::class, CellModel::getChangedCells());
        $this->assertGreaterThanOrEqual(2, count(CellModel::getChangedCells()));
    }

    private function iterateAllCellStatesOnCell(callable $function)
    {
        foreach ($this->cellModel->getAllStates() as $state) {
            $cell = $this->getCellMock('A1', $state->getId())->setId($state->getId());

            $function($state->getId(), $cell);
        }
    }
}
