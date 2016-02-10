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
            $this->assertContains($state->getId(), CellModel::getAllStates());
        }

        $this->assertEquals(count($this->cellModel->getCellStates()), count(CellModel::getAllStates()));
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getShipStates()
     * @test
     */
    public function getShipStates()
    {
        foreach (CellModel::getShipStates() as $state) {
            $this->assertContains($state, CellModel::getAllStates());
            $this->assertNotContains($state, CellModel::getWaterStates());
        }
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getWaterStates()
     * @test
     */
    public function getWaterStates()
    {
        foreach (CellModel::getWaterStates() as $state) {
            $this->assertContains($state, CellModel::getAllStates());
            $this->assertNotContains($state, CellModel::getShipStates());
        }
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getLiveStates()
     * @test
     */
    public function getLiveStates()
    {
        foreach (CellModel::getLiveStates() as $state) {
            $this->assertContains($state, CellModel::getAllStates());
            $this->assertNotContains($state, CellModel::getDiedStates());
        }
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getDiedStates()
     * @test
     */
    public function getDiedStates()
    {
        foreach (CellModel::getDiedStates() as $state) {
            $this->assertContains($state, CellModel::getAllStates());
            $this->assertNotContains($state, CellModel::getLiveStates());
        }
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getAllStates()
     * @test
     */
    public function getAllStates()
    {
        $diedStates = count(CellModel::getDiedStates());
        $liveStates = count(CellModel::getLiveStates());
        $totalStates = count(CellModel::getAllStates());

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

            if (in_array($stateBefore, CellModel::getLiveStates())) {
                $this->assertContains($cell->getState()->getId(), CellModel::getDiedStates());
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
