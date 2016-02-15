<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\CellModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

/**
 * @see CellModel
 */
class CellModelTest extends ExtendedTestCase
{
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
     * @see CellModel::getCellStates()
     * @test
     */
    public function getCellStates()
    {
        foreach ($this->cellModel->getCellStates() as $state) {
            $this->assertContains($state->getId(), CellModel::STATES_ALL);
        }

        $this->assertEquals(count($this->cellModel->getCellStates()), count(CellModel::STATES_ALL));
    }

    /**
     * @see CellModel::STATES_WATER
     * @test
     */
    public function getWaterStates()
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
    public function getShipStates()
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
    public function getLiveStates()
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
    public function getDiedStates()
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
    public function getAllStates()
    {
        $diedStates = count(CellModel::STATES_DIED);
        $liveStates = count(CellModel::STATES_LIVE);
        $totalStates = count(CellModel::STATES_ALL);

        $this->assertGreaterThanOrEqual($diedStates + $liveStates, $totalStates);
    }


    /**
     * @see CellModel::switchState()
     * @test
     */
    public function switchState()
    {
        foreach ($this->cellModel->getCellStates() as $cellState) {
            $stateBefore = $cellState->getId();
            $cell = $this->getMockedCell($cellState);
            $this->cellModel->switchState($cell);

            if (in_array($stateBefore, CellModel::STATES_LIVE)) {
                $this->assertContains($cell->getState()->getId(), CellModel::STATES_DIED);
            } else {
                $this->assertEquals($stateBefore, $cell->getState()->getId());
            }
        }
    }

    /**
     * @see CellModel::switchStateToSkipped()
     * @test
     */
    public function switchStateToSkipped()
    {
        foreach ($this->cellModel->getCellStates() as $cellState) {
            $stateBefore = $cellState->getId();
            $cell = $this->getMockedCell($cellState);
            $this->cellModel->switchStateToSkipped($cell);

            if ($stateBefore === CellModel::STATE_WATER_LIVE) {
                $this->assertEquals(CellModel::STATE_WATER_SKIP, $cell->getState()->getId());
            } else {
                $this->assertEquals($stateBefore, $cell->getState()->getId());
            }
        }
    }

    /**
     * @param CellState $state
     *
     * @return Cell
     *
     * @coversNothing
     */
    private function getMockedCell(CellState $state) : Cell
    {
        $cell = (new Cell())
            ->setState($state);

        return $cell;
    }
}
