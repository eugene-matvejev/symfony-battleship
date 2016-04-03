<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\CellMockTrait;

/**
 * @see CellModel
 */
class CellModelTest extends ExtendedTestSuite
{
    use CellMockTrait;
    /**
     * @var CellModel
     */
    private $cellModel;

    protected function setUp()
    {
        parent::setUp();
        $this->cellModel = $this->getContainer()->get('battleship.game.services.cell.model');
    }

    /**
     * @see CellModel::STATES_WATER
     * @test
     */
    public function waterStates()
    {
        foreach (CellModel::STATES_WATER as $state) {
            $this->assertContains($state, CellModel::STATES_ALL);
            $this->assertNotContains($state, CellModel::STATES_SHIP);
        }
    }

    /**
     * @see CellModel::STATES_SHIP
     * @test
     */
    public function shipStates()
    {
        foreach (CellModel::STATES_SHIP as $state) {
            $this->assertContains($state, CellModel::STATES_ALL);
            $this->assertNotContains($state, CellModel::STATES_WATER);
        }
    }

    /**
     * @see CellModel::STATES_LIVE
     * @test
     */
    public function liveStates()
    {
        foreach (CellModel::STATES_LIVE as $state) {
            $this->assertContains($state, CellModel::STATES_ALL);
            $this->assertNotContains($state, CellModel::STATES_DIED);
        }
    }

    /**
     * @see CellModel::STATES_DIED
     * @test
     */
    public function diedStates()
    {
        foreach (CellModel::STATES_DIED as $state) {
            $this->assertContains($state, CellModel::STATES_ALL);
            $this->assertNotContains($state, CellModel::STATES_LIVE);
        }
    }

    /**
     * @see CellModel::STATES_ALL
     * @test
     */
    public function allStates()
    {
        $diedStates = count(CellModel::STATES_DIED);
        $liveStates = count(CellModel::STATES_LIVE);
        $totalStates = count(CellModel::STATES_ALL);

        $this->assertGreaterThanOrEqual($diedStates + $liveStates, $totalStates);
    }

    /**
     * @see     CellModel::getAllStates
     * @test
     *
     * @depends waterStates
     * @depends shipStates
     * @depends liveStates
     * @depends diedStates
     * @depends allStates
     */
    public function getAllStates()
    {
        foreach ($this->cellModel->getAllStates() as $state) {
            $this->assertContains($state->getId(), CellModel::STATES_ALL);
        }

        $this->assertEquals(count($this->cellModel->getAllStates()), count(CellModel::STATES_ALL));
    }

    /**
     * @see     CellModel::switchState()
     * @test
     *
     * @depends getAllStates
     */
    public function switchState()
    {
        $this->iterateCellStates(function ($oldStateId, Cell $cell) {
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
        $this->iterateCellStates(function ($oldStateId, Cell $cell) {
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

    private function iterateCellStates(\Closure $function)
    {
        foreach ($this->cellModel->getAllStates() as $state) {
            $cell = $this->getCellMock('A1', $state->getId())->setId($state->getId());

            $function($state->getId(), $cell);
        }
    }
}
