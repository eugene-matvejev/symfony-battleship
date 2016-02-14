<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\CellModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

/**
 * @see EM\GameBundle\Model\CellModel
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
     * @see EM\GameBundle\Model\CellModel::getCellStates()
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
     * @see EM\GameBundle\Model\CellModel::STATES_WATER
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
     * @see EM\GameBundle\Model\CellModel::STATES_SHIP
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
     * @see EM\GameBundle\Model\CellModel::STATES_LIVE
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
     * @see EM\GameBundle\Model\CellModel::STATES_DIED
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
     * @see EM\GameBundle\Model\CellModel::STATES_ALL
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
     * @see EM\GameBundle\Model\CellModel::switchState()
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
     * @see EM\GameBundle\Model\CellModel::switchStateToSkipped()
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

    private function getMockedCell(CellState $state) : Cell
    {
        $cell = (new Cell())
            ->setState($state);

        return $cell;
    }
}
